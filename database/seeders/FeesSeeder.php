<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('fees')->truncate();

		DB::table('fees')->insert([
            ['id' => 1, 'name' => 'service_fee_type', 'value' => 'percentage'],
            ['id' => 2, 'name' => 'service_fee', 'value' => 10],
            ['id' => 3, 'name' => 'min_service_fee', 'value' => 1],
            ['id' => 4, 'name' => 'host_fee', 'value' => 5],
            ['id' => 5, 'name' => 'host_penalty_enabled', 'value' => 0],
            ['id' => 6, 'name' => 'host_cancel_limit', 'value' => 5],
            ['id' => 7, 'name' => 'penalty_days', 'value' => 7],
            ['id' => 8, 'name' => 'cancel_before_days', 'value' => 50],
            ['id' => 9, 'name' => 'cancel_after_days', 'value' => 100],
        ]);
    }
}
