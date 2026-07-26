<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\App\Models\AdminGroup[] $adminGroups
 */

?>

@extends('layouts.admin')

@section('title', __('Manage Admin Groups'))

@section('content')

    <div class="card card-primary card-outline">
        <div class="card-header">
            <i class="fas fa-user-shield"></i> {{ __('Admin Groups') }}
            <button class="btn btn-primary btn-sm float-right"
                    onclick="window.location.href='{{ route('admin.admin-groups.create') }}'">
                <i class="fa fa-plus"></i> {{ __('Add Group') }}
            </button>
        </div>
        <div class="card-body p-0">

            <table class="table table-responsive-sm table-striped">
                <thead class="thead-light">
                <tr>
                    <th>{{ __('Id') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Permissions') }}</th>
                    <th>{{ __('Updated at') }}</th>
                    <th>{{ __('Created at') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>

                @foreach ($adminGroups as $group)
                    <tr>
                        <td>{{ $group->id }}</td>
                        <td>
                            <a href="{{ route('admin.admin-groups.edit', [$group->id]) }}">{{ $group->name }}</a>
                        </td>
                        <td>{{ collect($group->permissions)->implode(', ') }}</td>
                        <td>{{ display_date_timezone($group->updated_at)  }}</td>
                        <td>{{ display_date_timezone($group->created_at) }}</td>
                        <td>
                            <div class="d-inline-flex">
                                {!! delete_form('admin.admin-groups.destroy', $group->id) !!}
                            </div>
                        </td>
                    </tr>
                @endforeach

            </table>

            <div class="table-responsive">
                {{ $adminGroups->appends(request()->except(['page']))->links() }}
            </div>

        </div><!-- /.box-body -->
    </div>

@endsection
