<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
use \Yasumi\Yasumi;

class ShiftsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        //1ヶ月前から3ヶ月分の期間を作成する
        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::now()->subMonth()->addMonths(2)->endOfMonth();

        $period = CarbonPeriod::create($startDate, $endDate);

        //祝日取得
        $year = Carbon::now()->year;
        $holidays = array_merge(
            array_values(Yasumi::create('Japan', $year, 'ja_JP')->getHolidayDates()),
            array_values(Yasumi::create('Japan', $year + 1, 'ja_JP')->getHolidayDates())
        );

        foreach ($users as $user) {
            foreach ($period as $date) {
                $f_date = $date->format('Y-m-d');
                $shift = '出勤';
                $arrivaltime = '10:00:00';
                $leavetime   = '19:00:00';
                //週末か祝日
                if ($date->isWeekend() || in_array($f_date, $holidays)){
                    $shift = '確休';
                    $arrivaltime = null;
                    $leavetime   = null;
                }

                Shift::create([
                    'user_id'       => $user->id,
                    'date'          => $f_date,
                    'shift_status'  => $shift,
                    'arrivaltime'   => $arrivaltime,
                    'leavetime'     => $leavetime,
                ]);
            }
        }

    }
}
