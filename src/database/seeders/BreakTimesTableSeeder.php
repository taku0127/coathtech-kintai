<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BreakTimesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // すべての attendance を取得
        $attendances = Attendance::all();

        $faker = Faker::create();

        foreach ($attendances as $attendance) {
            // 1つまたは2つランダムに breaktime を作成
            foreach (range(1, $faker->numberBetween(1, 2)) as $index) {

                Breaktime::create([
                    'attendance_id' => $attendance->id,
                    'start'    => $index == 1 ? '13:00:00' : '15:00:00',
                    'end'      => $index == 1 ? '14:00:00' : '15:30:00',
                ]);
            }
        }
    }
}
