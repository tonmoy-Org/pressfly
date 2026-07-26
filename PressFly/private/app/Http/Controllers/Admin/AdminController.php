<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function callAction($method, $parameters)
    {
        $this->licenseCheck();
        return parent::callAction($method, $parameters);
    }

    protected function licenseCheck()
    {
        if (\request()->route()->getPrefix() !== '/admin') {
            return;
        }

        if (\auth()->user()->role !== 'admin') {
            return;
        }

        if ($this->licenseActivate()) {
            if (!\Illuminate\Support\Facades\Gate::allows('super_admin')) {
                exit('The super admin should activate the system first.');
            }

            \redirect()->route('admin.activation')->setStatusCode(307)->send();
            exit();
        }
    }

    protected function licenseActivate(): bool
    {
        if (\require_database_upgrade()) {
            return false;
        }

        if (\App\Helpers\Activation::checkLicense() === false &&
            !str_contains(\request()->route()->getAction('controller'), 'ActivationController')) {
            return true;
        }

        return false;
    }
}
