<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use DateTime;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Helpers\AbstractTestCase;

class AttendanceTest extends AbstractTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    ///////////////////////////日時取得機能//////////////////////////////////

    //  現在の日時情報がUIと同じ形式で出力されている
    // jsを使っているのでduskテスト

    ///////////////////////////出勤機能//////////////////////////////////
    // 出勤ボタンが正しく機能する
    public function test_attendance_text_button()
    {
        // 勤務外ユーザーでログイン
        $user = User::find(1);
        $this->actingAs($user);

        // /attendanceページを開く
        $response = $this->get($this->ATTENDANCE_PATH);
        $response->assertStatus(200);
        // 勤務ボタン表示の確認
        $this->assertMatchesRegularExpression('/<button[^>]*>出勤<\/button>/', $response->getContent());
        // 出勤の処理を行う
        $currentDateTime = $this->getCurrentDateTime();// 現在の日時を取得
        $postData = [
            'date' => $currentDateTime['date'],
            'clock_in' => $currentDateTime['time'],
        ];
        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH,$postData);
        $this->assertDatabaseHas($this->ATTENDANCE_TABLE,$postData);

        // 出勤中が表示される
        $response->assertSee('出勤中');

    }

    // 出勤は一日一回のみできる
    public function test_attendance_once_time(){
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 出勤と退勤両方attendanceデータを入れる
        $currentDateTime = $this->getCurrentDateTime();
        $postData = [
            'user_id' => $user->id,
            'date' => $currentDateTime['date'],
            'clock_in' => $currentDateTime['time'],
            'clock_out' => $currentDateTime['time']
        ];
        DB::table($this->ATTENDANCE_TABLE)->insert($postData);
        // 画面に出勤ボタンがないことを確認
        $response = $this->get($this->ATTENDANCE_PATH);
        $response->assertDontSee('出勤');
    }
    // 出勤時刻が管理画面で確認できる
    public function test_attendance_verifiable(){
         // 勤務外ユーザーでログイン
         $user = User::find(1);
         $this->actingAs($user);

         // /attendanceページを開く
         $response = $this->get($this->ATTENDANCE_PATH);
         $response->assertStatus(200);
         // 出勤の処理を行う
         $currentDateTime = $this->getCurrentDateTime();// 現在の日時を取得
         $postData = [
             'date' => $currentDateTime['date'],
             'clock_in' => $currentDateTime['time'],
         ];

        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH,$postData);
        $this->assertDatabaseHas($this->ATTENDANCE_TABLE,$postData);

        //勤怠一覧に本日の出勤情報が見れるかか確認する。
        $response = $this->get($this->ATTENDANCE_LIST_PATH);
        // 勤怠一覧の表示フォーマットに修正
        $currentDateTimeFormat = $this->getCurrentDateTime('m/d','H:i', function($date){
            return '(' . $date->isoFormat('ddd') . ')';
        });
        $checkData = [
            'date' => $currentDateTimeFormat['date'],
            'clock_in' => $currentDateTimeFormat['time']
        ];
        $response->assertSeeInOrder($checkData);
    }

    ///////////////////////////休憩機能//////////////////////////////////
    // 休憩ボタンが正しく機能する
    public function test_attendance_rest_start(){
        // ログイン
        $user = User::find(1);
        $this->actingAs($user);
        // /attendanceページへ入る
        $response = $this->get($this->ATTENDANCE_PATH);
        $response->assertStatus(200);
        // 出勤の処理を行う
        $currentDateTime = $this->getCurrentDateTime();// 現在の日時を取得
        $postData = [
            'date' => $currentDateTime['date'],
            'clock_in' => $currentDateTime['time'],
        ];

        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH,$postData);
        $this->assertDatabaseHas($this->ATTENDANCE_TABLE,$postData);
        // 休憩ボタンがあるか確認
        $response->assertSee('休憩入');

        sleep(1);
        // 休憩ボタンを押下
        $attendance_id = Attendance::latest()->first()->id;
        $postData = [
            'start' => $currentDateTime['time'],
            'attendance_id' => $attendance_id
        ];
        $this->post($this->ATTENDANCE_REST_START_PATH,$postData);
        $response = $this->get($this->ATTENDANCE_PATH);
        // 画面に休憩中のテキストがあるか確認
        $response->assertSee('休憩中');
    }
    // 休憩は一日に何回でもできる
    public function test_attendance_rest_start_multiple_times(){
        // ログイン
        $user = User::find(1);
        $this->actingAs($user);
        // /attendanceページへ入る
        $response = $this->get($this->ATTENDANCE_PATH);
        $response->assertStatus(200);
        // 出勤の処理を行う
        $currentDateTime = $this->getCurrentDateTime();// 現在の日時を取得
        $postData = [
            'date' => $currentDateTime['date'],
            'clock_in' => $currentDateTime['time'],
        ];

        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH,$postData);
        $this->assertDatabaseHas($this->ATTENDANCE_TABLE,$postData);
        // 休憩ボタンがあるか確認
        $response->assertSee('休憩入');

        // 休憩ボタンを押下
        $attendance_id = Attendance::latest()->first()->id;
        $postData = [
            'start' => $currentDateTime['time'],
            'attendance_id' => $attendance_id
        ];
        $this->post($this->ATTENDANCE_REST_START_PATH,$postData);
        sleep(1);
        $response = $this->get($this->ATTENDANCE_PATH);
        // 画面に休憩中のテキストがあるか確認
        $response->assertSee('休憩中');

        sleep(1);
        // 休憩戻りのボタンを押下
        $postData = [
            'end' => $currentDateTime['time'],
            'attendance_id' => $attendance_id
        ];
        $response = $this->followingRedirects()->patch($this->ATTENDANCE_REST_END_PATH,$postData);
        // 休憩ボタンがあるか確認
        $response->assertSee('休憩入');
    }
    // 休憩戻ボタンが正しく機能する
    public function test_attendance_rest_end(){
        // ログイン
        $user = User::find(1);
        $this->actingAs($user);
        // /attendanceページへ入る
        $response = $this->get($this->ATTENDANCE_PATH);
        $response->assertStatus(200);
        // 出勤の処理を行う
        $currentDateTime = $this->getCurrentDateTime();// 現在の日時を取得
        $postData = [
            'date' => $currentDateTime['date'],
            'clock_in' => $currentDateTime['time'],
        ];

        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH,$postData);
        $this->assertDatabaseHas($this->ATTENDANCE_TABLE,$postData);
        // 休憩ボタンがあるか確認
        $response->assertSee('休憩入');

        // 休憩ボタンを押下
        $attendance_id = Attendance::latest()->first()->id;
        $postData = [
            'start' => $currentDateTime['time'],
            'attendance_id' => $attendance_id
        ];
        $this->post($this->ATTENDANCE_REST_START_PATH,$postData);
        $response = $this->get($this->ATTENDANCE_PATH);
        // 画面に休憩戻があるか確認
        sleep(1);
        $response->assertSee('休憩戻');
        sleep(1);
        // 休憩戻りのボタンを押下
        $postData = [
            'end' => $currentDateTime['time'],
            'attendance_id' => $attendance_id
        ];
        $response = $this->followingRedirects()->patch($this->ATTENDANCE_REST_END_PATH,$postData);
        // 出勤中があるか確認
        $response->assertSee('出勤中');
    }
    // 休憩戻は一日に何回でもできる
    public function test_attendance_rest_end_multiple_times(){
        // ログイン
        $user = User::find(1);
        $this->actingAs($user);
        // /attendanceページへ入る
        $response = $this->get($this->ATTENDANCE_PATH);
        $response->assertStatus(200);
        // 出勤の処理を行う
        $currentDateTime = $this->getCurrentDateTime();// 現在の日時を取得
        $postData = [
            'date' => $currentDateTime['date'],
            'clock_in' => $currentDateTime['time'],
        ];

        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH,$postData);
        $this->assertDatabaseHas($this->ATTENDANCE_TABLE,$postData);
        // 休憩ボタンがあるか確認
        $response->assertSee('休憩入');

        // 休憩ボタンを押下
        $attendance_id = Attendance::latest()->first()->id;
        $postData = [
            'start' => $currentDateTime['time'],
            'attendance_id' => $attendance_id
        ];
        $response = $this->followingRedirects()->post($this->ATTENDANCE_REST_START_PATH,$postData);
        // 画面に休憩戻があるか確認
        sleep(1);
        $response->assertSee('休憩戻');
        sleep(1);
        // 休憩戻りのボタンを押下
        $postData = [
            'end' => $currentDateTime['time'],
            'attendance_id' => $attendance_id
        ];
        $response = $this->followingRedirects()->patch($this->ATTENDANCE_REST_END_PATH,$postData);
        // 出勤中があるか確認
        $response->assertSee('出勤中');

         // 休憩ボタンを押下
         $postData = [
             'start' => $currentDateTime['time'],
             'attendance_id' => $attendance_id
         ];
         $response = $this->followingRedirects()->post($this->ATTENDANCE_REST_START_PATH,$postData);
         // 画面に休憩戻があるか確認
         sleep(1);
         $response->assertSee('休憩戻');
    }
    // 休憩時刻が勤怠一覧画面で確認できる
    public function test_attendance_rest_check_list(){
        // ログイン
        $user = User::find(1);
        $this->actingAs($user);
        // /attendanceページへ入る
        $response = $this->get($this->ATTENDANCE_PATH);
        $response->assertStatus(200);
        // 出勤の処理を行う
        $currentDateTime = $this->getCurrentDateTime();// 現在の日時を取得
        $postData = [
            'date' => $currentDateTime['date'],
            'clock_in' => $currentDateTime['time'],
        ];

        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH,$postData);
        $this->assertDatabaseHas($this->ATTENDANCE_TABLE,$postData);
        // 休憩ボタンがあるか確認
        $response->assertSee('休憩入');

        // 休憩ボタンを押下
        $attendance_id = Attendance::latest()->first()->id;
        $postData = [
            'start' => $currentDateTime['time'],
            'attendance_id' => $attendance_id
        ];
        $this->post($this->ATTENDANCE_REST_START_PATH,$postData);
        $response = $this->get($this->ATTENDANCE_PATH);
        sleep(1);
        $response->assertSee('休憩戻');
        sleep(1);
        // 休憩戻りのボタンを押下・一分後を登録
        $currentTimeafter1minit = Carbon::createFromTimeString($currentDateTime['time'])->addMinute()->format('H:i');
        $postData = [
            'end' => $currentTimeafter1minit,
            'attendance_id' => $attendance_id
        ];
        $response = $this->followingRedirects()->patch($this->ATTENDANCE_REST_END_PATH,$postData);
        // 出勤中があるか確認
        $response->assertSee('出勤中');

        //勤怠一覧画面に遷移して、休憩時刻が記録されているか確認
        $response = $this->get($this->ATTENDANCE_LIST_PATH);
        $response->assertStatus(200);
        // 勤怠一覧の表示フォーマットに修正
        $currentDateTimeFormat = $this->getCurrentDateTime('m/d','H:i', function($date){
            return '(' . $date->isoFormat('ddd') . ')';
        });
        // 休憩時間差分を0:00の形へ変換
        $breakTimeDiff = Carbon::createFromTimeString($currentDateTime['time'])->diffAsCarbonInterval(Carbon::createFromTimeString($currentTimeafter1minit));
        $totalMinutes = $breakTimeDiff->totalMinutes;
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        $formatted = sprintf('%d:%02d', $hours, $minutes);
        $checkData = [
            'date' => $currentDateTimeFormat['date'],
            'clock_in' => $currentDateTimeFormat['time'],
            'break_time' => $formatted,
        ];
        $response->assertSeeInOrder($checkData);
    }
    ///////////////////////////退勤機能//////////////////////////////////
    // 退勤ボタンが正しく機能する
    public function test_attendance_clock_out(){
        // ログイン
        $user = User::find(1);
        $this->actingAs($user);
        // /attendanceページへ入る
        $response = $this->get($this->ATTENDANCE_PATH);
        $response->assertStatus(200);
        // 出勤の処理を行う
        $currentDateTime = $this->getCurrentDateTime();// 現在の日時を取得
        $postData = [
            'date' => $currentDateTime['date'],
            'clock_in' => $currentDateTime['time'],
        ];

        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH,$postData);
        $this->assertDatabaseHas($this->ATTENDANCE_TABLE,$postData);
        // 退勤ボタンがあるか確認
        sleep(1);
        $response->assertSee('退勤');
        sleep(1);

        // 退勤処理を行う
        $attendance_id = Attendance::latest()->first()->id;
        $postData = [
            'clock_out' => $currentDateTime['time'],
            'attendance_id' => $attendance_id
        ];
        $this->patch($this->ATTENDANCE_CLOCK_OUT,$postData);
        sleep(1);
        $response = $this->get($this->ATTENDANCE_PATH);
        sleep(1);
        $response->assertSee('退勤済');
    }
    // 退勤時刻が管理画面で確認できる
    public function test_attendance_clock_out_check_list(){
        // ログイン
        $user = User::find(1);
        $this->actingAs($user);
        // /attendanceページへ入る
        $response = $this->get($this->ATTENDANCE_PATH);
        $response->assertStatus(200);
        // 出勤の処理を行う
        $currentDateTime = $this->getCurrentDateTime();// 現在の日時を取得
        $postData = [
            'date' => $currentDateTime['date'],
            'clock_in' => $currentDateTime['time'],
        ];

        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH,$postData);
        $this->assertDatabaseHas($this->ATTENDANCE_TABLE,$postData);
        // 退勤ボタンがあるか確認
        $response->assertSee('退勤');

        // 退勤処理を行う
        $attendance_id = Attendance::latest()->first()->id;
        $postData = [
            'clock_out' => $currentDateTime['time'],
            'attendance_id' => $attendance_id
        ];
        $this->patch($this->ATTENDANCE_CLOCK_OUT,$postData);
        sleep(1);
        $response = $this->get($this->ATTENDANCE_PATH);
        sleep(1);
        $response->assertSee('退勤済');
        // 一覧画面にも正確に記録されているか確認
        $response = $this->get($this->ATTENDANCE_LIST_PATH);
        $currentDateTimeFormat = $this->getCurrentDateTime('m/d','H:i', function($date){
            return '(' . $date->isoFormat('ddd') . ')';
        });
        $checkData = [
            'date' => $currentDateTimeFormat['date'],
            'clock_in' => $currentDateTimeFormat['time'],
            'clock_out' => $currentDateTimeFormat['time']
        ];
        $response->assertSeeInOrder($checkData);
    }



}
