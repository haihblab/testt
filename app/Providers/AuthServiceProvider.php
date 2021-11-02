<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('update-request', function ($user, $request) {
            return ($user->id == $request->user_id && $request->status == Config::get('constants.status.open'));
        });
        Gate::define('change-status-manager', function ($user, $request) {
            return ($request->status == config('constants.status.open')
                && $user->department_id == $request->user->department_id
                && $user->role_id == config('constants.GET_ROLE_ID.Manager'));
        });
        Gate::define('change-status-admin', function ($user, $request) {
            return ($user->role_id == config('constants.GET_ROLE_ID.Admin')
                && $request->manager_id == $user->id);
        });
        Gate::define('update-request-admin', function ($user, $request) {
            return ($user->role_id == config('constants.GET_ROLE_ID.Admin') && $user->id == $request->manager_id);
        });
    }
}
