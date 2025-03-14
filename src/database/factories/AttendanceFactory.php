<?php

namespace Database\Factories;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Attendance::class;
    public function definition()
    {
        $clockIn = Carbon::today()->addHours(rand(8, 12))->addMinutes(rand(0, 59))->format('H:i:s');
        $clockOut = Carbon::createFromFormat('H:i:s', $clockIn)
            ->addHours(rand(8, 10))
            ->addMinutes(rand(0, 59));
        return [
            //
            'date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'note' => $this->faker->sentence(),
            'approval' => $this->faker->boolean(),
        ];
    }
}
