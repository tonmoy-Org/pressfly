<div class="block-item-meta">
    @if(in_array('hits', get_option('listing_meta_data', [])))
        <small data-toggle="tooltip" data-placement="top" title="{{ __('Views') }}">
            <i class="far fa-eye"></i> {{ display_number($article->hits) }} {{ __('Hits') }}
        </small>
    @endif
    @if(in_array('paid_views', get_option('listing_meta_data', [])))
        <small data-toggle="tooltip" data-placement="top" title="{{ __('Views') }}">
            <i class="far fa-eye"></i> {{ display_number($article->paidViews()) }} {{ __('Hits') }}
        </small>
    @endif
    @if(in_array('author', get_option('listing_meta_data', [])))
        <small data-toggle="tooltip" data-placement="top" title="{{ __('Author') }}">
            <i class="far fa-user"></i> {{ $article->user->name }}
        </small>
    @endif
    @if(in_array('published_date', get_option('listing_meta_data', [])))
        <small data-toggle="tooltip" data-placement="top" title="{{ __('Published on') }}">
            <i class="far fa-clock"></i> {{ display_date_timezone($article->published_at) }}
        </small>
    @endif
    @if(in_array('modified_date', get_option('listing_meta_data', [])))
        <small data-toggle="tooltip" data-placement="top" title="{{ __('Updated on') }}">
            <i class="far fa-edit"></i> {{ display_date_timezone($article->updated_at) }}
        </small>
    @endif
</div>
