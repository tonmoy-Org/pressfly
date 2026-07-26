<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\App\Models\File[] $files
 */

?>

@extends('layouts.admin')

@section('title', __('Manage Files'))

@section('content')

    <div class="card border">
        <div class="card-body">
            <form method="get" action="{{ route('admin.files.index') }}" class="form-inline">

                <div class="form-group">
                    <input type="text" name="Filter[name]" placeholder="{{ __('Name') }}" class="form-control"
                           value="{{ old('Filter[name]', request()->input('Filter.name')) }}">
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-outline-primary" value="{{ __('Submit') }}">
                </div>

                <div class="form-group">
                    <a href="{{ route('admin.files.index') }}" class="btn btn-link btn-sm'">{{__('Reset')}}</a>
                </div>

            </form>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h5 class="m-0">
                <i class="far fa-images"></i> {{ __('Files') }}
                @can('file_upload')
                    <button class="btn btn-primary btn-sm float-right"
                            onclick="window.location.href='{{ route('admin.files.create') }}'">
                        <i class="fas fa-upload"></i> {{ __('File Upload') }}
                    </button>
                @endcan
            </h5>
        </div>
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>
                            {!! link_to_route('admin.files.index', __('Id'),
                                array_merge(request()->query(), ['order' => 'id', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>{{ __('Thumbnail') }}</th>
                        <th>
                            {!! link_to_route('admin.files.index', __('Name'),
                                array_merge(request()->query(), ['order' => 'name', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>{{ __('Username') }}</th>
                        <th>
                            {!! link_to_route('admin.files.index', __('Updated'),
                                array_merge(request()->query(), ['order' => 'updated_at', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>
                            {!! link_to_route('admin.files.index', __('Created'),
                                array_merge(request()->query(), ['order' => 'created_at', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>

                    @foreach ($files as $file)
                        <tr>
                            <td>{{ $file->id }}</td>
                            <td>
                                <a href="{{ asset($file->file) }}" target="_blank">
                                    <img src="{{ $file->thumbnail() }}" width="50" height="50">
                                </a>
                            </td>
                            <td>
                                @can('file_edit')
                                    <a href="{{ route('admin.files.edit', [$file->id]) }}">{{ $file->name }}</a>
                                @else
                                    {{ $file->name }}
                                @endcan

                                <input id="file-url" type="url" class="form-control input-sm"
                                       value='{{ asset($file->file) }}' readonly onfocus="this.select()">
                            </td>
                            <td>{{ $file->user->username }}</td>
                            <td>{{ display_date_timezone($file->updated_at) }}</td>
                            <td>{{ display_date_timezone($file->created_at) }}</td>
                            <td>
                                @can('file_edit')
                                    <a class="btn btn-sm btn-primary"
                                       href="{{ route('admin.files.edit', [$file->id]) }}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                @endcan

                                @can('file_delete')
                                    {!! delete_form('admin.files.destroy', $file->id) !!}
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    @php unset($file); @endphp
                </table>

                {{ $files->appends(request()->except(['page']))->links() }}
            </div>

        </div><!-- /.box-body -->
    </div>

@endsection
