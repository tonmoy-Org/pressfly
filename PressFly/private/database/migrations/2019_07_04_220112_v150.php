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
                        'name' => 'datetime_format',
                        'value' => 'MMM d, Y, h:mm a',
                        'auto' => '1',
                    ],
                    [
                        'name' => 'personal_token',
                        'value' => null,
                        'auto' => '1',
                    ],
                    [
                        'name' => 'purchase_code',
                        'value' => null,
                        'auto' => '1',
                    ],
                ]
            );

        Schema::table('comments', function (Blueprint $table) {
            $table
                ->unsignedTinyInteger('status')
                ->after('article_id')
                ->nullable()
                ->comment('1=active, 2=pending');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('image_id')->after('description')->nullable()->index('idx_imageid');
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
