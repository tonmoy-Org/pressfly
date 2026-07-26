<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\App\Models\Category[] $categories
 */
?>

@extends('layouts.admin')

@section('title', __('Manage Categories'))

@section('content')

    <?php
    //dump(\App\Models\Category::categoryTree(0, 'array'));
    //dump(\App\Models\Category::categoryTree(0, 'select'));
    ?>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <i class="fas fa-layer-group"></i> {{ __('Categories') }}
            @can('category_create')
                <button class="btn btn-primary btn-sm float-right"
                        onclick="window.location.href='{{ route('admin.categories.create') }}'">
                    <i class="fa fa-plus"></i> {{ __('Add Category') }}
                </button>
            @endcan
        </div>
        <div class="card-body p-0">

            <table class="table table-responsive-sm table-striped">
                <thead class="thead-light">
                <tr>
                    <th>{{ __('Id') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Slug') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Updated at') }}</th>
                    <th>{{ __('Created at') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>

                {{-- \App\Models\Category::display_categories($categories) --}}

                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>
                            <a href="{{ route('admin.categories.edit', [$category->id]) }}">{{ $category->name }}</a>
                        </td>
                        <td>{{ $category->slug }}</td>
                        <td>@php echo ($category->status) ? __('Active') : __('Inactive') @endphp</td>
                        <td>{{ display_date_timezone($category->updated_at)  }}</td>
                        <td>{{ display_date_timezone($category->created_at) }}</td>
                        <td>
                            <a class="btn btn-sm btn-primary" target="_blank"
                               href="{{ $category->permalink() }}"><i
                                        class="fa fa-eye"></i></a>

                            {!! delete_form('admin.categories.destroy', $category->id) !!}
                        </td>
                    </tr>
                @endforeach
            </table>

        </div><!-- /.box-body -->
    </div>


@endsection
