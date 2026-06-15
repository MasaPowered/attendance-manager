<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StartReportTable>
 */
class StartReportTableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //1割遅刻
        $isLate = $this->faker->boolean(10); 

        //遅刻判定
        if ($isLate) {
            // 10:15 〜 11:00
            $arrivaltime = sprintf('10:%02d:00', $this->faker->numberBetween(15, 59));
            $latetime = $arrivaltime;
            $report = $this->faker->randomElement(['おはようございます。遅刻してすみません。本日はデータ入力を行います。', '体調不良のため連絡済。清掃を行います。', 'おはようございます。軽作業を行います。']);
        } else {
            // 09:50 〜 10:00
            $arrivaltime = sprintf('09:%02d:00', $this->faker->numberBetween(50, 59));
            $latetime = null;
            $report = $this->faker->randomElement(['おはようございます。本日はデータ入力を行います。', 'おはようございます。清掃を行います。', 'おはようございます。軽作業を行います。']);;
        }

        $leave = sprintf('19:%02d:00', $this->faker->numberBetween(0, 30));

        return [
            'arrivaltime' => $arrivaltime,
            'latetime' => $latetime,
            'report' => $report,
        ];
    }
}