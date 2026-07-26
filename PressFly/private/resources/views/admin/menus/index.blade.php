<?php
/**
 * @var \Illuminate\Database\Eloquent\Builder|\App\Models\Menu[] $menus
 */
?>

@extends('layouts.admin')

@section('title', __('Manage Menus'))

@section('content')

    <div class="card border">
        <div class="card-body">
            <form method="get" action="{{ route('admin.menus.index') }}" class="form-inline float-left">
                <div class="form-group">
                    <label for="menu_id">{{ __('Menu Select') }}</label>
                    <select name="menu_id" id="menu_id" required onchange="this.form.submit()"
                            class="form-control">
                        <option value="">{{ __('Choose') }}</option>
                        @foreach($menus as $key=>$val)
                            <option
                                value="{{ $key }}" {{ (($key == old('menu_id', $menu_id))? "selected":"") }}>{{$val}}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <div class="float-right">
                <button class="btn btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#menu-new">
                    {{ __('Add new menu') }}
                </button>
            </div>

            <div class="modal fade" id="menu-new" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Add New Menu') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{ route('admin.menus.create') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="name">{{ __('Menu Name') }}</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                           value="{{ old('name') }}" required>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @if($menu)
        <div class="row">
            <div class="col-sm-4">
                <div class="card card-primary card-outline">
                    <?php
                    $menu_types = \App\Models\Menu::itemTypes();
                    ?>
                    <div class="card-header">{{ __('Add new menu item') }}</div>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.menu.item.add') }}" class="form-menu-item">
                            @csrf
                            <input type="hidden" name="menu_id" value="{{ $menu_id }}">

                            <div class="form-group">
                                <label for="title">{{ __('Title') }}</label>
                                <input type="text" name="title" id="title" class="form-control"
                                       value="{{ old('title') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="type">{{ __('Type') }}</label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="">{{ __('Choose') }}</option>
                                    @foreach($menu_types as $key=>$val)
                                        <option
                                            value="{{ $key }}" {{ (($key == old('type'))? "selected":"") }}>{{$val}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn btn-primary">{{ __('Add') }}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <form name="{{$form = uniqid('form_')}}" style="display:none;" method="post"
                              action="{{route('admin.menus.destroy', [$menu->id])}}">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                        </form>

                        <form method="post" action="{{ route('admin.menus.edit', [$menu->id]) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group form-inline">
                                <label for="name">{{ __('Menu Name') }}</label>
                                <input type="text" name="name" id="name" class="form-control"
                                       value="{{ old('name', $menu->name) }}" required>
                            </div>

                            <div class="dd">
                                <ol class="dd-list">
                                    @foreach($menu->items as $menuItem)
                                        <li class="dd-item" data-id="{{ $menuItem['id'] }}">
                                            <div class="dd-handle dd3-handle"></div>
                                            <div class="dd3-content">
                                                {{ $menuItem['title'] }}
                                                <a data-toggle="collapse" href="#menu-item2-{{ $menuItem['id'] }}"
                                                   class="btn btn-link btn btn-sm p-0 d-block float-right">
                                                    {{ __('Edit') }}
                                                </a>
                                                <div id="menu-item2-{{ $menuItem['id'] }}" class="collapse"
                                                     style="margin-top: 20px;">
                                                    <input type="hidden" name="item[{{ $menuItem['id'] }}][id]"
                                                           value="{{ old('item['.$menuItem['id'].']id', $menuItem['id']) }}">

                                                    <input type="hidden" name="item[{{ $menuItem['id'] }}][parent]"
                                                           value="{{ old('item['.$menuItem['id'].']parent', $menuItem['parent'] ?? '') }}">

                                                    <input type="hidden" name="item[{{ $menuItem['id'] }}][type]"
                                                           value="{{ old('item['.$menuItem['id'].']type', $menuItem['type']) }}">

                                                    <input type="hidden" name="item[{{ $menuItem['id'] }}][position]"
                                                           value="{{ old('item['.$menuItem['id'].']position', $menuItem['position']) }}">

                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label">{{ __('Title') }}</label>
                                                        <input type="text" name="item[{{ $menuItem['id'] }}][title]"
                                                               class="form-control col-sm-10" required
                                                               value="{{ old('item['.$menuItem['id'].']title', ($menuItem['title'] ?? '') ) }}">
                                                    </div>

                                                    <div class="type-data">
                                                        @if($menuItem['type'] === 'link')
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-sm-2 col-form-label">{{ __('Link') }}</label>
                                                                <input type="text"
                                                                       name="item[{{ $menuItem['id'] }}][link]"
                                                                       class="form-control col-sm-10"
                                                                       value="{{ old('item['.$menuItem['id'].']link', ($menuItem['link'] ?? '') ) }}">
                                                            </div>
                                                        @endif

                                                        @if($menuItem['type'] === 'page')
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-sm-2 col-form-label">{{ __('Pages') }}</label>
                                                                <select name="item[{{ $menuItem['id'] }}][page]"
                                                                        class="form-control col-sm-10">
                                                                    <option value="">{{ __('Choose') }}</option>
                                                                    @foreach($pages as $key=>$val)
                                                                        <option
                                                                            value="{{ $key }}" {{ (($key == ($menuItem['page'] ?? '') ) ? "selected":"") }}>{{$val}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endif

                                                        @if($menuItem['type'] === 'category')
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-sm-2 col-form-label">{{ __('Categories') }}</label>
                                                                <select name="item[{{ $menuItem['id'] }}][category]"
                                                                        class="form-control col-sm-10">
                                                                    <option value="">{{ __('Choose') }}</option>
                                                                    @foreach($categories as $key=>$val)
                                                                        <option
                                                                            value="{{ $key }}" {{ (($key == ($menuItem['category'] ?? ''))? "selected":"") }}>{{$val}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endif

                                                        @if($menuItem['type'] === 'tag')
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-sm-2 col-form-label">{{ __('Tags') }}</label>
                                                                <select name="item[{{ $menuItem['id'] }}][tag]"
                                                                        class="form-control col-sm-10">
                                                                    <option value="">{{ __('Choose') }}</option>
                                                                    @foreach($tags as $key=>$val)
                                                                        <option
                                                                            value="{{ $key }}" {{ (($key == ($menuItem['tag'] ?? ''))? "selected":"") }}>{{$val}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endif

                                                        @if($menuItem['type'] === 'author')
                                                            <div class="form-group row">
                                                                <label
                                                                    class="col-sm-2 col-form-label">{{ __('Authors') }}</label>
                                                                <select name="item[{{ $menuItem['id'] }}][author]"
                                                                        class="form-control col-sm-10">
                                                                    <option value="">{{ __('Choose') }}</option>
                                                                    @foreach($authors as $key=>$val)
                                                                        <option
                                                                            value="{{ $key }}" {{ (($key == ($menuItem['author'] ?? ''))? "selected":"") }}>{{$val}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="form-group row">
                                                        <?php
                                                        $menuItem['visibility'] = $menuItem['visibility'] ?? 'all';
                                                        ?>
                                                        <label
                                                            class="col-sm-2 col-form-label">{{ __('Visibility') }}</label>
                                                        <select class="form-control col-sm-10"
                                                                name="item[{{ $menuItem['id'] }}][visibility]">
                                                            <option
                                                                value="guest" {{ (('guest' == $menuItem['visibility'])? "selected":"") }}>{{ __('Only Guest') }}</option>
                                                            <option
                                                                value="logged" {{ (('logged' == $menuItem['visibility'])? "selected":"") }}>{{ __('Only Logged In') }}</option>
                                                            <option
                                                                value="admin" {{ (('admin' == $menuItem['visibility'])? "selected":"") }}>{{ __('Only Logged In Admins') }}</option>
                                                            <option
                                                                value="all" {{ (('all' == $menuItem['visibility'])? "selected":"") }}>{{ __('All') }}</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label
                                                            class="col-sm-2 col-form-label">{{ __('CSS Class') }}</label>
                                                        <input type="text" name="item[{{ $menuItem['id'] }}][class]"
                                                               class="form-control col-sm-10"
                                                               value="{{ old('item['.$menuItem['id'].']class', ($menuItem['class'] ?? '') ) }}">
                                                    </div>

                                                    <div class="form-group">
                                                        <a href="#"
                                                           class="item-delete btn btn-danger btn-sm float-right">
                                                            {{ __('Delete') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(isset($menuItem['children']) && count($menuItem['children']))
                                            @endif
                                        </li>
                                    @endforeach
                                </ol>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>

                                <a href="#" class="btn btn-danger float-right"
                                   onclick="if (confirm(&quot;Are you sure?&quot;)) { document.{{$form}}.submit(); } event.returnValue = false; return false;">
                                    {{ __('Delete') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('header')
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/nestable2@1.6.0/dist/jquery.nestable.min.css">
    <style>
        /**
         * Nestable Draggable Handles
         */
        .dd {
            max-width: 100%;
        }

        .dd3-content {
            display: block;
            margin: 5px 0;
            padding: 5px 10px 5px 40px;
            color: #333;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid #ccc;
            background: #fafafa;
            background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
            background: linear-gradient(top, #fafafa 0%, #eee 100%);
            -webkit-border-radius: 3px;
            border-radius: 3px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            overflow: auto;
        }

        .dd3-content:hover {
            color: #2ea8e5;
            background: #fff;
        }

        .dd-dragel > .dd3-item > .dd3-content {
            margin: 0;
        }

        .dd3-item > button {
            margin-left: 30px;
        }

        .dd3-handle {
            position: absolute;
            margin: 0;
            left: 0;
            top: 0;
            cursor: pointer;
            width: 30px;
            height: 100%;
            text-indent: 30px;
            white-space: nowrap;
            overflow: hidden;
            border: 1px solid #aaa;
            background: #ddd;
            background: -webkit-linear-gradient(top, #ddd 0%, #bbb 100%);
            background: -moz-linear-gradient(top, #ddd 0%, #bbb 100%);
            background: linear-gradient(top, #ddd 0%, #bbb 100%);
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .dd3-handle:before {
            content: '≡';
            display: block;
            position: absolute;
            left: 0;
            top: 3px;
            width: 100%;
            text-align: center;
            text-indent: 0;
            color: #fff;
            font-size: 20px;
            font-weight: normal;
        }

        .dd3-handle:hover {
            background: #ddd;
        }
    </style>
@endpush

@push('footer')
    <script src="https://fastly.jsdelivr.net/npm/nestable2@1.6.0/dist/jquery.nestable.min.js"></script>
    <script>
        $('.dd').nestable({
            maxDepth: 1,
            //includeContent: true,
            callback: function (l, e) {
                // l is the main container
                // e is the element that was moved

                widgetPositionsCorrect();

                var elementId = $(e).data('id');
                if ($(e).parents('li.dd-item').length > 0) {
                    var parentNode = $(e).parents('li.dd-item');
                    var parentId = parentNode.data('id');

                    console.log(elementId);
                    console.log(parentId);

                    $(e).find('input[name="item[' + elementId + '][parent]"]').val(parentId);
                } else {
                    $(e).find('input[name="item[' + elementId + '][parent]"]').val('');
                }
            }
        });

        $('.item-delete').on('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure?')) {
                //$(this).closest('li[data-id]').remove();
                var elementId = $(this).parents('li.dd-item').data('id');
                $('.dd').nestable('remove', elementId);
                widgetPositionsCorrect()
            }
            e.returnValue = false;
            return false;
        });

        function widgetPositionsCorrect() {
            $('ol.dd-list > li').each(function (index, element) {
                $(this).find('input[name="item[' + $(this).attr('data-id') + '][position]"]').val(index + 1);
            });
        }
    </script>
@endpush
