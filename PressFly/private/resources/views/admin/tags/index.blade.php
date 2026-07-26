<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\App\Models\Tag[] $tags
 * @var \App\Models\Tag $tag
 */

?>

@extends('layouts.admin')

@section('title', __('Manage Tag'))

@section('content')

    <div class="card border">
        <div class="card-body">
            <form class="form-inline" method="get" action="{{ route('admin.tags.index') }}">

                {{ Form::text('Filter[name]', old('Filter[name]', request()->input('Filter.name')), ['class' => 'form-control',
                    'placeholder' => __('Name')]) }}

                {{ Form::text('Filter[slug]', old('Filter[slug]', request()->input('Filter.slug')), ['class' => 'form-control',
                    'placeholder' => __('slug')]) }}

                <div class="form-group">
                    {{ Form::submit(__('Submit'), ['class' => 'btn btn-outline-primary']) }}
                </div>

                <div class="form-group">
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-link btn-sm'">{{__('Reset')}}</a>
                </div>

            </form>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <i class="fa fa-tags"></i> {{ __('Tags') }}
            @can('tag_create')
                <button class="btn btn-primary btn-sm float-right"
                        onclick="window.location.href='{{ route('admin.tags.create') }}'">
                    <i class="fa fa-plus"></i> {{ __('Add Tag') }}
                </button>
            @endcan
        </div>
        <div class="card-body p-0">

            <table class="table table-responsive-sm table-striped">
                <thead class="thead-light">
                <tr>
                    <th>
                        {!! link_to_route('admin.tags.index', __('Id'),
                            array_merge(request()->query(), ['order' => 'id', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                    </th>
                    <th>
                        {!! link_to_route('admin.tags.index', __('Name'),
                            array_merge(request()->query(), ['order' => 'name', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                    </th>
                    <th>
                        {!! link_to_route('admin.tags.index', __('Slug'),
                            array_merge(request()->query(), ['order' => 'slug', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                    </th>
                    <th>{{ __('Published') }}</th>
                    <th>{{ __('Updated at') }}</th>
                    <th>
                        {!! link_to_route('admin.tags.index', __('Created at'),
                            array_merge(request()->query(), ['order' => 'created_at', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                    </th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>

                <!-- Here is where we loop through our $posts array, printing out post info -->

                @foreach ($tags as $tag)
                    <tr>
                        <td>{{ $tag->id }}</td>
                        <td>
                            @can('tag_edit')
                                <a href="{{ route('admin.tags.edit', [$tag->id]) }}">{{ $tag->name }}</a>
                            @else
                                {{ $tag->name }}
                            @endcan
                        </td>
                        <td>{{ $tag->slug }}</td>
                        <td>@php echo ($tag->status) ? __('Yes') : __('No') @endphp</td>
                        <td>{{ display_date_timezone($tag->updated_at)  }}</td>
                        <td>{{ display_date_timezone($tag->created_at) }}</td>
                        <td>
                            <div class="d-inline-flex">
                                <a class="btn btn-sm btn-primary" target="_blank" href="{{ $tag->permalink() }}">
                                    <i class="fa fa-eye"></i>
                                </a>

                                @can('tag_delete')
                                    {!! delete_form('admin.tags.destroy', $tag->id) !!}
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach

            </table>

            <div class="table-responsive">
                {{ $tags->appends(request()->except(['page']))->links() }}
            </div>

        </div><!-- /.box-body -->
    </div>

@endsection
