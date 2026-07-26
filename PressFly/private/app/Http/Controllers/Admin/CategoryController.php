<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('category_view');

        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();

        return \view(
            'admin.categories.index',
            [
                'categories' => $categories,
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
        $this->authorize('category_create');

        $all_categories = Category::pluck('name', 'id');

        return \view(
            'admin.categories.create',
            [
                'all_categories' => $all_categories,
            ]
        );
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
        $this->authorize('category_create');

        $data = $request->only(
            [
                'parent_id',
                'name',
                'slug',
                'status',
                'description',
                'color',
                'seo',
            ]
        );

        if (isset($data['slug']) && !empty($data['slug'])) {
            $data['slug'] = Category::createSlug(Category::class, $data['slug']);
        } else {
            $data['slug'] = Category::createSlug(Category::class, $data['name']);
        }

        $data['description'] = Category::purify($data['description']);

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
            return \redirect()->route('admin.categories.create')
                ->withErrors($validator)
                ->withInput();
        }

        $category = Category::create($data);

        \Cache::forget('homepage');

        \session()->flash('message', 'Category added.');

        return \redirect()->route('admin.categories.edit', [$category->id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Category $category)
    {
        $this->authorize('category_edit');

        $categories = Category::pluck('name', 'id');

        return \view(
            'admin.categories.edit',
            [
                'category' => $category,
                'categories' => $categories,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('category_edit');

        $data = $request->only(
            [
                'parent_id',
                'name',
                'slug',
                'status',
                'description',
                'color',
                'seo',
            ]
        );

        if (isset($data['slug']) && !empty($data['slug'])) {
            $data['slug'] = Category::createSlug(Category::class, $data['slug'], $category->id);
        } else {
            $data['slug'] = Category::createSlug(Category::class, $data['name'], $category->id);
        }

        $data['description'] = Category::purify($data['description']);

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
            return \redirect()->route('admin.categories.edit', [$category->id])
                ->withErrors($validator)
                ->withInput();
        }

        /*
        if (empty($data['parent_id'])) {
            $data['parent_id'] = 0;
        }
        */

        $category->update($data);

        \Cache::forget('homepage');

        session()->flash('message', 'The category updated.');

        return \redirect()->route('admin.categories.edit', [$category->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function destroy(Category $category)
    {
        $this->authorize('category_delete');


        try {
            DB::transaction(function () use ($category) {
                if ($category->delete()) {
                    $category->articles()->detach();
                    DB::table('categories')->where('parent_id', $category->id)->update(['parent_id' => null]);
                } else {
                    \session()->flash('message', __('Unable to delete this category.'));
                }
            });
            \session()->flash('message', __('Category has been deleted.'));
        } catch (\Throwable $throwable) {
            \session()->flash('danger', 'Error: ' . $throwable->getMessage());
        }

        return \redirect()->route('admin.categories.index');
    }
}
