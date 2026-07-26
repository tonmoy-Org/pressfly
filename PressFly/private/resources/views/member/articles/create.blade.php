@extends('layouts.member')

@section('title', __('Add Article'))

@section('content')

    <form method="post" action="{{ route('member.articles.store') }}" enctype="multipart/form-data"
          class="form-article">
        @csrf

        <div class="card bg-light">
            <div class="card-header">{{ __('Add Article') }}</div>
            <div class="card-body">

                <div class="form-group">
                    {{ Form::label('title', __('Title')) }}
                    {{ Form::text('title', old('title'), ['class' => 'form-control', 'required' => true, 'maxlength' => 190]) }}
                </div>

                <div class="form-group" style="display: none">
                    {{ Form::label('slug', __('Slug(URL Key)')) }}
                    {{ Form::text('slug', old('slug'), ['class' => 'form-control', 'maxlength' => 190]) }}
                </div>

                <div class="form-group">
                    {{ Form::label('category', __('Category')) }}
                    {{ Form::select('category', $categories, old('category'),
                        ['class' => 'form-control select2', 'required' => true]) }}
                </div>

                <div class="form-group">
                    {{ Form::label('summary', __('Summary')) }}
                    {{ Form::textarea('summary', old('summary'), ['class' => 'form-control', 'rows' => 3, 'required' => true]) }}
                </div>

                <div class="form-group">
                    {{ Form::label('content', __('Content')) }}
                    {{ Form::textarea('content', old('content'), ['class' => 'form-control text-editor']) }}
                </div>

                <div class="form-group">
                    {{ Form::file('upload_featured_image', ['accept' => image_upload_accept()]) }}
                </div>

                <div class="form-group">
                    {{ Form::label('reason', __('Message to the Reviewer')) }}
                    {{ Form::textarea('reason', old('reason'), ['class' => 'form-control', 'rows' => 5]) }}
                    <small class="form-text text-muted"><?= __(
                            'You must give a brief description of any changes you have made.'
                        ) ?></small>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" name="submit"
                            value="3">{{ __('Submit for review') }}</button>
                    <button type="submit" class="btn btn-danger" name="submit"
                            value="8">{{ __('Save draft') }}</button>
                </div>

            </div>
        </div>

    </form>

@endsection

@include('_partials.editor')
