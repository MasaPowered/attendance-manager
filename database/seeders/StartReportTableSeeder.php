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
        $pastWorkShifts = Shift::where('shift_status', '出勤')
            ->where('date', '<=', Carbon::today()->format('Y-m-d'))
            ->get();

        foreach ($pastWorkShifts as $shift) {
            StartReportTable::factory()->create([
                'user_id' => $shift->user_id,
                'date'    => $shift->date,
                'arrivalcheck' => 1,
            ]);
        }
    }
}
