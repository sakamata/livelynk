<?php

namespace App\Rules;

use DB;
use Illuminate\Contracts\Validation\Rule;

class UniqueNameEdit implements Rule
{
    protected $user_id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // 自分以外のユーザーがIDを使用していた場合 false を返す
        return DB::table('users')->where([
            ['id', '<>', $this->user_id],
            ['unique_name', $value],
        ])->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'このユーザーIDは既に使われています';
    }
}
