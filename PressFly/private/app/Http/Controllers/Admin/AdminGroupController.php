<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminGroupController extends AdminController
{
    /**
     * Execute an action on the controller.
     *
     * @param string $method
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function callAction($method, $parameters)
    {
        $this->authorize('super_admin');

        return parent::callAction($method, $parameters);
    }

    public function index()
    {
        $adminGroups = AdminGroup::paginate(20);

        return \view(
            'admin.admin-groups.index',
            [
                'adminGroups' => $adminGroups,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return \view('admin.admin-groups.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->only(
            [
                'name',
                'permissions',
            ]
        );

        $validator = Validator::make(
            $data,
            [
                'name' => 'required',
                'permissions' => [
                    'required',
                    'array',
                ],
            ]
        );

        if ($validator->fails()) {
            return \redirect()->route('admin.admin-groups.create')
                ->withErrors($validator)
                ->withInput();
        }

        $adminGroup = AdminGroup::create($data);

        \session()->flash('message', 'Admin group added.');

        return \redirect()->route('admin.admin-groups.edit', [$adminGroup->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\AdminGroup $adminGroup
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(AdminGroup $adminGroup)
    {
        return \view(
            'admin.admin-groups.edit',
            [
                'adminGroup' => $adminGroup,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\AdminGroup $adminGroup
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, AdminGroup $adminGroup)
    {
        $data = $request->only(
            [
                'name',
                'permissions',
            ]
        );

        $validator = Validator::make(
            $data,
            [
                'name' => 'required',
                'permissions' => [
                    'required',
                    'array',
                ],
            ]
        );

        if ($adminGroup->id === 1) {
            $data['permissions'] = ['super_admin'];
        }

        if ($validator->fails()) {
            return \redirect()->route('admin.admin-groups.edit', [$adminGroup->id])
                ->withErrors($validator)
                ->withInput();
        }

        $adminGroup->update($data);

        \session()->flash('message', 'The admin group updated.');

        return \redirect()->route('admin.admin-groups.edit', [$adminGroup->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\AdminGroup $adminGroup
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function destroy(AdminGroup $adminGroup)
    {
        if ($adminGroup->id === 1) {
            \session()->flash('danger', 'The super admin group can not be deleted');
            return \redirect()->route('admin.admin-groups.index');
        }

        \DB::table('users')
            ->where('admin_group_id', $adminGroup->id)
            ->update(
                [
                    'role' => 'member',
                    'admin_group_id' => null,
                ]
            );

        $adminGroup->delete();

        \session()->flash('message', 'Admin group deleted.');

        return \redirect()->route('admin.admin-groups.index');
    }
}
