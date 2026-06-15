<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StartReportTable;
use App\Models\Shift;
use Illuminate\Support\Carbon;

class StartReportTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 💡 デバッグ用：テーブルに全体のシフトが何件あるか確認
    $totalShifts = Shift::count();
    $this->command->info("--- [デバッグ] シフトテーブルの総件数: {$totalShifts} 件 ---");

    // 💡 デバッグ用：「出勤」だけで検索したら何件あるか確認
    $statusCount = Shift::where('shift_status', '出勤')->count();
    $this->command->info("--- [デバッグ] statusが'出勤'の件数: {$statusCount} 件 ---");

        $pastWorkShifts = Shift::where('shift_status', '出勤')
            ->where('date', '<=', Carbon::today()->format('Y-m-d'))
            ->get();

        // 💡 デバッグ用：最終的にマッチした件数を確認
    $this->command->info("--- [デバッグ] 過去の出勤シフトの件数: " . $pastWorkShifts->count() . " 件 ---");

        foreach ($pastWorkShifts as $shift) {
            StartReportTable::factory()->create([
                'user_id' => $shift->user_id,
                'date'    => $shift->date,
                'arrivalcheck' => 1,
            ]);
        }
    }
}
