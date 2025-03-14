<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::all()->each(function ($User,$index){
            foreach(range(0,30) as $i){
                Attendance::factory()->count(1)->create(['user_id' => $User->id,'date' => Carbon::today()->subDays($i)]);
            }
        });
    }
}
