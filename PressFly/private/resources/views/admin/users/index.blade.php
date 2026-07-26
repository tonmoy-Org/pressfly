<?php
/**
 * @var \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\App\Models\User[] $users
 * @var \App\Models\User $user
 */
?>

@extends('layouts.admin')

@section('title', __('Manage Users'))

@section('content')
    <?php
    $yes_no = [
        1 => __('Yes'),
        0 => __('No'),
    ];
    ?>

    <div class="card">
        <div class="card-body">
            <form method="get" action="{{ route('admin.users.index') }}">

                <div class="form-row">
                    <div class="col">
                        {{ Form::text('Filter[id]', old('Filter[id]', request()->input('Filter.id')), ['class' => 'form-control',
                            'placeholder' => __('ID')]) }}
                    </div>

                    <div class="col">
                        {{ Form::select('Filter[status]', get_user_statuses(), old('Filter[status]', request()->input('Filter.status')),
                            ['placeholder' => __('Status'), 'class' => 'form-control']) }}
                    </div>

                    <div class="col">
                        {{ Form::select('Filter[disable_earnings]', $yes_no, old('Filter[disable_earnings]', request()->input('Filter.disable_earnings')),
                            ['placeholder' => __('Disable Earnings'), 'class' => 'form-control']) }}
                    </div>

                    <div class="col">
                        {{ Form::text('Filter[username]', old('Filter[username]', request()->input('Filter.username')), ['class' => 'form-control',
                            'placeholder' => __('Username')]) }}
                    </div>

                    <div class="col">
                        {{ Form::text('Filter[email]', old('Filter[email]', request()->input('Filter.email')), ['class' => 'form-control',
                            'placeholder' => __('Email')]) }}
                    </div>

                    <div class="col">
                        {{ Form::text('Filter[login_ip]', old('Filter[login_ip]', request()->input('Filter.login_ip')), ['class' => 'form-control',
                            'placeholder' => __('Login IP')]) }}
                    </div>

                    <div class="col">
                        {{ Form::text('Filter[register_ip]', old('Filter[register_ip]', request()->input('Filter.register_ip')), ['class' => 'form-control',
                            'placeholder' => __('Register IP')]) }}
                    </div>

                    <div class="col">
                        {{ Form::submit(__('Submit'), ['class' => 'btn btn-outline-primary']) }}
                        <a href="{{ route('admin.users.index') }}" class="btn btn-link btn-sm'">{{__('Reset')}}</a>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="card card-primary card-outline">
        <div class="card-header">
            <i class="fa fa-users"></i> {{ __('Users') }}
            @can('user_create')
                <button class="btn btn-primary btn-sm float-right"
                        onclick="window.location.href='{{ route('admin.users.create') }}'">
                    <i class="fa fa-plus"></i> {{ __('Add User') }}
                </button>
            @endcan
        </div>
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>
                            {!! link_to_route('admin.users.index', __('ID'),
                                array_merge(request()->query(), ['order' => 'id', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>
                            {!! link_to_route('admin.users.index', __('Username'),
                                array_merge(request()->query(), ['order' => 'username', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>
                            {!! link_to_route('admin.users.index', __('Status'),
                                array_merge(request()->query(), ['order' => 'status', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>
                            {!! link_to_route('admin.users.index', __('Disable Earnings'),
                                array_merge(request()->query(), ['order' => 'disable_earnings', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>
                            {!! link_to_route('admin.users.index', __('Email'),
                                array_merge(request()->query(), ['order' => 'email', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>
                            {!! link_to_route('admin.users.index', __('Login IP'),
                                array_merge(request()->query(), ['order' => 'login_ip', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>
                            {!! link_to_route('admin.users.index', __('Register IP'),
                                array_merge(request()->query(), ['order' => 'register_ip', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>
                            {!! link_to_route('admin.users.index', __('Updated at'),
                                array_merge(request()->query(), ['order' => 'updated_at', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>
                            {!! link_to_route('admin.users.index', __('Created at'),
                                array_merge(request()->query(), ['order' => 'created_at', 'dir' => $orderBy['dir'], 'page' => 1]) ) !!}
                        </th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>

                    <!-- Here is where we loop through our $posts array, printing out post info -->

                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                @can('user_edit')
                                    <a href="{{ route('admin.users.edit', [$user->id]) }}">{{ $user->username }}</a>
                                @else
                                    {{ $user->username }}
                                @endcan
                            </td>
                            <td>{{ get_user_statuses($user->status) }}</td>
                            <td>{{ $yes_no[$user->disable_earnings] }}</td>
                            <td>{{ $user->email  }}</td>
                            <td>{{ $user->login_ip }}</td>
                            <td>{{ $user->register_ip }}</td>
                            <td>{{ display_date_timezone($user->updated_at) }}</td>
                            <td>{{ display_date_timezone($user->created_at) }}</td>
                            <td>
                                <div class="d-inline-flex">
                                    <a class="btn btn-sm btn-primary" target="_blank" href="{{ $user->permalink() }}">
                                        <i class="far fa-eye"></i>
                                    </a>

                                    @can('user_edit')
                                        <a href="{{ route('admin.users.edit', [$user->id]) }}"
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                    @endcan

                                    @can('user_delete')
                                        {!! delete_form('admin.users.destroy', $user->id) !!}
                                    @endcan

                                    @can('super_admin')
                                        @if($user->status === 1)
                                            <button class="btn btn btn-primary btn-sm" type="button" data-toggle="modal"
                                                    data-target="#login-as-{{ $user->id }}">
                                                {{ __('Login as user') }}
                                            </button>

                                            <div class="modal fade" id="login-as-{{ $user->id }}" tabindex="-1"
                                                 role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                {{ __('Login as user') }} <b>{{ $user->username }}</b>
                                                            </h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>{{ __('Copy the following URL and paste it on a different browser so you can login as this user. Please note, the URL will expire after 5 minutes.') }}</p>

                                                            <code>
                                                                <?php
                                                                echo \Illuminate\Support\Facades\URL::temporarySignedRoute(
                                                                    'login.as',
                                                                    \now()->addMinutes(5),
                                                                    ['data' => \encrypt($user->id)]
                                                                );
                                                                ?>
                                                            </code>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endcan

                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @php unset($user); @endphp
                </table>

                {{ $users->appends(request()->except(['page']))->links() }}
            </div>

        </div><!-- /.box-body -->
    </div>

@endsection

