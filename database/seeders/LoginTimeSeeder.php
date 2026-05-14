<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoginTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('login_time_table')->updateOrInsert(
            ['id' => 1], 
            [
                'name' => '',
                'logintime_status' => 0,
                'start_time' => '09:00:00',
                'end_time'   => '18:00:00',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]
        );
    }
}