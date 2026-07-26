<?php

namespace App\Helpers;

class Elements
{
    public static function apply(string $content): string
    {
        return \hooks()->do_shortcode($content);
    }

    public static function add(): void
    {
        /**
         * Article Blocks
         */
        \hooks()->add_shortcode('block1', [self::class, 'block1']);
        \hooks()->add_shortcode('block2', [self::class, 'block2']);
        \hooks()->add_shortcode('block3', [self::class, 'block3']);
        \hooks()->add_shortcode('block4', [self::class, 'block4']);
        \hooks()->add_shortcode('block5', [self::class, 'block5']);
        \hooks()->add_shortcode('block6', [self::class, 'block6']);
        \hooks()->add_shortcode('block7', [self::class, 'block7']);

        /**
         * Article Sliders
         */
        \hooks()->add_shortcode('slider1', [self::class, 'slider1']);

        /**
         * Article Grids
         */
        \hooks()->add_shortcode('grid1', [self::class, 'grid1']);
        \hooks()->add_shortcode('grid2', [self::class, 'grid2']);
        \hooks()->add_shortcode('grid3', [self::class, 'grid3']);

        /**
         * Ads
         */
        \hooks()->add_shortcode('ads', [self::class, 'ads']);

        /**
         * Social Embed
         */
        \hooks()->add_shortcode('embed_video', [self::class, 'embed_video']);
        \hooks()->add_shortcode('embed_audio', [self::class, 'embed_audio']);
        \hooks()->add_shortcode('embed_facebook', [self::class, 'embed_facebook']);
        \hooks()->add_shortcode('embed_twitter', [self::class, 'embed_twitter']);
        \hooks()->add_shortcode('embed_instagram', [self::class, 'embed_instagram']);
        \hooks()->add_shortcode('embed_pinterest', [self::class, 'embed_pinterest']);
        \hooks()->add_shortcode('embed_general', [self::class, 'embed_general']);
        \hooks()->add_shortcode('embed_github_gist', [self::class, 'embed_github_gist']);
    }

    public static function embed_video(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'type' => '',
            ],
            $attributes
        );

        if (empty($attributes['type'])) {
            return '';
        }

        if ($attributes['type'] === 'youtube') {
            $pattern = "/(https?:\/\/(www.|m.)?(youtube.com|youtu.be)\/(watch\?v=)?([a-zA-Z0-9\-_]+)(.*))/i";
            $replace = "<div class='embed-responsive embed-responsive-16by9'><iframe class='embed-responsive-item' loading='lazy' src='https://www.youtube.com/embed/$5' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe></div>";
            return \preg_replace($pattern, $replace, $content);
        }

        if ($attributes['type'] === 'vimeo') {
            $pattern = "/(https?:\/\/(www.)?vimeo.com\/([0-9]+)(.*))/i";
            $replace = "<div class='embed-responsive embed-responsive-16by9'><iframe class='embed-responsive-item' loading='lazy' src='https://player.vimeo.com/video/$3' frameborder='0' allow='autoplay; fullscreen; picture-in-picture' allowfullscreen></iframe></div>";
            return \preg_replace($pattern, $replace, $content);
        }

        if ($attributes['type'] === 'dailymotion') {
            $pattern = "/(https?:\/\/(www.)?(dailymotion.com|dai.ly)\/(video\/)?([a-zA-Z0-9\-_]+)(.*))/i";
            $replace = "<div class='embed-responsive embed-responsive-16by9'><iframe class='embed-responsive-item' loading='lazy' src='https://www.dailymotion.com/embed/video/$5' frameborder='0' allow='autoplay; fullscreen; picture-in-picture' allowfullscreen></iframe></div>";
            return \preg_replace($pattern, $replace, $content);
        }

        // https://www.tiktok.com/@omarmokbel/video/7005248403483495686
        if ($attributes['type'] === 'tiktok') {
            $pattern = "/(https?:\/\/(www.)?tiktok.com\/(.*)\/video\/([0-9]+)(.*))/i";
            return \preg_replace_callback(
                $pattern,
                function ($matches) {
                    return '<blockquote class="tiktok-embed" cite="' . $matches[1] . '" data-video-id="' . $matches[4] . '" style="max-width: 605px;min-width: 325px;" ><p>' . $matches[1] . '</p></blockquote>';
                    //'<script async src="https://www.tiktok.com/embed.js"></script>';
                },
                $content
            );
        }

        return '';
    }

    public static function embed_audio(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'type' => '',
            ],
            $attributes
        );

        if (empty($attributes['type'])) {
            return '';
        }

        if ($attributes['type'] === 'soundcloud') {
            $pattern = "/(https?:\/\/(www.)?soundcloud.com\/([a-zA-Z0-9\-_]+)\/([a-zA-Z0-9\-_]+)(.*))/i";
            return \preg_replace_callback(
                $pattern,
                function ($matches) {
                    return "<iframe width='100%' height='166' src='https://w.soundcloud.com/player/?visual=true&url=" .
                        urlencode($matches[1]) .
                        "&show_artwork=true' loading='lazy' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>";
                },
                $content
            );
        }

        return '';
    }

    /**
     * @see https://developers.facebook.com/docs/plugins/embedded-posts#add-code-manually
     */
    public static function embed_facebook(array $attributes = [], string $content = ''): string
    {
        //'<script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>';
        return '<div class="fb-post" data-href="' . e($content) . '" data-allowfullscreen="true" data-width="auto">' .
            e($content) . '</div>';
        /*
        $pattern = "/(https?:\/\/(www.|m.)?facebook.com\/([a-zA-Z0-9\-_\.]+)\/(posts|photos|videos)\/([a-zA-Z0-9\-_\.]+)(.*))/i";
        return \preg_replace_callback(
            $pattern,
            function ($matches) {
                return '<script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>' .
                    '<div class="fb-post" data-href="' . $matches[1] . '" data-allowfullscreen="true" data-width="auto"></div>';
            },
            $content
        );
        */
    }

    /**
     * @see https://developer.twitter.com/en/docs/twitter-for-websites/embedded-tweets/guides/embedded-tweet-parameter-reference
     */
    public static function embed_twitter(array $attributes = [], string $content = ''): string
    {
        return '<blockquote class="twitter-tweet" data-width="100%"><a href="' . e($content) . '">' .
            e($content) . '</a></blockquote>';
        //'<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>';
        /*
        $pattern = "/(https?:\/\/(www.)?twitter.com\/([a-zA-Z0-9\-_]+)\/status\/([0-9]+)(.*))/i";
        return \preg_replace_callback(
            $pattern,
            function ($matches) {
                return '<blockquote class="twitter-tweet" data-width="100%"><a href="' . $matches[1] . '">' . $matches[1] . '</a></blockquote>' .
                    '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>';
            },
            $content
        );
        */
    }

    public static function embed_instagram(array $attributes = [], string $content = ''): string
    {
        return '<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="' . e($content) .
            '" data-instgrm-version="14" style="max-width:540px; min-width:326px; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);">' .
            e($content) . '</blockquote>';
        //'<script async src="//www.instagram.com/embed.js"></script>';
        /*
        $pattern = "/(https?:\/\/(www.)?instagram.com\/p\/([a-zA-Z0-9\-_]+)(.*))/i";
        return \preg_replace_callback(
            $pattern,
            function ($matches) {
                return '<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="' .
                    e($matches[1]) .
                    '" data-instgrm-version="14" style="max-width:540px; min-width:326px; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);">' .
                    e($matches[1]) . '</blockquote> <script async src="//www.instagram.com/embed.js"></script>';
            },
            $content
        );
        */
    }

    /**
     * @see https://developers.pinterest.com/tools/widget-builder/?type=pin
     */
    public static function embed_pinterest(array $attributes = [], string $content = ''): string
    {
        return '<a data-pin-do="embedPin" data-pin-width="large" href="' . e($content) . '">' . e($content) . '</a>';
        //'<script async defer src="//assets.pinterest.com/js/pinit.js"></script>';
        /*
        $pattern = "/(https?:\/\/(www.)?(pinterest.com|pin.it)\/(pin\/)?([a-zA-Z0-9\-_]+)(.*))/i";
        return \preg_replace_callback(
            $pattern,
            function ($matches) {
                return '<a data-pin-do="embedPin" data-pin-width="large" href="' . $matches[1] . '"></a>' .
                    '<script async defer src="//assets.pinterest.com/js/pinit.js"></script>';
            },
            $content
        );
        */
    }

    public static function embed_github_gist(array $attributes = [], string $content = ''): string
    {
        if (empty($content)) {
            return '';
        }

        if (\filter_var($content, \FILTER_VALIDATE_URL) === false) {
            return '';
        }

        return '<script src="' . e($content) . '.js"></script>';
    }

    public static function embed_general(array $attributes = [], string $content = ''): string
    {
        if (empty($content)) {
            return '';
        }

        if (\filter_var($content, \FILTER_VALIDATE_URL) === false) {
            return '';
        }

        return '<a class="embedly-card" data-card-controls="0" href="' . e($content) . '">' . e($content) . '</a>';
        //'<script async src="//cdn.embedly.com/widgets/platform.js" charset="UTF-8"></script>';
        //return "<div class='embed-responsive embed-responsive-4by3'><iframe class='embed-responsive-item' loading='lazy' width='100%' height='500' src='https://oembed.link/$content' frameborder='0' allow='autoplay; fullscreen; picture-in-picture' allowfullscreen></iframe></div>";
    }

    /**
     * @throws \Throwable
     */
    public static function grid1(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'block_title' => 'no',
                'type' => 'recent', // recent, popular
                'cats' => '',
                'filter' => 'no',
                'tags' => '',
                'per_page' => 4,
                'order_by' => 'published_at',
                'order' => 'desc',
                'page' => '1',

                'summary_length' => '',

                'title' => '',
                'spinner_type' => '',
                'pagination' => 'numeric', // next-prev numeric
                'spinner' => 'spinner-4',

            ],
            $attributes
        );

        return view('elements.grid1', [
            'attributes' => $attributes,
            'content' => $content,
        ])->render();
    }

    /**
     * @throws \Throwable
     */
    public static function grid2(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'type' => 'recent', // recent, popular
                'cats' => '',
                'filter' => 'no',
                'tags' => '',
                'per_page' => 5,
                'order_by' => 'published_at',
                'order' => 'desc',
                'page' => '1',

                'summary_length' => '',

                'title' => '',
                'spinner_type' => '',
                'pagination' => 'numeric', // next-prev numeric
                'spinner' => 'spinner-4',

            ],
            $attributes
        );

        return view('elements.grid2', [
            'attributes' => $attributes,
            'content' => $content,
        ])->render();
    }

    /**
     * @throws \Throwable
     */
    public static function grid3(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'type' => 'recent', // recent, popular
                'cats' => '',
                'filter' => 'no',
                'tags' => '',
                'per_page' => 4,
                'order_by' => 'published_at',
                'order' => 'desc',
                'page' => '1',

                'summary_length' => '',

                'title' => '',
                'spinner_type' => '',
                'pagination' => 'numeric', // next-prev numeric
                'spinner' => 'spinner-4',

            ],
            $attributes
        );

        return view('elements.grid3', [
            'attributes' => $attributes,
            'content' => $content,
        ])->render();
    }

    /**
     * @throws \Throwable
     */
    public static function block1(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'type' => 'recent', // recent, popular
                'cats' => '',
                'filter' => 'no',
                'tags' => '',
                'per_page' => 6,
                'order_by' => 'published_at',
                'order' => 'desc',
                'page' => '1',

                'summary_length' => '',

                'title' => '',
                'spinner_type' => '',
                'pagination' => 'numeric', // next-prev numeric
                'spinner' => 'spinner-4',

            ],
            $attributes
        );

        return view('elements.block1', [
            'attributes' => $attributes,
            'content' => $content,
        ])->render();
    }

    /**
     * @throws \Throwable
     */
    public static function block2(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'type' => 'recent', // recent, popular
                'cats' => '',
                'filter' => 'no',
                'tags' => '',
                'per_page' => 7,
                'order_by' => 'published_at',
                'order' => 'desc',
                'page' => '1',

                'summary_length' => '',

                'title' => '',
                'spinner_type' => '',
                'pagination' => 'numeric', // next-prev numeric
                'spinner' => 'spinner-4',

            ],
            $attributes
        );

        return view('elements.block2', [
            'attributes' => $attributes,
            'content' => $content,
        ])->render();
    }

    /**
     * @throws \Throwable
     */
    public static function block3(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'type' => 'recent', // recent, popular
                'cats' => '',
                'filter' => 'no',
                'tags' => '',
                'per_page' => 5,
                'order_by' => 'published_at',
                'order' => 'desc',
                'page' => '1',

                'summary_length' => '',

                'title' => '',
                'spinner_type' => '',
                'pagination' => 'numeric', // next-prev numeric
                'spinner' => 'spinner-4',

            ],
            $attributes
        );

        return view('elements.block3', [
            'attributes' => $attributes,
            'content' => $content,
        ])->render();
    }

    /**
     * @throws \Throwable
     */
    public static function block4(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'type' => 'recent', // recent, popular
                'cats' => '',
                'filter' => 'no',
                'tags' => '',
                'per_page' => 6,
                'order_by' => 'published_at',
                'order' => 'desc',
                'page' => '1',

                'summary_length' => '',

                'title' => '',
                'spinner_type' => '',
                'pagination' => 'numeric', // next-prev numeric
                'spinner' => 'spinner-4',

            ],
            $attributes
        );

        return view('elements.block4', [
            'attributes' => $attributes,
            'content' => $content,
        ])->render();
    }

    /**
     * @throws \Throwable
     */
    public static function block5(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'type' => 'recent', // recent, popular
                'cats' => '',
                'filter' => 'no',
                'tags' => '',
                'per_page' => 5,
                'order_by' => 'published_at',
                'order' => 'desc',
                'page' => '1',

                'summary_length' => '',

                'title' => '',
                'spinner_type' => '',
                'pagination' => 'numeric', // next-prev numeric
                'spinner' => 'spinner-4',

            ],
            $attributes
        );

        return view('elements.block5', [
            'attributes' => $attributes,
            'content' => $content,
        ])->render();
    }

    /**
     * @throws \Throwable
     */
    public static function block6(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'type' => 'recent', // recent, popular
                'cats' => '',
                'filter' => 'no',
                'tags' => '',
                'per_page' => 5,
                'order_by' => 'published_at',
                'order' => 'desc',
                'page' => '1',

                'summary_length' => '',

                'title' => '',
                'spinner_type' => '',
                'pagination' => 'numeric', // next-prev numeric
                'spinner' => 'spinner-4',

            ],
            $attributes
        );

        return view('elements.block6', [
            'attributes' => $attributes,
            'content' => $content,
        ])->render();
    }

    /**
     * @throws \Throwable
     */
    public static function block7(array $attributes = [], string $content = ''): string
    {
        $attributes = self::elementAttributes(
            [
                'type' => 'recent', // recent, popular
                'cats' => '',
                'filter' => 'no',
                'tags' => '',
                'per_page' => 5,
                'order_by' => 'published_at',
                'order' => 'desc',
                'page' => '1',

                'summary_length' => '',

                'title' => '',
                'spinner_type' => '',
                'pagination' => 'numeric', // next-prev numeric
                'spinner' => 'spinner-4',

            ],
            $attributes
        );

        return view('elements.block7', [
            'attributes' => $attributes,
            'content' => $content,
        ])->render();
    }

    /**
     * @throws \Throwable
     */
    public static function slider1(array $attributes = [], string $content = ''): string
    {
        return view('elements.slider1', [
            'attributes' => $attributes,
            'content' => $content,
        ])->render();
    }

    public static function ads(array $attributes = []): string
    {
        $attributes = self::elementAttributes(
            [
                'id' => '',
            ],
            $attributes
        );

        $id = (int)$attributes['id'];

        if (!$id) {
            return '';
        }

        $ad = \App\Models\Ad::find($id);

        if (!$ad) {
            return '';
        }

        if ($ad->status !== 1) {
            return '';
        }

        $style = '';
        if (!empty($ad->size['width']) && !empty($ad->size['height'])) {
            $style = 'style="';
            $style .= 'width: ' . $ad->size['width'] . 'px; ';
            //$style .= 'height: ' . $ad->size['height'] . 'px; ';
            $style .= 'height: auto; ';
            $style .= 'max-width: 100%; ';
            $style .= 'aspect-ratio: ' . $ad->size['width'] . ' / ' . $ad->size['height'] . ';';
            $style .= '"';
        }

        if ((bool)get_option('ads_protector', 0) === false) {
            return '<div id="ad-' . $id . '" class="ad-element"><div class="ad-inner" ' . $style . '>' . $ad->code . '</div></div>';
        }

        if (!request()->session()->has('VisitorStatus')) {
            return '<div id="ad-' . $id . '" data-id="' . $id . '" data-code="' . base64_encode($ad->code) .
                '" class="ad-element"><div class="ad-inner" ' . $style . '></div></div>';
        }

        if (request()->session()->get('VisitorStatus') === 0) {
            return '';
        }

        return '<div id="ad-' . $id . '" class="ad-element"><div class="ad-inner" ' . $style . '>' . $ad->code . '</div></div>';
    }

    /**
     * Combine user attributes with known attributes and fill in defaults when needed.
     *
     * The pairs should be considered to be all of the attributes which are
     * supported by the caller and given as a list. The returned attributes will
     * only contain the attributes in the $pairs list.
     *
     * If the $atts list has unsupported attributes, then they will be ignored and
     * removed from the final returned list.
     *
     * @see https://github.com/WordPress/WordPress/blob/4.9.8/wp-includes/shortcodes.php#L533
     *
     * @param array $pairs Entire list of supported attributes and their defaults.
     * @param array $atts User defined attributes in shortcode tag.
     *
     * @return array Combined and filtered attribute list.
     */
    public static function elementAttributes(array $pairs, array $attributes = []): array
    {
        $out = [];
        foreach ($pairs as $name => $default) {
            if (array_key_exists($name, $attributes)) {
                $out[$name] = $attributes[$name];
            } else {
                $out[$name] = $default;
            }
        }

        return $out;
    }
}
