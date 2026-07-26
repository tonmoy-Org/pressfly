<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends AdminController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $this->authorize('comment_view');

        $orderBy = [
            'col' => request()->input('order', 'id'),
            'dir' => request()->input('dir', 'desc'),
        ];

        $comments = Comment::query()
            ->with(['user', 'article'])
            ->orderBy($orderBy['col'], $orderBy['dir'])
            ->paginate(20);

        $orderBy['dir'] = ($orderBy['dir'] === 'asc') ? 'desc' : 'asc';

        return \view(
            'admin.comments.index',
            [
                'comments' => $comments,
                'orderBy' => $orderBy,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Comment $comment)
    {
        $this->authorize('comment_edit');

        $comment->load('article');

        return \view(
            'admin.comments.edit',
            [
                'comment' => $comment,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('comment_edit');

        $data = $request->only(
            [
                'status',
                'content',
            ]
        );

        $validator = Validator::make(
            $data,
            [
                'status' => 'required',
                'content' => 'required',
            ]
        );

        if ($validator->fails()) {
            return \redirect()->route('admin.comments.edit', [$comment->id])
                ->withErrors($validator)
                ->withInput();
        }

        $comment->update($data);

        \session()->flash('success', 'The comment updated.');

        return \redirect()->route('admin.comments.edit', [$comment->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('comment_delete');

        try {
            DB::transaction(function () use ($comment) {
                $repliesIds = $comment::getChildrenRepliesIds($comment->id);
                if (count($repliesIds)) {
                    Comment::whereIn('id', $repliesIds)->delete();
                }
                $comment->delete();
            });
            \session()->flash('success', __('Comment deleted.'));
        } catch (\Throwable $throwable) {
            \session()->flash('danger', 'Error: ' . $throwable->getMessage());
        }

        return \redirect()->route('admin.comments.index');
    }
}
