<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TumolinkTest extends TestCase
{

    // 行くツモリpostの値を検証する
    // tumolink_に非ログインでPOSTすると302が返る

    /**
     * @test
     */
    public function 現在のツモリスト取得のGETで200が返る()
    {
        $response = $this->get('tumolink/tumolist');
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function 現在のツモリスト取得のGETでJSONが返る()
    {
        $response = $this->get('tumolink/tumolist');
        $this->assertThat($response->content(), $this->isJson());
    }

    /**
     * @test
     */
    public function 現在のツモリスト取得のGETでstatus_OKが返る()
    {
        $response = $this->get('tumolink/tumolist');
        $response->assertJsonFragment(['status'=> 'OK']);
    }

    /**
     * @test
     */
    public function 現在のツモリスト取得のGETでツモリスト1名が返る()
    {
        $response = $this->get('tumolink/tumolist');
        $params = [
            'status' => 'OK',
            [
                'community_user_id' => 1,
                'name' => 'ツモリ太郎',
                'tumori' => 'arraival',
                'time' => '12:34',
            ]
        ];
        $response->assertJsonFragment($params);
    }
    
    /**
     * @test
     */
    public function POSTすると200が返る()
    {
        // (2) 実行部分を記述
        $response = $this->post('tumolink');
        // (1) 先に検証部分を記述する
        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function POSTするとIDがJSONで返る()
    {
        $response = $this->post('tumolink');
        $response->assertJsonFragment(['id' => 1]);
    }



        // post時間は指定した時間となっている
        // 0時間0分で行くPOSTすると時刻と異なるメッセージが表示される『いますぐ来るツモリ』
    // 行くツモリpost先を検証する
        // ***POST先は外部からのAPI_POSTと内部POSTで共通化が望ましい
        // API側は認証keyの検証のみして、値はそのままcontrollerのPOST先に内部的に投げる

    // 帰るツモリpostの値を検証する
        // post時間は指定した時間となっている
        // 0時間0分で帰るPOSTすると時刻と異なるメッセージが表示される『いますぐ帰るツモリ』
    // 帰るツモリpost先を検証する



    // 非ログインではツモリンク機能は表示されない
    // ログインするとメイン画面にツモリンク機能が表示される
        // 滞在していない場合は『行くツモリ』ボタンが押せる
        // 『行くツモリ』ボタンを押すと『ツモリスト』に予定時間と名前が表示される
        // 『行くツモリ』ボタンを押すと『ツモリスト』の人数がカウントされる

        // 行くツモリ宣言中にさらに『行くツモリ』ボタンが押せる
        // 滞在中に変更になると『行くツモリ』ボタンは押せない状態に変化する
        // 滞在中に変更になると『ツモリスト』から消える
        // 滞在中は行くツモリボタンは押せない
        // 帰宅するとツモリボタンが押せる状態に変化する

        // 不在中は『帰るツモリ』ボタンは押せない
        // 滞在中になると『帰るツモリ』ボタンが押せる状態に変化する
        // 滞在中は『帰るツモリ』ボタンが押せる
        // 『帰るツモリ』ボタンを押すと『帰るかもリスト』に予定時間と名前が表示される
        // 『帰るツモリ』ボタンを押すと『帰るかもリスト』の人数がカウントされる

        // 帰るツモリ宣言中にさらに『帰るツモリ』ボタンが押せる
        // 滞在から不在に変更になると『帰るツモリ』ボタンは押せない状態に変化する

        // GoogleHome通知ボタンは該当コミュニティ設定でONの際に表示される
        // GoogleHome通知ボタンは該当コミュニティ設定でOFFの際は非表示となる
        // GoogleHome通知ボタン表示中ONでツモリボタンを押すと通知フラグがONでPOSTされる
        // GoogleHome通知ボタン表示中OFFでツモリボタンを押すと通知フラグがOFFでPOSTされる





}
