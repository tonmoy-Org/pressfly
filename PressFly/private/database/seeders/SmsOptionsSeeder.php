<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmsOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            ['name' => 'sms_revesms_api_key', 'value' => '', 'auto' => 1],
            ['name' => 'sms_revesms_secret_key', 'value' => '', 'auto' => 1],
            ['name' => 'sms_revesms_caller_id', 'value' => '', 'auto' => 1],
            ['name' => 'sms_verification_enabled', 'value' => '1', 'auto' => 1]
        ];

        foreach ($options as $option) {
            \DB::table('options')->insertOrIgnore($option);
        }
    }
}
