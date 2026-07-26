<?php

namespace App\Http\Controllers\Admin;

use App\Mail\EmailVerify;
use App\Models\AdminGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UsersController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('user_view');

        $conditions = [];

        if (null !== request()->input('Filter')) {
            $filter_fields = [
                'id',
                'status',
                'disable_earnings',
                'username',
                'email',
                'login_ip',
                'register_ip',
            ];

            foreach (request()->input('Filter') as $param_name => $param_value) {
                if (!$param_value) {
                    continue;
                }
                //$value = urldecode($value);
                if (in_array($param_name, $filter_fields)) {
                    $like_params = ['username', 'email'];

                    if (in_array($param_name, $like_params)) {
                        $conditions[] = [$param_name, 'like', '%' . $param_value . '%'];
                    } else {
                        $conditions[] = [$param_name, '=', $param_value];
                    }
                }
            }
        }

        $orderBy = [
            'col' => request()->input('order', 'id'),
            'dir' => request()->input('dir', 'desc'),
        ];

        $users = User::where($conditions)
            ->orderBy($orderBy['col'], $orderBy['dir'])
            ->paginate(20);

        $orderBy['dir'] = ($orderBy['dir'] === 'asc') ? 'desc' : 'asc';

        return \view(
            'admin.users.index',
            [
                'users' => $users,
                'orderBy' => $orderBy,
            ]
        );
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function referrals()
    {
        $this->authorize('user_referrals');

        $referrals = User::query()
            ->whereNotNull('referred_by')
            ->paginate(20);

        foreach ($referrals as $referral) {
            $referred_by = User::find($referral->referred_by);
            if ($referred_by) {
                $referral->referred_by_username = User::find($referral->referred_by)->username;
            }
        }

        return \view(
            'admin.users.referrals',
            [
                'referrals' => $referrals,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('user_create');

        $adminGroups = AdminGroup::select(['id', 'name'])->get()->pluck('name', 'id')->toArray();

        return \view('admin.users.create', ['adminGroups' => $adminGroups]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('user_create');

        $data = $request->only(
            [
                'role',
                'admin_group_id',
                'status',
                'username',
                'email',
                'password',
            ]
        );

        $validator = Validator::make(
            $data,
            [
                'role' => 'required',
                'admin_group_id' => 'required_if:role,admin',
                'status' => 'required',
                'username' => 'required|string|alpha_num|min:3|max:50|unique:users',
                'email' => 'required|string|email|max:191|unique:users',
                'password' => 'required|string|min:6',
            ]
        );

        if ($validator->fails()) {
            return \redirect()->route('admin.users.create')
                ->withErrors($validator)
                ->withInput();
        }

        if ((int)$data['status'] === 2) {
            $data['activation_key'] = sha1(\Str::Random(40));
        }

        $data['password'] = Hash::make($data['password']);
        $data['author_earnings'] = price_database_format(get_option('signup_bonus', 0));

        $user = User::create($data);

        if ((int)$data['status'] === 2 && get_option('account_activate_email', 'yes') == 'yes') {
            try {
                Mail::send(new EmailVerify($user));
            } catch (\Exception $exception) {
                \Log::error($exception->getMessage());
            }
        }

        \session()->flash('message', __('User added.'));

        return \redirect()->route('admin.users.edit', [$user->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $user)
    {
        $this->authorize('user_edit');

        $adminGroups = AdminGroup::select(['id', 'name'])->get()->pluck('name', 'id')->toArray();

        return \view(
            'admin.users.edit',
            [
                'user' => $user,
                'adminGroups' => $adminGroups,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('user_edit');

        $data = $request->only(
            [
                'role',
                'admin_group_id',
                'status',
                'disable_earnings',
                'username',
                'email',
                'referred_by',
                'password',
                'name',
                'description',
                'author_earnings',
                'referral_earnings',
            ]
        );

        // TODO add the full validation rules
        $validator = Validator::make(
            $data,
            [
                'role' => 'required',
                'admin_group_id' => 'required_if:role,admin',
                'status' => 'required',
                'username' => 'required|string|alpha_num|min:3|max:50|unique:users,username,' . $user->id,
                'email' => 'required|string|email|max:191|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:6',
            ]
        );

        if ($validator->fails()) {
            return \redirect()->route('admin.users.edit', [$user->id])
                ->withErrors($validator)
                ->withInput();
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        \session()->flash('message', 'The user updated.');

        return \redirect()->route('admin.users.edit', [$user->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        $this->authorize('user_delete');

        if ($user->id === user()->id) {
            session()->flash('danger', __("You can't delete your account."));
            return redirect()->route('admin.users.index');
        }

        try {
            DB::transaction(function () use ($user) {
                if ($user->delete()) {
                    foreach ($user->articles as $article) {
                        $article->tags()->detach();
                        $article->categories()->detach();
                        $article->delete();
                    }
                    $user->comments()->delete();
                    $user->bookmarks()->detach();
                    $user->likes()->detach();
                    $user->followers()->detach();
                    $user->followings()->detach();
                    $user->socialProfiles()->delete();
                    $user->withdraws()->delete();
                    $user->statistics()->delete();

                    \DB::table('users')->where('referred_by', $user->id)->update(['referred_by' => null]);

                    foreach ($user->files as $file) {
                        $file_path = public_path($file->file);

                        $file_info = pathinfo($file_path);

                        @unlink($file_path);

                        array_map(
                            'unlink',
                            (array)@glob(
                                $file_info['dirname'] . DS . $file_info['filename'] . "-*." . $file_info['extension']
                            )
                        );
                    }
                    $user->files()->delete();
                } else {
                    \session()->flash('danger', __('Unable to delete this article.'));
                }
            });
            \session()->flash('message', __('Article has been deleted.'));
        } catch (\Throwable $throwable) {
            \session()->flash('danger', 'Error: ' . $throwable->getMessage());
        }

        return \redirect()->route('admin.users.index');
    }
}
