<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\App\Models\Comment[] $comments
 */

?>

@extends('layouts.admin')

@section('title', __('Manage Comments'))

@section('content')

    <div class="card card-primary card-outline">
        <div class="card-header">
            <i class="far fa-comments"></i> {{ __('Comments') }}
        </div>
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>
                            {!! link_to_route('admin.comments.index', __('Id'),
                                array_merge(request()->query(), ['order' => 'id', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>{{ __('Author') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Content') }}</th>
                        <th>{{ __('In Response To') }}</th>
                        <th>
                            {!! link_to_route('admin.comments.index', __('Updated'),
                                array_merge(request()->query(), ['order' => 'updated_at', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>
                            {!! link_to_route('admin.comments.index', __('Created'),
                                array_merge(request()->query(), ['order' => 'created_at', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>

                    @foreach ($comments as $comment)
                        <tr>
                            <td>{{ $comment->id }}</td>
                            <td>{{ $comment->user->username }}</td>
                            <td>{{ get_comment_statuses($comment->status) }}</td>
                            <td>{{ $comment->content }}</td>
                            <td><a href="{{ $comment->article->permalink() }}"
                                   target="_blank">{{ $comment->article->title  }}</a></td>
                            <td>{{ display_date_timezone($comment->updated_at) }}</td>
                            <td>{{ display_date_timezone($comment->created_at) }}</td>
                            <td>
                                @can('comment_edit')
                                    <a class="btn btn-sm btn-primary"
                                       href="{{ route('admin.comments.edit', [$comment->id]) }}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                @endcan

                                @can('comment_delete')
                                    {!! delete_form('admin.comments.destroy', $comment->id) !!}
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <div class="table-responsive">
                {{ $comments->appends(request()->except(['page']))->links() }}
            </div>

        </div><!-- /.box-body -->
    </div>

@endsection
