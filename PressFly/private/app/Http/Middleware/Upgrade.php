<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class Upgrade
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($request->route()->getPrefix() !== '/admin') {
            return $next($request);
        }
        /*
        if (!auth()->check()) {
            return $next($request);
        }

        if (auth()->user()->role !== 'admin') {
            return $next($request);
        }
        */

        if ($this->databaseUpgrade($request)) {
            if (!Gate::allows('super_admin')) {
                exit('The super admin should upgrade the system first.');
            }

            return \to_route('admin.upgrade')->setStatusCode(307);
        }

        return $next($request);
    }

    protected function databaseUpgrade(Request $request): bool
    {
        if (\require_database_upgrade() &&
            !str_contains($request->route()->getAction('controller'), 'UpgradeController')) {
            return true;
        }

        return false;
    }
}
