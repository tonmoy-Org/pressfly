<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function loginAsUser(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        if (Auth::check()) {
            abort(401);
        }

        try {
            $userId = \decrypt($request->query('data'));
        } catch (\Exception $exception) {
            abort(401);
        }

        $user = User::query()
            ->where(
                [
                    ['id', $userId],
                    ['status', 1],
                ]
            )
            ->first();

        if (!$user) {
            abort(401);
        }

        Auth::login($user);

        return \redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param string|null $username
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function ref($username = null)
    {
        if (!$username) {
            return \redirect('/');
        }

        $user = User::query()
            ->where('username', $username)
            ->where('status', 1)
            ->first();

        if (!$user) {
            return \redirect('/');
        }

        \setcookie('ref', $username, \now()->addMonths(3)->timestamp, '/', '', false, true);

        return \redirect('/');
    }
}
