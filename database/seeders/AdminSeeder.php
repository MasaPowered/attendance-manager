<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        //2026.05.13 本番でfactoryが使えないみたいだから修正。
        /*if (app()->isLocal()) {
            // 開発環境のみレコードを追加
            Admin::factory()
                ->count(10)
                ->sequence(function ($sequence) {
                    return [
                        'name' => sprintf('admin_%02d', $sequence->index + 1),
                        'email' => sprintf('admin_%02d@mail.com', $sequence->index + 1),
                        'password' => Hash::make('admin'), // パスワード: admin  ※ 開発環境用のパスワードのためソース埋め込み
                        'created_at' => '2022-12-30 11:22:33',
                        'updated_at' => '2022-12-31 23:58:59',
                    ];
                })
                ->create();
        }*/

        $admins = [
            [
                'name' => 'admin',
                'email' => 'admin@mail.com',
                'password' => Hash::make('admin-01'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
        ];

        foreach ($admins as $admin) {
            Admin::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }
    }
}
