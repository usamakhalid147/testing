<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MealPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currentDateTime = date('Y-m-d H:i:s');
        \DB::table('meal_plans')->truncate();

        \DB::table('meal_plans')->insert([
            ['name' => '{"en":"Breakfast"}', 'description' => NULL, 'status' => 1, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
            ['name' => '{"en":"Lunch"}', 'description' => NULL, 'status' => 1, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
            ['name' => '{"en":"Dinner"}', 'description' => NULL, 'status' => 1, 'created_at' => $currentDateTime, 'updated_at' => $currentDateTime],
        ]);
    }
}
