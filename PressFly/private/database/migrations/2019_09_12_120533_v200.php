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
                        'name' => 'youtube_url',
                        'value' => null,
                        'auto' => '1',
                    ],
                    [
                        'name' => 'pinterest_url',
                        'value' => null,
                        'auto' => '1',
                    ],
                    [
                        'name' => 'instagram_url',
                        'value' => null,
                        'auto' => '1',
                    ],
                    [
                        'name' => 'vk_url',
                        'value' => null,
                        'auto' => '1',
                    ],
                    [
                        'name' => 'proxy_service',
                        'value' => 'free',
                        'auto' => '1',
                    ],
                    [
                        'name' => 'isproxyip_key',
                        'value' => null,
                        'auto' => '1',
                    ],
                    [
                        'name' => 'ads_protector',
                        'value' => 0,
                        'auto' => '1',
                    ],
                    [
                        'name' => 'site_keywords',
                        'value' => null,
                        'auto' => '1',
                    ],
                ]
            );

        DB::statement("ALTER TABLE `articles` CHANGE `status` `status` TINYINT(2) UNSIGNED NULL DEFAULT NULL COMMENT '1=Approved, 2=Disabled, 3=New Pending Review, 5=New Need Improvements, 4=Update Pending Review, 6=Update Need Improvements, 7=Soft Disabled, 8=Draft';");

        Schema::table('articles', function (Blueprint $table) {
            $table->text('seo')->after('content')->nullable();

            $table->dropColumn('notes');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->text('seo')->after('description')->nullable();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->text('seo')->after('description')->nullable();
        });
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
