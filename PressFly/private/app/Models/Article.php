<?php

namespace App\Models;

use App\Helpers\Image;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use ModelTrait;

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @see https://laravel.com/docs/master/eloquent-mutators#attribute-casting
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'pay_type' => 'integer',
        'price' => 'double',
        'paid' => 'boolean',
        'disable_earnings' => 'boolean',
        'status' => 'integer',
        'hits' => 'integer',
        'tmp_content' => 'object',
        'seo' => 'array',
        'published_at' => 'datetime',
    ];

    protected $perPage = 20;

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(File::class, 'featured_image_id')->withDefault();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withPivot('main');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function activeComments()
    {
        return $this->comments()->with('user')->where('status', 1)->orderBy('id')->get();
    }

    public function statistics(): HasMany
    {
        return $this->hasMany(Statistic::class);
    }

    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'article_id', 'user_id');
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes', 'article_id', 'user_id');
    }

    public function paidViews(): int
    {
        return $this->statistics()->where('statistics.reason', 1)->count();
    }

    public function mainCategory()
    {
        return $this->belongsToMany(Category::class)->wherePivot('main', 1);
        //return $this->belongsTo(Category::class, 'main_category_id')->withDefault();
    }

    /**
     * @return \App\Models\Category
     */
    public function getMainCategory()
    {
        if ($this->mainCategory->first()) {
            return $this->mainCategory->first();
        }

        return (new Category);
    }

    /**
     * @return Article[]|\Illuminate\Database\Eloquent\Builder[]|null
     */
    public function relatedArticles(int $count = 6)
    {
        $tags = $this->tags->pluck('id')->toArray();
        $tags = array_filter($tags);

        if (empty($tags)) {
            return null;
        }

        $articles = self::query()
            ->with(['user', 'featuredImage', 'mainCategory'])
            ->whereHas('tags', function ($query) use ($tags) {
                /**
                 * @var \Illuminate\Database\Eloquent\Builder $query
                 */
                $query->whereIn('id', $tags);
                $query->where('status', 1);
            }
            )
            ->where('id', '!=', $this->id)
            ->orderBy('published_at', 'desc')
            ->whereIn('status', [1, 4])
            ->limit($count)
            ->get();

        return $articles;
    }

    /**
     * @param array $args
     *      'cats'     => '',
     *      'tags'     => '',
     *      'per_page' => 6,
     *      'order_by' => 'published_at',
     *      'order'    => 'desc',
     *      'page'     => '',
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\App\Models\Article[]
     */
    public static function getArticles(array $args = [])
    {
        $type = $args['type'];

        if ($type === 'recent') {
            $query = self::query()
                ->has('mainCategory')
                ->with(['user', 'featuredImage', 'mainCategory'])
                ->whereIn('status', [1, 4])
                ->when((isset($args['cats']) && trim($args['cats'])), function ($query) use ($args) {
                    return $query->whereHas('categories', function ($query) use ($args) {
                        /**
                         * @var \Illuminate\Database\Eloquent\Builder $query
                         */
                        $cats = array_map(
                            function ($value) {
                                return (int)trim($value);
                            },
                            explode(',', $args['cats'])
                        );

                        $cats = array_filter($cats);

                        if (!empty($cats)) {
                            $query->whereIn('categories.id', $cats);
                            $query->where('categories.status', 1);
                        }
                    }
                    );
                })
                ->when((isset($args['tags']) && trim($args['tags'])), function ($query) use ($args) {
                    return $query->whereHas('tags', function ($query) use ($args) {
                        /**
                         * @var \Illuminate\Database\Eloquent\Builder $query
                         */
                        $tags = array_map(
                            function ($value) {
                                return (int)trim($value);
                            },
                            explode(',', $args['tags'])
                        );

                        $tags = array_filter($tags);

                        if (!empty($tags)) {
                            $query->whereIn('tags.id', $tags);
                            $query->where('tags.status', 1);
                        }
                    }
                    );
                })
                ->orderBy($args['order_by'], $args['order'])
                ->paginate($args['per_page']);

            return $query;
        }

        if ($type === 'popular') {
            $query = self::query()
                ->has('mainCategory')
                ->with(['user', 'featuredImage', 'mainCategory'])
                ->whereIn('status', [1, 4])
                ->withCount('statistics')
                ->when((isset($args['cats']) && trim($args['cats'])), function ($query) use ($args) {
                    return $query->whereHas('categories', function ($query) use ($args) {
                        /**
                         * @var \Illuminate\Database\Eloquent\Builder $query
                         */
                        $cats = array_map(
                            function ($value) {
                                return (int)trim($value);
                            },
                            explode(',', $args['cats'])
                        );

                        $cats = array_filter($cats);

                        if (!empty($cats)) {
                            $query->whereIn('categories.id', $cats);
                            $query->where('categories.status', 1);
                        }
                    }
                    );
                })
                ->when((isset($args['tags']) && trim($args['tags'])), function ($query) use ($args) {
                    return $query->whereHas('tags', function ($query) use ($args) {
                        /**
                         * @var \Illuminate\Database\Eloquent\Builder $query
                         */
                        $tags = array_map(
                            function ($value) {
                                return (int)trim($value);
                            },
                            explode(',', $args['tags'])
                        );

                        $tags = array_filter($tags);

                        if (!empty($tags)) {
                            $query->whereIn('tags.id', $tags);
                            $query->where('tags.status', 1);
                        }
                    }
                    );
                })
                ->orderByDesc('statistics_count')
                ->paginate($args['per_page']);

            return $query;
        }
    }

    public function permalink()
    {
        return route('article.show', ['slug' => $this->slug, 'article' => $this->id]);
        //return url("/{$this->slug}-{$this->id}") ;
    }

    public function getFinalContent()
    {
        static $finalContent = null;

        $content = $this->content;

        if (!$finalContent) {
            $content = \applyShortCodes($this->insertAdAfterParagraph($content));
            return $this->adlinkflyIntegration($content);
        }

        return $finalContent;
    }

    protected function insertAdAfterParagraph($content)
    {
        $article_ads = get_style('ads_article_paragraphs', []);

        $ads = [];

        if (count($article_ads)) {
            foreach ($article_ads as $article_ad) {
                $p = (int)$article_ad['p'];
                $code = (int)$article_ad['code'];

                if ($p > 0 && $code > 0) {
                    $ads[] = [
                        'p' => $p,
                        'code' => \App\Helpers\Elements::ads(['id' => $code]),
                    ];
                }
            }
        }

        if (!count($ads)) {
            return $content;
        }

        $closing_p = '</p>';
        $paragraphs = explode($closing_p, $content);
        foreach ($paragraphs as $index => $paragraph) {
            $paragraphs[$index] .= $closing_p;

            foreach ($ads as $ad) {
                if ($ad['p'] === $index + 1) {
                    $paragraphs[$index] .= $ad['code'];
                }
            }
            /*
            if ($paragraph_id === $index + 1) {
                $paragraphs[$index] .= $insertion;
            }
            */
        }

        return implode('', $paragraphs);
    }

    protected function adlinkflyIntegration($content): string
    {
        if (\request()->route()->getName() !== 'article.show') {
            return $content;
        }

        if (!empty($_GET['fbclid2'])) {
            try {
                $time_out = 5;
                $counter = get_option('adlinkfly_counter', 5);

                $data = json_decode(adlinkfly_decrypt(rawurldecode($_GET['fbclid2'])), true);

                if (time() - $data['time'] > $time_out * 60) {
                    throw new \Exception();
                }

                $send = json_encode(
                    [
                        'short' => $data['short'],
                        'alias' => $data['alias'],
                        'time' => time(),
                    ]
                );
                $send = adlinkfly_encrypt($send);

                $before = '<style>.alf_button {font-size: 18px;border-color: #000000;background-color: #000000;color: #ffffff !important;padding: 10px 15px;}</style>';
                $before .= '<div style="text-align: center;margin-bottom: 30px;"><span class="alf_button">' .
                    __('Scroll down to get the link')
                    . '</span></div>';

                $after = '';
                //$referer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : 'empty';
                //$after   .= "<p><b>HTTP_REFERER</b>: {$referer}</p>";
                $after .= "
<form method='post' action='" . route('api.adlinkfly.article.out') . "' target=''>
    " . csrf_field() . "
    <input type='hidden' name='data' value='{$send}'>
    <div style='text-align: center;margin-bottom: 30px;'>
        <button id='alf_continue' class='alf_button' disabled>
            " . sprintf(__('Please wait for %s seconds'), "<span id='alf_counter'>{$counter}</span>") . "
        </button>
    </div>
</form>
";
                $after .= $this->js_code();

                return $before . $content . $after;
            } catch (\Exception $exception) {
            }
        }

        return $content;
    }

    protected function js_code(): string
    {
        $counter = get_option('adlinkfly_counter', 5);
        $continue = __('Click here to continue');

        return "
<script type='text/javascript'>
    document.addEventListener('DOMContentLoaded', (event) => {
        var alf_continue = document.getElementById('alf_continue');
        var alf_counter = document.getElementById('alf_counter');
        var counter_seconds = {$counter};

        window.setTimeout(function () {
            var time = counter_seconds * 1000,
                delta = 1000,
                tid;

            tid = setInterval(function () {
                if (window.blurred) {
                    return;
                }
                time -= delta;
                alf_counter.innerText = time / 1000;
                if (time <= 0) {
                    clearInterval(tid);

                    alf_continue.disabled = false;
                    alf_continue.innerText = '{$continue}';
                }
            }, delta);
        }, 500);

        window.onblur = function () {
            window.blurred = true;
        };
        window.onfocus = function () {
            window.blurred = false;
        };

    });
</script>
";
    }

    public function tagsString()
    {
        $tags = $this->tags;

        $string = '';

        foreach ($tags as $tag) {
            $string .= '<a class="badge badge-pill badge-light" href="' . $tag->permalink() . '">' .
                $tag->name . '</a>';
        }

        return $string;
    }

    public function getMainImage($size = null)
    {
        $image = (string)$this->featuredImage->file;
        if (!$image) {
            return asset('assets/img/thumb.png');
        }

        $extension = (string)$this->featuredImage->extension;

        if (!$size) {
            return asset($image);
        }

        $sizes = Image::$sizes;

        $size = $sizes[$size] ?? $sizes['medium'];

        $pattern = "/(.*?)" . preg_quote('.' . $extension, '/') . "$/"; // Ex. .jpg
        $replacement = "$1-{$size[0]}x{$size[1]}.{$extension}";

        $image = preg_replace($pattern, $replacement, $image);

        return asset($image);
    }

    public function getMainImageHTML($size = null)
    {
        $src = (string)$this->featuredImage->file;

        /*
        $meta = $this->featuredImage->meta;

        $srcset = '';
        if (!empty($meta['sizes'])) {
            foreach ($meta['sizes'] as &$meta_size) {
                $w_h = $meta_size;

                $meta_size = asset(
                    str_replace(
                        ".",
                        "-{$w_h[0]}x{$w_h[1]}.",
                        $src
                    )
                );
                $meta_size .= ' ' . $w_h[0] . 'w';
            }
            $srcset .= 'srcset="';
            $srcset .= implode(', ', $meta['sizes']);
            $srcset .= '"';
        }
        */

        /*
        $image_info = pathinfo(public_path($src));

        $images = @glob($image_info['dirname'] . DS . $image_info['filename'] . "-*." . $image_info['extension']);

        foreach ($images as &$image) {
            $image = str_replace(public_path(), '', $image);
            if (preg_match("/-(?<width>\d+)x(?<height>\d+)./", $image, $matches)) {
                $image = asset($image) . ' ' . $matches['width'] . 'w';
            };
        }

        $srcset = 'srcset="';
        $srcset .= implode(', ', $images);
        $srcset .= '"';
        */

        if (is_integer($size)) {
            $size = [$size, $size];
        }

        if (is_array($size)) {
            $src = str_replace(".", "-{$size[0]}x{$size[1]}.", $src);
        }

        if (empty($src)) {
            $src = "//via.placeholder.com/{$size[0]}x{$size[1]}";
        }

        $html = '<img src ="' . asset($src) . '" alt="' . e($this->title) . '" width="' . $size[0] . '" ' .
            'height="' . $size[1] . '" />';

        return $html;
    }

    public function getMainImageBackground()
    {
        $html = "background-image: url('" . $this->getMainImage([370, 222]) . "');" .
            "background-image: -webkit-image-set(url('" . $this->getMainImage([370, 222]) . "') 1x," .
            "url('" . $this->getMainImage([740, 444]) . "') 2x);" .
            "background-image: -moz-image-set(url('" . $this->getMainImage([370, 222]) . "') 1x," .
            "url('" . $this->getMainImage([740, 444]) . "') 2x);" .
            "background-image: -o-image-set(url('" . $this->getMainImage([370, 222]) . "') 1x," .
            "url('" . $this->getMainImage([740, 444]) . "') 2x);" .
            "background-image: -ms-image-set(url('" . $this->getMainImage([370, 222]) . "') 1x," .
            "url('" . $this->getMainImage([740, 444]) . "') 2x);";

        $html = str_replace(["\r\n", "\r", "\n", "\t"], '', $html);

        return $html;
    }

    public function getMainImageRatio($ratio)
    {
        /*
        $src = (string)$this->featuredImage->file;

        $image_info = pathinfo(public_path($src));

        $images = @glob($image_info['dirname'] . DS . $image_info['filename'] . "-*." . $image_info['extension']);

        foreach ($images as &$image) {
            $image = str_replace(public_path(), '', $image);
            if (preg_match("/-(?<width>\d+)x(?<height>\d+)./", $image, $matches)) {
                $image = asset($image) . ' ' . $matches['width'] . 'w';
            };
        }

        return $image;
        */
    }

    public function getSummary($length = null)
    {
        $summary = $this->summary;

        return $this->getTextWords($summary, $length);
    }

    public function getMetaDescription()
    {
        $summary = $this->summary;

        return $this->getTextChars($summary, 160);
    }
}
