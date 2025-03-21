<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'name' => '拓',
            'email' => 'y.taku84@gmail.com',
            'password' => bcrypt('00000000'),
            'email_verified_at' => now(),
        ]);
        User::factory()->count(3)->create();
    }
}
