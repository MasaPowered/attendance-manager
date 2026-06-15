<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EndReportTable>
 */
class EndReportTableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //1割早退
        $isLate = $this->faker->boolean(10); 

        //早退判定
        if ($isLate) {
            // 13:15 〜 14:00
            $leavetime = sprintf('13:%02d:00', $this->faker->numberBetween(15, 59));
            $report = $this->faker->randomElement(['お疲れ様でした。本日はデータ入力を行いました。', '清掃を行います。', 'おはようございます。軽作業を行います。']);
        } else {
            // 19:00 〜 19:30
            $leavetime = sprintf('19:%02d:00', $this->faker->numberBetween(0, 30));
            $report = $this->faker->randomElement(['お疲れ様です。本日はデータ入力を行いました。', 'お疲れ様です。清掃を行いました。', 'お疲れ様でした。軽作業を行いました。']);;
        }

        return [
            'leavetime' => $leavetime,
            'report' => $report,
        ];
    }
}
