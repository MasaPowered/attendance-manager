<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EndReportTable;
use App\Models\Shift;
use Illuminate\Support\Carbon;

class EndReportTableSeeder extends Seeder
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
            EndReportTable::factory()->create([
                'user_id' => $shift->user_id,
                'date'    => $shift->date,
                'leavecheck' => 1,
            ]);
        }
    }
}
