<?php

namespace App\Rules;

use DB;
use Illuminate\Contracts\Validation\Rule;

class ThisCommunityExist implements Rule
{
    protected $community_id;
    protected $unique_name;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($community_id, $unique_name)
    {
        $this->community_id = $community_id;
        $this->unique_name = $unique_name;
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
        $community = DB::table('community_user')->where('community_id', $this->community_id);
        return $users = DB::table('users')
            ->JoinSub($community, 'community_user', function($join) {
                $join->on('users.id', '=', 'community_user.user_id');
            })->where('unique_name', $this->unique_name)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'このコミュニティに登録されていません';
    }
}
