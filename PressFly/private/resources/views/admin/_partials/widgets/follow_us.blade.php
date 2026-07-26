<?php
/** @var array $widget */

$widget = array_merge(
    [
        'title' => '',
        'social_links' => [],
        'facebook' => '',
        'twitter' => '',
        'google_plus' => '',
        'youtube' => '',
        'pinterest' => '',
        'instagram' => '',
        'vk' => '',
        'class' => '',
    ],
    $widget
);
?>

<div class="form-group">
    <label>{{ __('Title') }}</label>
    <input type="text" name="item[{{ $widget['id'] }}][title]" class="form-control"
           value="{{ old('item['.$widget['id'].']title', $widget['title']) }}">
</div>

<p>We only support FontAwesome icons and you can find a list of all icons at
    <a target="_blank" href="https://fontawesome.com/v5.15/icons?d=gallery&p=2&m=free">https://fontawesome.com/v5.15/icons?d=gallery&p=2&m=free</a>
</p>

<textarea id="template_social_link_{{ $widget['id'] }}" style="display: none;"
          readonly><li class="ui-state-default list-group-item">
            <div class="form-inline">
                <i class="fas fa-exchange-alt fa-rotate-90"></i>

                <div class="form-group">
                    <input type="text" class="form-control" placeholder="{{ __('Name') }}"
                           name="item[{{ $widget['id'] }}][social_links][{SOCIAL_KEY}][name]"
                           value="">
                </div>

                <div class="form-group">
                    <input type="text" class="form-control" placeholder="{{ __('Icon') }}"
                           name="item[{{ $widget['id'] }}][social_links][{SOCIAL_KEY}][icon]"
                           value="">
                </div>

                <div class="form-group">
                    <input type="text" class="form-control" placeholder="{{ __('URL') }}"
                           name="item[{{ $widget['id'] }}][social_links][{SOCIAL_KEY}][url]"
                           value="">
                </div>

                <div class="form-group">
                    <input type="text" class="form-control" placeholder="{{ __('Color') }}"
                           name="item[{{ $widget['id'] }}][social_links][{SOCIAL_KEY}][color]"
                           value="">
                </div>

                <a href="#" class="btn btn-danger btn btn-sm float-right social-delete">
                    <i class="fas fa-trash"></i>
                </a>
            </div>
        </li></textarea>

<a href="#" class="social-add-{{ $widget['id'] }} btn btn-info">Add new item</a>

<ul id="widget-sortable-{{ $widget['id'] }}" class="list-group mb-3">
    @foreach($widget['social_links'] ?? [] as $key => $social_link)
        <li class="ui-state-default list-group-item">
            <div class="form-inline">
                <i class="fas fa-exchange-alt fa-rotate-90"></i>

                <div class="form-group">
                    <input type="text" class="form-control" placeholder="{{ __('Name') }}"
                           name="item[{{ $widget['id'] }}][social_links][{{ $key }}][name]"
                           value="{{ old("item[{$widget['id']}][social_links][{$key}][name]", $social_link['name']) }}">
                </div>


                <div class="form-group">
                    <input type="text" class="form-control" placeholder="{{ __('Icon') }}"
                           name="item[{{ $widget['id'] }}][social_links][{{ $key }}][icon]"
                           value="{{ old("item[{$widget['id']}][social_links][{$key}][icon]", $social_link['icon']) }}">
                </div>

                <div class="form-group">
                    <input type="text" class="form-control" placeholder="{{ __('URL') }}"
                           name="item[{{ $widget['id'] }}][social_links][{{ $key }}][url]"
                           value="{{ old("item[{$widget['id']}][social_links][{$key}][url]", $social_link['url']) }}">
                </div>

                <div class="form-group">
                    <input type="text" class="form-control" placeholder="{{ __('Color') }}"
                           name="item[{{ $widget['id'] }}][social_links][{{ $key }}][color]"
                           value="{{ old("item[{$widget['id']}][social_links][{$key}][color]", $social_link['color']) }}">
                </div>

                <a href="#" class="btn btn-danger btn btn-sm float-right social-delete">
                    <i class="fas fa-trash"></i>
                </a>
            </div>
        </li>
    @endforeach
</ul>

<div class="form-group">
    <label>{{ __('CSS Class') }}</label>
    <input type="text" name="item[{{ $widget['id'] }}][class]" class="form-control"
           value="{{ old('item['.$widget['id'].']class', $widget['class']) }}">
</div>

@push('footer')
    <style>
        .list-group-item {
            cursor: grabbing;
        }

        .list-group-item .form-inline > i {
            margin-right: 15px;
        }
    </style>
    <script>
        $(function () {
            $("#widget-sortable-{{ $widget['id'] }}").sortable({
                //placeholder: "ui-state-highlight",
                items: "> li",
                cursor: 'move',
                opacity: 0.6,
                update: function (event, ui) {
                }
            }).disableSelection();
        });

        $(document).on('click', '.social-add-{{ $widget['id'] }}', function (e) {
            e.preventDefault();

            var count = $('#widget-sortable-{{ $widget['id'] }} > li.list-group-item').length;

            var template = $('#template_social_link_{{ $widget['id'] }}').val().replaceAll('{SOCIAL_KEY}', count);

            $("#widget-sortable-{{ $widget['id'] }}").append(template);
        });

        $(document).on('click', '#widget-sortable-{{ $widget['id'] }} .social-delete', function (e) {
            e.preventDefault();
            if (confirm('Are you sure?')) {
                $(this).closest('li.list-group-item').remove();
            }
            e.returnValue = false;
            return false;
        });
    </script>
@endpush
