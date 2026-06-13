<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            /*'user_id' => User::factory(), // 紐づくユーザーがなければ自動生成
            
            // 💡 1. まずはFakerでランダムな日付（例: 2026年6月の間）を生成
            'date' => $this->faker->dateTimeBetween('2026-06-01', '2026-06-30')->format('Y-m-d'),
            
            // 💡 2. 上で決まった 'date' を使って、シフト状態を動的に決定する
            'shift_status' => function (array $attributes) {
                // 生成された日付をCarbonで解析
                $date = Carbon::parse($attributes['date']);

                // 🎌 日本の祝日リスト（2026年6月〜7月の例。必要な分だけ足してください）
                $holidays = [
                    '2026-07-20', // 海の日
                    '2026-08-11', // 山の日
                    '2026-09-21', // 敬老の日
                    // 必要に応じて他の祝日も「'YYYY-MM-DD',」の形式で追加
                ];

                // 🔴 判定A：土曜日、または日曜日（CarbonのisWeekend()を使用）
                // 🔴 判定B：祝日リストに含まれている
                if ($date->isWeekend() || in_array($date->format('Y-m-d'), $holidays)) {
                    return '休'; // 土日祝なら「休」
                }

                // 🟢 それ以外（平日）
                return '出勤';
            },
            
            'created_at' => now(),
            'updated_at' => now(),*/
        ];
    }
}
