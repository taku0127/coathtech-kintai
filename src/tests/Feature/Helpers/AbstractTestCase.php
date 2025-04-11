<?php

namespace Tests\Feature\Helpers;

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
    protected $LOGOUT_PATH = '/logout';
    protected $ATTENDANCE_PATH = '/attendance';
    protected $ATTENDANCE_TABLE = 'attendances';

     // 各テスト前に実行される
     protected function setUp(): void
     {
         parent::setUp();
         $this->artisan('migrate:fresh'); //  IDの自動採番をリセット
         $this->seed(); // シーダー実行
     }
}
