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
        DB::statement("ALTER TABLE `admin_groups` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `admin_groups` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `ads` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `ads` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `articles` CHANGE `published_at` `published_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `articles` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `articles` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `categories` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `categories` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `comments` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `comments` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `files` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `files` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `followers` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `followers` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `pages` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `pages` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `social_profiles` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `social_profiles` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `statistics` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `tags` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `tags` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `users` CHANGE `email_verified_at` `email_verified_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `users` CHANGE `updated_at` `updated_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `users` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
        DB::statement("ALTER TABLE `withdraws` CHANGE `created_at` `created_at` DATETIME NULL DEFAULT NULL;");
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
