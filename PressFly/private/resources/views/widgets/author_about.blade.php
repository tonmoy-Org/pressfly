<?php
/** @var array $widget */

$widget = array_merge(
    [
        'title' => '',
        'class' => '',
    ],
    $widget
);
?>

<?php
/**
 * @var \App\Models\Article $article
 */
$article = request()->route()->parameter('article');
?>
@if($article)
    <div class="widget author">
        <div class="block-header">
            <div class="block-title"><span>{{ $widget['title'] }}</span></div>
        </div>
        <div class="block-content">

            <div class="author-info">
                <div class="author-image">
                    <img alt="{{ $article->user->name }}" height="100" width="100" loading="lazy"
                         src="{{ $article->user->profileImage() }}">
                </div>
                <div class="author-connect">
                    <div class="author-name">
                        <a href="{{ $article->user->permalink() }}">{{ $article->user->name }}</a>
                    </div>
                    <div class="follow-me">
                        @if($article->user->socialNetwork('facebook'))
                            <a href="{{ $article->user->socialNetwork('facebook') }}" target="_blank"
                               title="{{ __('FaceBook') }}" class="fab fa-facebook-f fa-fw"
                               rel="noopener noreferrer"></a>
                        @endif

                        @if($article->user->socialNetwork('twitter'))
                            <a href="{{ $article->user->socialNetwork('twitter') }}" target="_blank"
                               title="{{ __('Twitter') }}" class="fab fa-twitter fa-fw"
                               rel="noopener noreferrer"></a>
                        @endif
                    </div>

                    <div class="author-follow">
                        @if(!auth()->check() || !$article->user->followers()->wherePivot('follower_id', auth()->user()->id)->exists())
                            <form method="post" action="{{ route('author.follow', [$article->user->username]) }}">
                                @csrf
                                <input type="submit" class="btn btn-primary btn-sm btn-follow"
                                       value="{{ __('Follow') }}">
                            </form>
                        @else
                            <form method="post" action="{{ route('author.unfollow', [$article->user->username]) }}">
                                @csrf
                                <input type="submit" class="btn btn-primary btn-sm btn-follow"
                                       value="{{ __('Unfollow') }}">
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="author-description">
                <p>{{ $article->user->description }}</p>
            </div>
        </div>
    </div>
@endif
