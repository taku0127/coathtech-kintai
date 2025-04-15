<?php

namespace Tests\Browser;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AttendanceTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     */
    use RefreshDatabase;

    protected $REGISTER_PATH = '/register';
    protected $USER_LOGIN_PATH = '/login';
    protected $ADMIN_LOGIN_PATH = '/admin/login';
    protected $LOGOUT_PATH = '/logout';
    protected $ATTENDANCE_PATH = '/attendance';

     // 各テスト前に実行される
     protected function setUp(): void
     {
         parent::setUp();
         $this->artisan('migrate:fresh'); //  IDの自動採番をリセット
         $this->seed(); // シーダー実行
     }
    ///////////////////////////日時取得機能//////////////////////////////////

    //  現在の日時情報がUIと同じ形式で出力されている
    public function test_attendance_date_info()
    {
        // ログイン
        $user = User::find(1);
        $this->actingAs($user);

        // /attendanceページを開く
        $this->browse(function (Browser $browser) use ($user) {
            // 画面の時刻と現在の時刻の比較
            // 現在の日時を取得
            $date = Carbon::now();
            $date->locale('ja');
            $currentDateTime = [
                'date' => $date->format('Y年n月d日') . '(' . $date->isoFormat('ddd') . ')'
                ,
                'time' => $date->format('H:i')
            ];

            $browser->loginAs($user)
                    ->visit('/attendance')
                    ->assertPathIs('/attendance')
                    ->pause(100)
                    ->assertSee($currentDateTime['date'])
                    ->assertSee($currentDateTime['time']);


        });
    }
}
