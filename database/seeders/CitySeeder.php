<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cities')->insert([
            ['name' => 'Dubai','country' => 'AE','roman_number' => 'I'],
            ['name' => 'New York','country' => 'US','roman_number' => 'II'],
            ['name' => 'UK','country' => 'GB','roman_number' => 'III'],
            ['name' => 'Sydney','country' => 'AU','roman_number' => 'IV'],
            ['name' => 'Potts Point','country' => 'AU','roman_number' => 'V'],
        ]);
    }
}
