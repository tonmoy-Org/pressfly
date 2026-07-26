<?php
/** @var array $widget */

$widget = array_merge(
    [
        'title' => '',
        'username' => '',
        'twitter_api_key' => '',
        'twitter_api_secret_key' => '',
        'num' => 5,
        'class' => '',
    ],
    $widget
);

if (!$widget['twitter_api_key'] || !$widget['twitter_api_secret_key']) {
    return;
}

try {
    $tweets = [];

    $tweets = \Cache::remember('recent_tweets_' . $widget['id'], 3 * 60 * 60, function () use ($widget) {
        $tokenResponse = \Illuminate\Support\Facades\Http::asForm()
            ->withToken(
                base64_encode($widget['twitter_api_key'] . ':' . $widget['twitter_api_secret_key']),
                'Basic'
            )
            ->post('https://api.twitter.com/oauth2/token', ['grant_type' => 'client_credentials'])
            ->throw();

        $result_id = \Illuminate\Support\Facades\Http::withToken($tokenResponse->json('access_token'))
            ->get('https://api.twitter.com/2/users/by?usernames=' . $widget['username'])
            ->throw();

        // https://developer.twitter.com/en/docs/twitter-api/tweets/timelines/api-reference/get-users-id-tweets
        $result = \Illuminate\Support\Facades\Http::withToken($tokenResponse->json('access_token'))
            ->get(
                'https://api.twitter.com/2/users/' . $result_id->json('data')[0]['id'] . '/tweets',
                [
                    'max_results' => $widget['num'],
                    'tweet.fields' => 'created_at',
                ]
            )
            ->throw();

        return $result->object()?->data ?? [];
    });
} catch (\Illuminate\Http\Client\RequestException $exception) {
    //$errorData = $exception->response->json();
    //echo print_r($errorData, true);
} catch (\Exception $exception) {
    //echo $exception->getMessage();
}
?>
<div class="widget tweets {{ $widget['class'] }}">
    <div class="block-header">
        <div class="block-title"><span>{{ $widget['title'] }}</span></div>
    </div>
    <div class="block-content">
        @foreach($tweets as $tweet)
            <div class="block-item">
                <div class="block-content">
                    <i class="fab fa-twitter" style="color: #55ACEE;"></i> {{ $tweet->text }}
                </div>
                <div class="block-item-meta">
                    <small>
                        <a href="https://twitter.com/i/web/status/{{ $tweet->id }}" target="_blank"
                           rel="noopener noreferrer">
                            <i class="far fa-clock"></i> {{ display_date_timezone($tweet->created_at) }}
                        </a>
                    </small>
                </div>
            </div>
        @endforeach
    </div>
</div>
