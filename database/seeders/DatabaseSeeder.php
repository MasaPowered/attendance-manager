<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            ShiftsTableSeeder::class,
            LoginTimeSeeder::class,
            StartReportTableSeeder::class,
            EndReportTableSeeder::class,
        ]);

        //2026.06.15 本番環境で使いたくないものはこちらに入れる。
        /*if (app()->environment('local')) {
            $this->call([
                UserSeeder::class,
                ShiftsTableSeeder::class,
            ]);
        }*/
    }
}
