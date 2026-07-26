<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('options')
            ->insert(
                [
                    [
                        'name' => 'hcaptcha_checkbox_site_key',
                        'value' => null,
                        'auto' => '1',
                    ],
                    [
                        'name' => 'hcaptcha_checkbox_secret_key',
                        'value' => null,
                        'auto' => '1',
                    ],
                    [
                        'name' => 'social_links',
                        'value' => null,
                        'auto' => '1',
                    ],
                    [
                        'name' => 'listing_meta_data',
                        'value' => 'a:3:{i:0;s:1:"0";i:1;s:6:"author";i:2;s:14:"published_date";}',
                        'auto' => '1',
                    ],
                    [
                        'name' => 'article_meta_data',
                        'value' => 'a:3:{i:0;s:1:"0";i:1;s:6:"author";i:2;s:14:"published_date";}',
                        'auto' => '1',
                    ],
                    [
                        'name' => 'site_share_image',
                        'value' => null,
                        'auto' => '1',
                    ],
                ]
            );

        DB::table('options')
            ->where('name', 'social_links')
            ->update(
                [
                    'value' => \serialize(
                        [
                            [
                                'name' => 'Facebook',
                                'icon' => 'fab fa-facebook-f',
                                'url' => get_option_db('facebook_url'),
                            ],
                            [
                                'name' => 'Twitter',
                                'icon' => 'fab fa-twitter',
                                'url' => get_option_db('twitter_url'),
                            ],
                            [
                                'name' => 'YouTube',
                                'icon' => 'fab fa-youtube',
                                'url' => get_option_db('youtube_url'),
                            ],
                            [
                                'name' => 'Pinterest',
                                'icon' => 'fab fa-pinterest',
                                'url' => get_option_db('pinterest_url'),
                            ],
                            [
                                'name' => 'Instagram',
                                'icon' => 'fab fa-instagram',
                                'url' => get_option_db('instagram_url'),
                            ],
                            [
                                'name' => 'VK',
                                'icon' => 'fab fa-vk',
                                'url' => get_option_db('vk_url'),
                            ],
                        ]
                    ),
                ]
            );

        DB::table('options')
            ->whereIn(
                'name',
                [
                    'facebook_url',
                    'twitter_url',
                    'google_plus_url',
                    'youtube_url',
                    'pinterest_url',
                    'instagram_url',
                    'vk_url',
                ]
            )
            ->delete();

        DB::table('options')
            ->where('name', 'upload_filetypes')
            ->update(['value' => get_option_db('upload_filetypes') . ',webp']);

        Schema::table('pages', function (Blueprint $table) {
            $table->text('seo')->after('content')->nullable();
        });

        Schema::create('admin_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->text('permissions')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();
        });

        DB::table('admin_groups')
            ->insert(
                [
                    [
                        'id' => 1,
                        'name' => 'Super Admin',
                        'permissions' => '["super_admin"]',
                        'updated_at' => \now()->toDateTimeString(),
                        'created_at' => \now()->toDateTimeString(),
                    ],
                    [
                        'id' => 2,
                        'name' => 'Article Manager',
                        'permissions' => '["article_view","article_create","article_edit","article_new_approve","article_update_approve","article_delete"]',
                        'updated_at' => \now()->toDateTimeString(),
                        'created_at' => \now()->toDateTimeString(),
                    ],
                    [
                        'id' => 3,
                        'name' => 'Withdrawal Requests Manager',
                        'permissions' => '["withdrawal_requests_view","withdrawal_requests_manage"]',
                        'updated_at' => \now()->toDateTimeString(),
                        'created_at' => \now()->toDateTimeString(),
                    ],
                ]
            );

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_group_id')
                ->nullable()
                ->after('role')
                ->index('idx_adminGroupId');
        });

        DB::table('users')->where('role', 'admin')->update(['admin_group_id' => 1]);

        Schema::table('bookmarks', function (Blueprint $table) {
            $table->dropColumn('created_at');
            $table->unique(['user_id', 'article_id'], 'idx_user_article');
        });

        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('article_id');
            $table->unique(['user_id', 'article_id'], 'idx_user_article');
        });

        if (!Schema::hasColumn('ads', 'size')) {
            Schema::table('ads', function (Blueprint $table) {
                $table->string('size')->after('status')->nullable();
            });
        }

        if (Schema::hasColumn('ads', 'type')) {
            Schema::table('ads', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        $this->useEmbedGeneralShortcode();

        $this->deleteUnusedFiles();
    }

    protected function useEmbedGeneralShortcode()
    {
        $patterns = [
            "/<(p|div)>(https?:\/\/(www.|m.)?(youtube.com|youtu.be)\/(watch\?v=)?([a-zA-Z0-9\-_]+)([a-zA-Z0-9#\/\*\-\_\?\&\;\%\=\.]*))<\/(p|div)>/i",
            "/<(p|div)>(https?:\/\/(www.)?vimeo.com\/([0-9]+)([a-zA-Z0-9#\/\*\-\_\?\&\;\%\=\.]*))<\/(p|div)>/i",
            "/<(p|div)>(https?:\/\/(www.)?(dailymotion.com|dai.ly)\/(video\/)?([a-zA-Z0-9\-_]+)([a-zA-Z0-9#\/\*\-\_\?\&\;\%\=\.]*))<\/(p|div)>/i",
            "/<(p|div)>(https?:\/\/(www.)?twitter.com\/([a-zA-Z0-9\-_]+)\/status\/([0-9]+)([a-zA-Z0-9#\/\*\-\_\?\&\;\%\=\.]*))<\/(p|div)>/i",
            "/<(p|div)>(https?:\/\/(www.)?soundcloud.com\/([a-zA-Z0-9\-_]+)\/([a-zA-Z0-9\-_]+)([a-zA-Z0-9#\/\*\-\_\?\&\;\%\=\.]*))<\/(p|div)>/i",
            "/<(p|div)>(https?:\/\/(www.)?instagram.com\/p\/([a-zA-Z0-9\-_]+)([a-zA-Z0-9#\/\*\-\_\?\&\;\%\=\.]*))<\/(p|div)>/i",
            "/<(p|div)>(https?:\/\/(www.)?pinterest.com\/pin\/([a-zA-Z0-9\-_]+)([a-zA-Z0-9#\/\*\-\_\?\&\;\%\=\.]*))<\/(p|div)>/i",
            "/<(p|div)>(https?:\/\/(www.|m.)?facebook.com\/([a-zA-Z0-9\-_\.]+)\/(posts|photos|videos)\/([a-zA-Z0-9\-_\.]+)([a-zA-Z0-9#\/\*\-\_\?\&\;\%\=\.]*))<\/(p|div)>/i",
        ];

        $replace = [
            "<$1>[embed_general]$2[/embed_general]</$1>",
            "<$1>[embed_general]$2[/embed_general]</$1>",
            "<$1>[embed_general]$2[/embed_general]</$1>",
            "<$1>[embed_general]$2[/embed_general]</$1>",
            "<$1>[embed_general]$2[/embed_general]</$1>",
            "<$1>[embed_general]$2[/embed_general]</$1>",
            "<$1>[embed_general]$2[/embed_general]</$1>",
            "<$1>[embed_general]$2[/embed_general]</$1>",
        ];

        DB::table('articles')->select(['id', 'content'])
            ->chunkById(100, function ($articles) use ($patterns, $replace) {
                foreach ($articles as $article) {
                    $content = \preg_replace($patterns, $replace, $article->content);
                    \DB::table('articles')->where('id', $article->id)->update(['content' => $content]);
                }
            });
    }

    protected function deleteUnusedFiles()
    {
        $basePath = \base_path();

        $files = [
            $basePath . '/app/Ad.php',
            $basePath . '/app/AppModel.php',
            $basePath . '/app/Article.php',
            $basePath . '/app/Category.php',
            $basePath . '/app/Comment.php',
            $basePath . '/app/File.php',
            $basePath . '/app/Menu.php',
            $basePath . '/app/Option.php',
            $basePath . '/app/Page.php',
            $basePath . '/app/Sidebar.php',
            $basePath . '/app/SocialProfile.php',
            $basePath . '/app/Statistic.php',
            $basePath . '/app/Tag.php',
            $basePath . '/app/User.php',
            $basePath . '/app/Withdraw.php',
            $basePath . '/app/Http/Middleware/Activation.php',
        ];

        \File::delete($files);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
