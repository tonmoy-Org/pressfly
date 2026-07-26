@extends('layouts.admin')

@section('title', __('Add New Admin Group'))

@section('content')

    <form action="{{ route('admin.admin-groups.store') }}" method="POST">
        @csrf

        <div class="card card-primary card-outline">
            <div class="card-header">{{ __('Add New Admin Group') }}</div>
            <div class="card-body">
                <div class="form-group">
                    {{ Form::label('name', __('Name')) }}
                    {{ Form::text('name', old('name'), ['class' => 'form-control', 'required' => true]) }}
                </div>

                <div class="form-group">
                    <div>
                        {{ Form::label('permissions', __('Permissions')) }}
                    </div>
                    @foreach (array_keys(\App\Models\AdminGroup::getAllPermissions()) as $permission)
                        <div class="form-check form-check-inline">
                            <label class="form-check-label">
                                <input type="checkbox" name="permissions[]" value="{{ $permission }}"
                                       @if(in_array($permission, old('permissions', []))) checked
                                       @endif
                                       class="form-check-input"> {{ $permission }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    {{ Form::submit(__('Submit'), ['class' => 'btn btn-primary']) }}
                </div>
            </div>
        </div>

    </form>

@endsection
