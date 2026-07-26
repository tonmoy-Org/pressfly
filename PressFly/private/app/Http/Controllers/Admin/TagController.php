<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TagController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('tag_view');

        $conditions = [];

        if (null !== request()->input('Filter')) {
            $filter_fields = [
                'name',
                'slug',
            ];

            foreach (request()->input('Filter') as $param_name => $param_value) {
                if (!$param_value) {
                    continue;
                }
                //$value = urldecode($value);
                if (in_array($param_name, $filter_fields)) {
                    $like_params = ['name', 'slug'];

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

        $tags = Tag::where($conditions)
            ->orderBy($orderBy['col'], $orderBy['dir'])
            ->paginate(20);

        $orderBy['dir'] = ($orderBy['dir'] === 'asc') ? 'desc' : 'asc';

        return \view(
            'admin.tags.index',
            [
                'tags' => $tags,
                'orderBy' => $orderBy,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('tag_create');

        return \view('admin.tags.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('tag_create');

        $data = $request->only(
            [
                'name',
                'slug',
                'status',
                'description',
                'seo',
            ]
        );

        if (isset($data['slug']) && !empty($data['slug'])) {
            $data['slug'] = Tag::createSlug(Tag::class, $data['slug']);
        } else {
            $data['slug'] = Tag::createSlug(Tag::class, $data['name']);
        }

        $data['description'] = Tag::purify($data['description']);

        $validator = Validator::make(
            $data,
            [
                'name' => 'required',
                'slug' => 'required',
                'status' => 'required',
                'seo' => [
                    'nullable',
                    'array',
                ],
            ]
        );

        if ($validator->fails()) {
            return \redirect()->route('admin.tags.create')
                ->withErrors($validator)
                ->withInput();
        }

        $tag = Tag::create($data);

        \Cache::forget('homepage');

        \session()->flash('message', 'Tag added.');

        return \redirect()->route('admin.tags.edit', [$tag->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Tag $tag
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Tag $tag)
    {
        $this->authorize('tag_edit');

        return \view(
            'admin.tags.edit',
            [
                'tag' => $tag,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Tag $tag
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Tag $tag)
    {
        $this->authorize('tag_edit');

        $data = $request->only(
            [
                'name',
                'slug',
                'status',
                'description',
                'seo',
            ]
        );

        if (isset($data['slug']) && !empty($data['slug'])) {
            $data['slug'] = Tag::createSlug(Tag::class, $data['slug'], $tag->id);
        } else {
            $data['slug'] = Tag::createSlug(Tag::class, $data['name'], $tag->id);
        }

        $data['description'] = Tag::purify($data['description']);

        $validator = Validator::make(
            $data,
            [
                'name' => 'required',
                'slug' => 'required',
                'status' => 'required',
                'seo' => [
                    'nullable',
                    'array',
                ],
            ]
        );

        if ($validator->fails()) {
            return \redirect()->route('admin.tags.edit', [$tag->id])
                ->withErrors($validator)
                ->withInput();
        }

        $tag->update($data);

        \Cache::forget('homepage');

        \session()->flash('message', 'The tag updated.');

        return \redirect()->route('admin.tags.edit', [$tag->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Tag $tag
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function destroy(Tag $tag)
    {
        $this->authorize('tag_delete');

        try {
            DB::transaction(function () use ($tag) {
                if ($tag->delete()) {
                    $tag->articles()->detach();
                } else {
                    \session()->flash('message', __('Unable to delete this tag.'));
                }
            });
            \session()->flash('message', __('Tag has been deleted.'));
        } catch (\Throwable $throwable) {
            \session()->flash('danger', 'Error: ' . $throwable->getMessage());
        }

        return \redirect()->route('admin.tags.index');
    }
}
