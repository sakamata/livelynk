<?php

namespace App\Service;

use DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
// 設定用の値を入れるModel set_key, set_value というカラムで各種設定を管理・保存、更新する
class SystemSettingService
{
    public function CreateKeyOrUpdate(string $set_key, string $set_value)
    {
        // set_key でkey名を取得するか、存在しなければ作成する
        $set = 'App\SystemSetting'::firstOrCreate(
            ['set_key' => $set_key],
            ['set_value' => $set_value]
        );
        $set->save();
        return $set;
    }

    // 上記のCreateKeyOrUpdateと機能が重複するが安全な更新の為通常はこちらを使用する
    public function updateValue(string $set_key, string $set_value)
    {
        return DB::table('systems_settings')
            ->where('set_key', $set_key)
            ->update(['set_value' => $set_value]);
    }

    public function setValue(string $set_key, string $set_value)
    {
        return DB::table('systems_settings')
            ->where('set_key', $set_key)
            ->pluck('set_value')->first();
    }


    public function getValue(string $set_key)
    {
        return DB::table('systems_settings')
            ->where('set_key', $set_key)
            ->pluck('set_value')->first();
    }

}
