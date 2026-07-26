<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Install
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
        $is_install_url = \strpos($_SERVER['REQUEST_URI'], '/install');
        //$is_install_url = \strpos($request->url(), '/install');

        if (is_app_installed() === false) {
            if ($is_install_url === false) {
                return \to_route('install.index')->setStatusCode(307);
            }
        } elseif ($is_install_url !== false) {
            return \redirect(\url('/'));
        }

        return $next($request);
    }
}
