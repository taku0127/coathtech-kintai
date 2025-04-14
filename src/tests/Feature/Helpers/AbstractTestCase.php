<?php

namespace Tests\Feature\Helpers;

use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AbstractTestCase extends TestCase{
 /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    protected $REGISTER_PATH = '/register';
    protected $USER_LOGIN_PATH = '/login';
    protected $ADMIN_LOGIN_PATH = '/admin/login';
    protected $ADMIN_ATTENDANCE_LIST = 'admin/attendance/list';
    protected $LOGOUT_PATH = '/logout';
    protected $ATTENDANCE_PATH = '/attendance';
    protected $ATTENDANCE_TABLE = 'attendances';
    protected $ATTENDANCE_LIST_PATH = '/attendance/list';
    protected $ATTENDANCE_REST_START_PATH = '/attendance/break_start';
    protected $ATTENDANCE_REST_END_PATH = '/attendance/break_end';
    protected $ATTENDANCE_CLOCK_OUT = '/attendance/clock_out';
    protected $BREAKTIME_TABLE = 'break_times';
    protected $ADMIN_STAMP_CORRECTION_REQUEST = '/stamp_correction_request/list';
    protected $ADMIN_STAMP_CORRECTION_REQUEST_APPROVE = '/stamp_correction_request/approve';

     // 各テスト前に実行される
     protected function setUp(): void
     {
         parent::setUp();
         $this->artisan('migrate:fresh'); //  IDの自動採番をリセット
         $this->seed(); // シーダー実行
     }

      // 処理用関数
    protected function getCurrentDateTime($dateType="Y-m-d",$timeType="H:i",?Closure $dateOptions = null){
        $date = Carbon::now();
        $date->locale('ja');
        $currentDateTime = [
            'date' => $date->format($dateType),
            'time' => $date->format($timeType)
        ];
        if ($dateOptions) {
            $currentDateTime['date'] .= $dateOptions($date); // クロージャを呼び出して結果を追加
        }
        return $currentDateTime;
    }
}
