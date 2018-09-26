<?php

namespace App\Rules;

use DB;
use Illuminate\Contracts\Validation\Rule;

class UniqueCommunity implements Rule
{
    protected $community_id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($community_id)
    {
        $this->community_id = $community_id;
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
        // users tableの同コミュニティ内に該当のEmailが存在しなければ true を返す
        return DB::table('users')->where([
            ['community_id', $this->community_id],
            ['email', $value],
        ])->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'このメールアドレスはこのコミュニティで既に使われています。';
    }
}
