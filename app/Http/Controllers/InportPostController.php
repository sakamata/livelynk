<?php

namespace App\Http\Controllers;

use DB;
use App\CommunityUser;
use App\Http\Middleware\VerifyCsrfToken;
use App\Service\CommunityService;
use App\Service\CommunityUserService;
use App\Service\MacAddressService;
use App\Service\MacAddress;
use App\Service\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class InportPostController extends Controller
{
    private $call_community;
    private $call_community_user;
    private $call_mac;
    private $call_user;

    public function __construct(
        CommunityService $call_community,
        CommunityUserService $call_community_user,
        MacAddressService $call_mac,
        UserService $call_user
        )
    {
        $this->call_community      = $call_community;
        $this->call_community_user = $call_community_user;
        $this->call_mac            = $call_mac;
        $this->call_user           = $call_user;
    }

    // MAC アドレス一覧を受け取って、mac_addresses tableへの登録、更新を行う
    public function MacAddress(Request $request)
    {
        $check_array  = $this->JsonValueCheck($request->json);
        // POSTされた community_id の**半角英数字**から communities table の id を導く
        $community = $this->call_community->NameGet((string)$check_array['community_id']);

        if (!$community) {
            Log::debug(print_r('community_id not found!! check json ==> ', 1));
            Log::debug(print_r($check_array, 1));
            exit();
        }
        $post_mac_array  = $this->CheckMACArray((array)$check_array["mac"]);
        // あえて宣言する変数名 jsonの ['community_id'] が半角英数で名前が紛らわしい為
        $community_id_int = $community->id;
        // DBにあるPOST前のMACaddressを取得
        $stays_macs =$this->call_community_user->GetStaysMacs((int)$community_id_int);
        // クエリビルダで取得したオブジェクトを配列に変換
        $stays_mac_array = json_decode(json_encode($stays_macs), true);
        $now = Carbon::now();
        $push_users =array();
        $users_ids = array();
        $google_talk_trigger = null;
        $i = 0;
        $v = 0;
        // POSTされたMACaddressを個々で精査し来訪判断
        foreach ((array)$post_mac_array as $post_mac) {
            // 登録済みMACアドレスか個別確認
            // MACAddress形式の場合はhash化させる
            if (preg_match('/^([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F])$/', $post_mac)) {
                $post_mac_hash = $this->CahngeCrypt($post_mac, $check_array['router_id']);
            } else {
                $post_mac_hash = $post_mac;
            }
            // return bool
            $check = $this->call_community_user->NewComerCheck((int)$community_id_int, (string)$post_mac_hash);
            if (!$check) {
                // 未登録なら、仮ユーザーの登録と端末の insert 滞在中に変更
                $provisional_name = $this->call_user->ProvisionalNameMaker();
                DB::beginTransaction();
                try {
                    // 仮ユーザーの作成
                    $community_user_id  = $this->call_user->UserCreate(
                        (string)$provisional_name, // name
                        (string)$provisional_name, // unique_name
                        (string)$email = null,
                        (bool)$provisional = true,
                        (string)$provisional_name, // password
                        (int)$community_id_int,
                        (int)$role_id = 1, //normal
                        (string)$action = 'InportPostProvisional'
                    );
                    // 仮ユーザーに紐づけてMACAddressの登録
                    $param = [
                        'community_user_id' => $community_user_id,
                        'router_id' => $check_array["router_id"],
                        'mac_address' => $post_mac,
                        'mac_address_hash' => $post_mac_hash,
                        'vendor' => $check_array["vendor"][$v],
                        'arraival_at' => $now,
                        'posted_at' => $now,
                        'current_stay' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    DB::table('mac_addresses')->insert($param);
                    DB::commit();
                    $success = true;
                } catch (\Exception $e) {
                    $success = false;
                    DB::rollback();
                }
                if (!$success) {
                    // コケた際はひとまずlog出力
                    Log::waning(print_r('Provisional User & Device Create Error!!', 1));
                    continue;
                }
                // この宣言は外してはいけない！！
                // MacAddressStatusUpdate 第一引数に必要な宣言
                $community_user_id = $community->user_id;
                $google_talk_trigger = 'new_comer'; 
                // 新規訪問者通知へのpush
                $person = array(
                    "id" => "id未定",
                    "name" => "newcomer! ". $provisional_name,
                    "name_only" => $provisional_name,
                 );
                $push_users[$i] = $person;
                $i++;
            } else {
                // 登録済みMACaddressの場合
                // last_accessの更新をするuserのid一覧を取得
                $mac_record = $this->call_community_user->GetLastAccsessUpdateMacAddress(
                    (int)$community->id,
                    (string)$post_mac_hash
                );
                if (!$mac_record) { continue; }
                $community_user_id = $mac_record->id;
                $users_ids[] = $community_user_id;
                // 前回のPOSTに該当MACaddressがない場合
                if (!in_array($post_mac, $stays_mac_array)) {
                    // 来訪/継続ステータスの変更、通知の判断、通知の値取得を行う

                    // 前回帰宅時間から、ルーター瞬断や中座に対応した通知処理を行う
                    $departure_at = new Carbon($mac_record->departure_at);
                    $second = env("JUDGE_ARRAIVAL_INTERVAL_SECOND");
                    // 初来訪機器で、帰宅時間がない場合は limit は現在となる
                    $limit = $departure_at->addSecond($second);
                    // 既にこのuserの他のデバイスの存在があるか?
                    $stay = $this->call_mac->ThisUserExists((int)$community_user_id);

                    // 一定時間以上間の空いた場合はDBのステータス滞在中に変更 arraival_at のみ変更
                    // 現在を含むので ">="  が正
                    if ($now >= $limit) {
                        //  該当レコードの来訪時間 arraival_at 更新
                        $this->call_mac->Arraival_at_Update(
                            (int)$community_user_id,
                            (string)$post_mac_hash,
                            (int)$check_array["router_id"],
                            (string)$now
                        );
                    }
                    // 他のデバイスが無く、かつ不在から一定時間以上だった場合のみ
                    //  push_usersに追加
                    if (!$stay && $now >= $limit) {
                        //  通知の為のuser nameを取得
                        // user_id name hide を取得 (非表示ユーザー除外)
                        $user = $this->call_community_user->GetPushUser((int) $community_user_id);

                        if ($user) {
                            Log::debug(print_r("来訪者の通知開始 該当user>>>", 1));
                            Log::debug(print_r($user->name, 1));
                            // ***ToDo*** 重複削除をする処理を追加する 1ユーザーの複数端末が同時到着の際、重複して名前が飛ぶ。
                            $person = array(
                                "id" => $user->user_id,
                                "name" => $user->name,
                                "name_only" => $user->name,
                            );
                            $push_users[$i] =  $person;
                            $i++;
                        }
                    }
                }
            }
            // 登録済みの場合、通知判定を終えた後、テータスを更新する
            // 非表示デバイスの情報はMACが飛んでいても更新さない
            $this->call_mac->MacAddressStatusUpdate(
                (int)$community_user_id,
                (string)$post_mac_hash,
                (int)$check_array["router_id"],
                (string)$now
            );
            $v++;
        } // end foreach

        // 滞在者の id重複削除してからuser table last_accessを更新
        $users_ids = array_unique($users_ids);
        $users_ids = array_values($users_ids);
        $this->user_last_access_update($users_ids, $now);
        // 外部機能IFTTTに来訪通知をPOST
        if ($push_users) {
            Log::debug(print_r("!push_ifttt arraival!>>>>", 1));
            Log::debug(print_r($push_users, 1));
            (new ExportPostController)->push_ifttt($push_users, $category = "arraival", $community->id);
            if ($google_talk_trigger == null) {
                $google_talk_trigger = 'users_arraival';
            }
        }
        // !!!Tips!!! 来訪直後に帰宅通知が出るのは .env ファイルのキャッシュの問題かも
        // .env 値をlog出力して値が反映されるか確認 
        // 無い場合はコンソールで以下のいずれかのコマンドをたたく事
        // $ php artisan config:cache
        // $ php artisan config:clear
        $this->DepartureCheck($community->id);
        if ($google_talk_trigger && $community->google_home_enable == true) {
            // GoogleHomeへのコマンドを記載する
            $set = (new GoogleHomeController)->GetGoogleHomeTalk($google_talk_trigger, $community, $push_users);
            Log::debug(print_r($set, 1));
            return response()->json([
                'status' => 'From Livelynk posted',
                'MAC' => $set['MAC'],
                'name' => $set['name'],
                'message' => $set['message'],
            ]);
        }
    }

    public function JsonValueCheck($json)
    {
        if (!$json) { exit(); };
        $check_array = json_decode($json, true);
        Log::debug(print_r($check_array, 1));
        // hash値が異なる場合はexit() で処理停止
        $this->HashCheck($check_array);
        if (!ctype_digit($check_array['time']) &&
            !ctype_digit($check_array['router_id']) &&
            !ctype_digit($check_array['community_id'])) {
            Log::debug(print_r('json int value not integer!! check json ==> ', 1));
            Log::debug(print_r($check_array, 1));
            exit();
        }
        return $check_array;
    }

    public function HashCheck($check_array)
    {
        // hash確認 router_id が数値以外なら処理停止
        if (!is_numeric($check_array["router_id"])) {
            Log::debug(print_r('Inport json post router_id not integer!! posted router_id ==> ' .$check_array["router_id"], 1));
            exit();
        } else {
            $router_id = $check_array["router_id"];
        }
        $secret = 'App\Router'::Join('communities', 'routers.community_id', '=', 'communities.id')
            ->where('routers.id', $router_id)->pluck('hash_key')->first();
        $time = $check_array["time"];
        $this_side_hash = hash('sha256',$time.$secret);
        $post_hash = $check_array["hash"];
        if ($this_side_hash != $post_hash) {
            Log::debug(print_r('Inport json post hash unmatch !! posted hash ==> ' .$post_hash, 1));
            Log::debug(print_r('Inport json post hash unmatch !! This side hash ==> ' .$this_side_hash, 1));
            exit();
        }
    }

    public function CheckMACArray(array $check_array_mac)
    {
        // MACAddressの値をPOSTされた形式別で配列処理する
        $post_mac_array = array();
        foreach ((array)$check_array_mac as $check) {
            // MACアドレス形式は大文字にして配列に入る
            if (preg_match('/^([0-9a-fA-F][0-9a-fA-F]:){5}([0-9a-fA-F][0-9a-fA-F])$/', $check)) {
                $check_MAC = strtoupper($check);
                array_push($post_mac_array, $check_MAC);
            // hash化された値であれば配列に入れる
            } elseif (preg_match('/[0-9a-f]{64}/', $check)) {
                array_push($post_mac_array, $check);
            } else {
                Log::debug(print_r('Inport post MACAddress not pattern!! posted element ==> ' . $check, 1));
                exit();
            }
        }
        return $post_mac_array;
    }

    public function CahngeCrypt($mac_address, $router_id)
    {
        $secret = 'App\Router'::Join('communities', 'routers.community_id', '=', 'communities.id')
            ->where('routers.id', $router_id)->pluck('hash_key')->first();
        return hash('sha256', $mac_address . $secret);
    }

    // users table last_accessの一括更新
    public function user_last_access_update($users_ids, $now)
    {
        foreach ($users_ids as $id) {
            DB::table('communities_users_statuses')->where('id', $id)
            ->update(['last_access' => $now,]);
        }
    }

    // ***ToDo*** $community 引数を排除、中で別メソッドから $community を取得させ独立した処理にさせる。さらにタイマー、またはPOSTの止まったコミュニティを探して呼び出し、コミュニティ毎に定期的に確認する処理に変更
    // public function DepartureCheck()
    public function DepartureCheck($community_id)
    {
        // 一定時間アクセスの無いmac_address を不在に変更
        // last_access が 一定時間以上になった全ての current_stay true を false にする
        // ***ToDo*** 同時刻に人感センサー有りなら、帰宅確度を上げる処理を追加
        $now = Carbon::now();
        $second = env("JUDGE_DEPARTURE_INTERVAL_SECOND");
        $past_limit = $now->subSecond($second);

        // 帰宅処理が必要なIDを抽出
            // 端末の current_stay => false
            // user を特定、名前を取得して通知にpush
        $went_away = DB::table('mac_addresses')
        ->select('mac_addresses.id', 'community_user_id', 'user_id')
        ->leftJoin('community_user', 'community_user.id', '=', 'mac_addresses.community_user_id')
        ->where([
            ['community_id', $community_id],
            ['hide', false],
            ['current_stay', true],
            ['posted_at', '<=', $past_limit],
        ])->get();
        if (!$went_away) {
            // log::debug(print_r('$went_away 帰宅判断対象無し、処理停止',1));
            // exit();
            return;
        }

        // 帰宅判断以上の時間が経過した端末の current_stay やステータスを変更
        $users_id = array();
        $i = 0;
        foreach ($went_away as $went) {
            // $went->id == mac_addresses id
            DB::table('mac_addresses')->where('id', $went->id)
                ->update([
                    'departure_at' => $now,
                    'current_stay' => false,
                    'updated_at' => $now,
            ]);
            $users_id[$i] = $went->user_id;
            $i++;
        }

        // $users_id == 帰宅可能性のある user_id (users の id カラム)
        $users_id = array_unique($users_id);
        $near_push_users_id = array();
        $i = 0;
        foreach ((array)$users_id as $user_id) {
            // push通知が必要な user 選定処理、非表示 user を除外
            // $near_push_users_id に user_id の配列として加工
            $user = DB::table('community_user')
                ->select('community_user.id')
                ->Join('communities_users_statuses', 'communities_users_statuses.id', '=', 'community_user.id')
                ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
                ->where([
                    ['community_id', $community_id],
                    ['user_id', $user_id],
                    ['hide', false],
            ])->first();
            $near_push_users_id[$i] = $user->id;
            $i++;

            // 非表示userは処理せず
            if (!$user) {
                Log::debug(print_r('非表示userの為、帰宅通知をしないuserのid>>> ' . $user_id, 1));
                continue;
            }
        }

        if (!$near_push_users_id) {
            // log::debug(print_r('$near_push_users_id 帰宅判断対象無し、処理停止',1));
            // exit();
            return;
        }

        // 該当userの非表示以外の滞在中mac_addressが無いか確認
        $no_push_user_id = array();
        $i = 0;
        foreach ($near_push_users_id as $id) {
            $exist = DB::table('mac_addresses')->where([
                ['community_user_id', $id],
                ['hide', false],
                ['posted_at', '>', $past_limit],
            ])->exists();
            if ($exist) {
                $no_push_user_id[$i] = $went->community_user_id;
                $i++;
            }
        }

        // $no_push_user_id を通知から外す
        $push_users_id = array_diff($near_push_users_id, $no_push_user_id);
        if (!$push_users_id) {
            // log::debug(print_r('$push_users_id 帰宅判断対象無し、処理停止',1));
            // exit();
            return;
        }

        $push_users_obj = DB::table('community_user')
            ->select('user_id', 'name')
            ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
            ->whereIn('community_user.id', $push_users_id)
        ->get();

        // 通知へのデータ加工
        $push_users = array();
        $i = 0;
        foreach ($push_users_obj as $user) {
            $person = array(
                "id" => $user->user_id,
                "name" => $user->name,
            );
            Log::debug(print_r("帰ったステータス更新 判定user>>>>", 1));
            Log::debug(print_r($user->user_id .' '. $user->name, 1));
            $push_users[$i] =  $person;
            $i++;
        }

        // 滞在者数判断処理～外部機能IFTTTに帰宅通知をPOST
        if ($push_users) {
            Log::debug(print_r("!!!push_ifttt departure pushusers >>>>!!!", 1));
            Log::debug(print_r($push_users, 1));

            (new ExportPostController)->push_ifttt($push_users, $category = "departure", $community_id);
        }
    }   // end function
}
