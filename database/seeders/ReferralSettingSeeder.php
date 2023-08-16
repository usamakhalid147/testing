<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('referral_settings')->truncate();

        DB::table('referral_settings')->insert([
            ['name' => 'is_enabled', 'value' => '1'],
            ['name' => 'per_user_limit', 'value' => '5000'],
            ['name' => 'new_referral_credit', 'value' => '20'],
            ['name' => 'user_become_guest_credit', 'value' => '20'],
        ]);
    }
}