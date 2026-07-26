<?php
/** @var array $widget */

$widget = array_merge(
    [
        'title' => '',
        'social_links' => [],
        'class' => '',
    ],
    $widget
);
?>

<div class="widget follow-us {{ $widget['class'] }}">
    <div class="block-header">
        <div class="block-title"><span>{{ $widget['title'] }}</span></div>
    </div>
    <div class="block-content">
        @foreach($widget['social_links'] as $social_link)
            @if($social_link['url'] && $social_link['icon'])
                <a href="{{ url($social_link['url']) }}" target="_blank" title="{{ $social_link['name'] }}"
                   class="{{ $social_link['icon'] }} fa-fw" rel="noopener noreferrer" style="background-color: {{ $social_link['color'] }}"></a>
            @endif
        @endforeach
    </div>
</div>
