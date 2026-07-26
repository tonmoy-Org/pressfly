<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('options')
            ->where('name', 'datetime_format')
            ->update(['value' => 'MMM D, OY, h:mm A']);

        DB::table('options')
            ->insert(
                [
                    [
                        'name' => 'adlinkfly_secret_key',
                        'value' => (string)\Str::uuid(),
                        'auto' => '1',
                    ],
                    [
                        'name' => 'adlinkfly_counter',
                        'value' => '5',
                        'auto' => '1',
                    ],
                ]
            );
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
