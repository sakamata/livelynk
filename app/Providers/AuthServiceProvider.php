<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //  スーパー管理者のみに許可
        Gate::define('superAdmin', function ($user) {
            return (
                $user->role == 'superAdmin'
            );
        });

        // リーダー管理者以上（リーダー管理者 & スーパー管理者）に許可
        Gate::define('readerAdmin', function ($user) {
            return (
                $user->role == 'readerAdmin' ||
                $user->role == 'superAdmin'
            );
        });

        // 委託管理者以上（委託管理者 & リーダー管理者 & スーパー管理者）に許可
        Gate::define('normalAdmin', function ($user) {
            return (
                $user->role == 'normalAdmin' ||
                $user->role == 'readerAdmin' ||
                $user->role == 'superAdmin'
            );
        });

        // 一般ユーザ以上（つまり全権限）に許可
        Gate::define('normal', function ($user) {
            return (
                $user->role == 'normal' ||
                $user->role == 'normalAdmin' ||
                $user->role == 'readerAdmin' ||
                $user->role == 'superAdmin'
            );
        });

        // コミュニティ管理者 reader normal に許可
        Gate::define('communityAdmin', function ($user) {
            return (
                $user->role == 'readerAdmin' ||
                $user->role == 'normalAdmin'
            );
        });

        // 退会できる権限のあるユーザー
        Gate::define('deactivation', function ($user) {
            return (
                $user->role == 'normal' ||
                $user->role == 'normalAdmin'
            );
        });
    }
}
