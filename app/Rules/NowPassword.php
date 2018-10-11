<?php

namespace App\Rules;

use DB;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class NowPassword implements Rule
{
    protected $user;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->user = DB::table('users')->where('id', $id)->first();
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
        return Hash::check($value, $this->user->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '現在のPasswordが一致しません';
    }
}
