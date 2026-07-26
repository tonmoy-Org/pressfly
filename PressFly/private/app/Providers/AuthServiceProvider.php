<?php

namespace App\Providers;

use App\Models\AdminGroup;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        foreach (\array_keys(AdminGroup::getAllPermissions()) as $permission) {
            Gate::define($permission, function (User $user) use ($permission) {
                if ($user->role !== 'admin') {
                    return false;
                }

                if (\version_compare(\get_option('version', '1.0.0'), '3.0.0', '<')) {
                    return true;
                }

                $permissions = $user->adminGroup->permissions ?? [];

                if (\in_array('super_admin', $permissions)) {
                    return true;
                }

                return \in_array($permission, $permissions);
            });
        }
    }
}
