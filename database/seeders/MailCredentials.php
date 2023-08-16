<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class MailCredentials extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('credentials')->insert([
            [
                'name' => 'host',
                'value' => '',
                'site' => 'MemberEmailConfig'
            ],
            [
                'name' => 'port',
                'value' => '',
                'site' => 'MemberEmailConfig'
            ],
            [
                'name' => 'from_address',
                'value' => '',
                'site' => 'MemberEmailConfig'
            ],
            [
                'name' => 'from_name',
                'value' => '',
                'site' => 'MemberEmailConfig'
            ],
            [
                'name' => 'encryption',
                'value' => '',
                'site' => 'MemberEmailConfig'
            ],
            [
                'name' => 'username',
                'value' => '',
                'site' => 'MemberEmailConfig'
            ],
            [
                'name' => 'password',
                'value' => '',
                'site' => 'MemberEmailConfig'
            ],
            [
                'name' => 'host',
                'value' => '',
                'site' => 'HotelierEmailConfig'
            ],
            [
                'name' => 'port',
                'value' => '',
                'site' => 'HotelierEmailConfig'
            ],
            [
                'name' => 'from_address',
                'value' => '',
                'site' => 'HotelierEmailConfig'
            ],
            [
                'name' => 'from_name',
                'value' => '',
                'site' => 'HotelierEmailConfig'
            ],
            [
                'name' => 'encryption',
                'value' => '',
                'site' => 'HotelierEmailConfig'
            ],
            [
                'name' => 'username',
                'value' => '',
                'site' => 'HotelierEmailConfig'
            ],
            [
                'name' => 'password',
                'value' => '',
                'site' => 'HotelierEmailConfig'
            ]
        ]);
    }
}
