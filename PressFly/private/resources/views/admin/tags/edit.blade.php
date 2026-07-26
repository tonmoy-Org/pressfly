@extends('layouts.admin')

@section('title', __('Edit Tag'))

@section('content')

    <form action="{{ route('admin.tags.update', $tag->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card card-primary card-outline">
            <div class="card-header">{{ __('Edit Tag') }}</div>
            <div class="card-body">
                <div class="form-group">
                    {{ Form::label('name', __('Name')) }}
                    {{ Form::text('name', old('name', $tag->name), ['class' => 'form-control', 'required' => true]) }}
                </div>

                <div class="form-group">
                    {{ Form::label('slug', __('Slug(URL Key)')) }}
                    {{ Form::text('slug', old('slug', $tag->slug), ['class' => 'form-control']) }}
                </div>

                <div class="form-group">
                    {{ Form::label('status', __('Status')) }}
                    {{ Form::select('status', [
                        0 => __('No'),
                        1 => __('Yes'),
                    ], old('status', $tag->status), ['placeholder' => __('Choose'), 'class' => 'form-control']) }}
                </div>

                <div class="form-group">
                    {{ Form::label('description', __('Description')) }}
                    {{ Form::textarea('description', old('description', $tag->description), ['class' => 'form-control', 'rows' => 3]) }}
                </div>

                <h3>{{ __('SEO') }}</h3>

                <div class="form-group">
                    {{ Form::label('seo[title]', __('SEO Title')) }}
                    {{ Form::text('seo[title]', old('seo[title]', $tag->seo['title'] ?? ''), ['class' => 'form-control']) }}
                </div>

                <div class="form-group">
                    {{ Form::label('seo[keywords]', __('SEO Keywords')) }}
                    {{ Form::text('seo[keywords]', old('seo[keywords]', $tag->seo['keywords'] ?? ''), ['class' => 'form-control']) }}
                </div>

                <div class="form-group">
                    {{ Form::label('seo[description]', __('SEO Description')) }}
                    {{ Form::textarea('seo[description]', old('seo[description]', $tag->seo['description'] ?? ''), ['class' => 'form-control', 'rows' => 3]) }}
                </div>

                <div class="form-group">
                    {{ Form::label('seo[share_image]', __('Share Image')) }}
                    {{ Form::text('seo[share_image]', old('seo[share_image]', $tag->seo['share_image'] ?? ''), ['class' => 'form-control']) }}
                </div>

                <div class="form-group">
                    {{ Form::submit(__('Submit'), ['class' => 'btn btn-primary']) }}
                </div>
            </div>
        </div>

    </form>

@endsection
