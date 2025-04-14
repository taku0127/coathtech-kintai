<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Tests\Feature\Helpers\AbstractTestCase;

class AttendanceDetailTest extends AbstractTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    ////////////////////////勤怠詳細情報取得機能（一般ユーザー）//////////////////////////////
    // 勤怠詳細画面の「名前」がログインユーザーの氏名になっている
    public function test_check_name()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);
        // 名前があるか確認する
        $response->assertSee($user->name);
    }
    // 勤怠詳細画面の「日付」が選択した日付になっている
    public function test_check_date()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);
        // 日付があるか確認する
        $date = Carbon::parse($attendance->date);
        $formattedYear = $date->format('Y年');
        $formattedMonthDay = $date->format('n月j日');
        $response->assertSee($formattedYear);
        $response->assertSee($formattedMonthDay);
    }
    // 「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
    public function test_check_clock()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);
        // 出勤・退勤が表示されているか確認
        $clock_in = Carbon::parse($attendance->clock_in);
        $clock_out = Carbon::parse($attendance->clock_out);
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $response->assertSee($clock_inFormat);
        $response->assertSee($clock_outFormat);
    }
    // 「休憩」にて記されている時間がログインユーザーの打刻と一致している
    public function test_check_break_time()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);
        // 休憩が表示されているか確認
        $clock_in = Carbon::parse($attendance->clock_in);
        $clock_out = Carbon::parse($attendance->clock_out);
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $response->assertSee($clock_inFormat);
        $response->assertSee($clock_outFormat);
    }


    ////////////////////////勤怠詳細情報取得機能（一般ユーザー）//////////////////////////////
    // 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_check_validation_clock()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::with('breakTimes')->where('user_id', $user->id)->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 出勤時間が退勤時間より後の時間で修正
        $clock_in = Carbon::parse($attendance->clock_in);
        $clock_out = $clock_in->copy()->subMinutes(30);
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $postData = [
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => 'test'
        ];
        // 休憩の追加
        foreach ($attendance->breakTimes as $breakTime) {
            $breakTimeStartFormat = Carbon::parse($breakTime->start)->format('H:i');
            $breakTimeEndFormat = Carbon::parse($breakTime->end)->format('H:i');
            $postData['break_time']['start'][$breakTime->id] = $breakTimeStartFormat;
            $postData['break_time']['end'][$breakTime->id] = $breakTimeEndFormat;
        }

        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH."/".$attendance->id,$postData);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }
    // 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_check_validation_break_time_start_before_clock_out()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::with('breakTimes')->where('user_id', $user->id)->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 出勤時間と退勤時刻で入力
        $clock_in = Carbon::parse($attendance->clock_in);
        $clock_out = Carbon::parse($attendance->clock_out);
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $postData = [
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => 'test'
        ];
        // 休憩の追加
        foreach ($attendance->breakTimes as $breakTime) {
            $breakTimeStartFormat = $clock_out->copy()->addMinutes(30)->format('H:i');
            $breakTimeEndFormat = Carbon::parse($breakTime->end)->format('H:i');
            $postData['break_time']['start'][$breakTime->id] = $breakTimeStartFormat;
            $postData['break_time']['end'][$breakTime->id] = $breakTimeEndFormat;
        }
        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH."/".$attendance->id,$postData);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }
    // 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_check_validation_break_time_end_before_clock_out()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::with('breakTimes')->where('user_id', $user->id)->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 出勤時間と退勤時刻で入力
        $clock_in = Carbon::parse($attendance->clock_in);
        $clock_out = Carbon::parse($attendance->clock_out);
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $postData = [
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => 'test'
        ];
        // 休憩の追加
        foreach ($attendance->breakTimes as $breakTime) {
            $breakTimeStartFormat = Carbon::parse($breakTime->start)->format('H:i');
            $breakTimeEndFormat = $clock_out->copy()->addMinutes(30)->format('H:i');
            $postData['break_time']['start'][$breakTime->id] = $breakTimeStartFormat;
            $postData['break_time']['end'][$breakTime->id] = $breakTimeEndFormat;
        }
        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH."/".$attendance->id,$postData);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }
    // 備考欄が未入力の場合のエラーメッセージが表示される
    public function test_check_validation_note()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::with('breakTimes')->where('user_id', $user->id)->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 出勤時間と退勤時刻で入力
        $clock_in = Carbon::parse($attendance->clock_in);
        $clock_out = Carbon::parse($attendance->clock_out);
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $postData = [
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => ''
        ];
        // 休憩の追加
        foreach ($attendance->breakTimes as $breakTime) {
            $breakTimeStartFormat = Carbon::parse($breakTime->start)->format('H:i');
            $breakTimeEndFormat = Carbon::parse($breakTime->end)->format('H:i');
            $postData['break_time']['start'][$breakTime->id] = $breakTimeStartFormat;
            $postData['break_time']['end'][$breakTime->id] = $breakTimeEndFormat;
        }
        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH."/".$attendance->id,$postData);
        $response->assertSee('備考を記入してください');
    }
    // 修正申請処理が実行される
    public function test_check_ok()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::with('breakTimes')->where('user_id', $user->id)->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 出勤時間と退勤時刻で入力
        $clock_in = Carbon::parse($attendance->clock_in)->addMinute();
        $clock_out = Carbon::parse($attendance->clock_out)->addMinute();
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $postData = [
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => 'test'
        ];
        // 休憩の追加
        foreach ($attendance->breakTimes as $breakTime) {
            $breakTimeStartFormat = Carbon::parse($breakTime->start)->addMinute()->format('H:i');
            $breakTimeEndFormat = Carbon::parse($breakTime->end)->addMinute()->format('H:i');
            $postData['break_time']['start'][$breakTime->id] = $breakTimeStartFormat;
            $postData['break_time']['end'][$breakTime->id] = $breakTimeEndFormat;
        }
        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH."/".$attendance->id,$postData);
        $this->post('/logout');

        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');
        // 承認画面確認
        $response = $this->followingRedirects()->get($this->ADMIN_STAMP_CORRECTION_REQUEST_APPROVE."/".$attendance->id);
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($postData['note']);

        // 申請一覧画面確認
        $response = $this->get($this->ADMIN_STAMP_CORRECTION_REQUEST);
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($postData['note']);

    }
    // 「承認待ち」にログインユーザーが行った申請が全て表示されていること
    public function test_check_ok_by_user()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::with('breakTimes')->where('user_id', $user->id)->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 出勤時間と退勤時刻で入力
        $clock_in = Carbon::parse($attendance->clock_in)->addMinute();
        $clock_out = Carbon::parse($attendance->clock_out)->addMinute();
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $postData = [
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => 'test'
        ];
        // 休憩の追加
        foreach ($attendance->breakTimes as $breakTime) {
            $breakTimeStartFormat = Carbon::parse($breakTime->start)->addMinute()->format('H:i');
            $breakTimeEndFormat = Carbon::parse($breakTime->end)->addMinute()->format('H:i');
            $postData['break_time']['start'][$breakTime->id] = $breakTimeStartFormat;
            $postData['break_time']['end'][$breakTime->id] = $breakTimeEndFormat;
        }
        $response = $this->post($this->ATTENDANCE_PATH."/".$attendance->id,$postData);

        // 申請一覧画面確認
        $response = $this->get($this->ADMIN_STAMP_CORRECTION_REQUEST);
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($postData['note']);

    }
    // 「承認済み」に管理者が承認した修正申請が全て表示されている
    public function test_check_approval()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::with('breakTimes')->where('user_id', $user->id)->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 出勤時間と退勤時刻で入力
        $clock_in = Carbon::parse($attendance->clock_in)->addMinute();
        $clock_out = Carbon::parse($attendance->clock_out)->addMinute();
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $postData = [
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => 'test'
        ];
        // 休憩の追加
        foreach ($attendance->breakTimes as $breakTime) {
            $breakTimeStartFormat = Carbon::parse($breakTime->start)->addMinute()->format('H:i');
            $breakTimeEndFormat = Carbon::parse($breakTime->end)->addMinute()->format('H:i');
            $postData['break_time']['start'][$breakTime->id] = $breakTimeStartFormat;
            $postData['break_time']['end'][$breakTime->id] = $breakTimeEndFormat;
        }
        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH."/".$attendance->id,$postData);
        $this->post('/logout');

        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');
        // 承認処理
        $response = $this->followingRedirects()->post($this->ADMIN_STAMP_CORRECTION_REQUEST_APPROVE."/".$attendance->id);

        //管理者ログアウト
        $this->post('/logout');

        // スタッフログイン
        $user = User::find(1);
        $this->actingAs($user);

        // 申請一覧(承認済み)画面確認
        $response = $this->get($this->ADMIN_STAMP_CORRECTION_REQUEST."?page=approval");
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($postData['note']);

    }
    // 各申請の「詳細」を押下すると申請詳細画面に遷移する
    public function test_check_detail()
    {
        // ログインする
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠詳細ページを開く
        $attendance = Attendance::with('breakTimes')->where('user_id', $user->id)->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 出勤時間と退勤時刻で入力
        $clock_in = Carbon::parse($attendance->clock_in)->addMinute();
        $clock_out = Carbon::parse($attendance->clock_out)->addMinute();
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $postData = [
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => 'test'
        ];
        // 休憩の追加
        foreach ($attendance->breakTimes as $breakTime) {
            $breakTimeStartFormat = Carbon::parse($breakTime->start)->addMinute()->format('H:i');
            $breakTimeEndFormat = Carbon::parse($breakTime->end)->addMinute()->format('H:i');
            $postData['break_time']['start'][$breakTime->id] = $breakTimeStartFormat;
            $postData['break_time']['end'][$breakTime->id] = $breakTimeEndFormat;
        }
        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH."/".$attendance->id,$postData);

        // 申請一覧画面確認
        $response = $this->get($this->ADMIN_STAMP_CORRECTION_REQUEST);
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($postData['note']);

        // 詳細ページへリンク
        $html = $response->getContent();
        preg_match('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>詳細<\/a>/i', $html, $matches);
        $detailLink = str_replace('http://localhost', '', $matches[1]);
        $response = $this->get($detailLink);
        $response->assertStatus(200);
        $response->assertSee($postData['note']);
    }

    ////////////////////勤怠詳細情報取得・修正機能（管理者）///////////////////////////
    // 勤怠詳細画面に表示されるデータが選択したものになっている
    public function test_admin_view(){
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // 勤怠詳細画面を開く
        $attendance = Attendance::with('breakTimes')->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 内容確認
        $checkData = [
            Carbon::parse($attendance->date)->format('Y-m-d'),
            $attendance->getTimeFormatted('clock_in'),
            $attendance->getTimeFormatted('clock_out'),
        ];
        foreach ($attendance->breakTimes as $breakTime) {
            $checkData[] = $breakTime->getTimeFormatted('start');
            $checkData[] = $breakTime->getTimeFormatted('end');
        }
        $response->assertSeeInOrder($checkData);
    }
    // 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_validation_clock_in(){
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // 勤怠詳細画面を開く
        $attendance = Attendance::with('breakTimes')->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 出勤時間が退勤時間より後の時間で修正
        $clock_in = Carbon::parse($attendance->clock_in);
        $clock_out = $clock_in->copy()->subMinutes(30);
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $postData = [
            'date' => Carbon::parse($attendance->date)->format('Y-m-d'),
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => 'test'
        ];
        foreach ($attendance->breakTimes as $breakTime) {
            $postData['break_time']['start'][$breakTime->id] = $breakTime->getTimeFormatted('start');
            $postData['break_time']['end'][$breakTime->id] = $breakTime->getTimeFormatted('end');
        }
        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH."/".$attendance->id,$postData);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');

    }
    // 休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_validation_break_start(){
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // 勤怠詳細画面を開く
        $attendance = Attendance::with('breakTimes')->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 休憩開始が退勤時間より後の時間で修正
        $clock_in = Carbon::parse($attendance->clock_in);
        $clock_out = Carbon::parse($attendance->clock_out);
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $checkData = [
            'date' => Carbon::parse($attendance->date)->format('Y-m-d'),
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => 'test'
        ];
        foreach ($attendance->breakTimes as $breakTime) {
            $checkData['break_time']['start'][$breakTime->id] = $clock_out->copy()->addMinutes(30)->format('H:i');
            $checkData['break_time']['end'][$breakTime->id] = $breakTime->getTimeFormatted('end');
        }
        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH."/".$attendance->id,$checkData);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');

    }
    // 休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_validation_break_end(){
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // 勤怠詳細画面を開く
        $attendance = Attendance::with('breakTimes')->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 休憩開始が退勤時間より後の時間で修正
        $clock_in = Carbon::parse($attendance->clock_in);
        $clock_out = Carbon::parse($attendance->clock_out);
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $checkData = [
            'date' => Carbon::parse($attendance->date)->format('Y-m-d'),
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => 'test'
        ];
        foreach ($attendance->breakTimes as $breakTime) {
            $checkData['break_time']['start'][$breakTime->id] = $breakTime->getTimeFormatted('start');
            $checkData['break_time']['end'][$breakTime->id] = $clock_out->copy()->addMinutes(30)->format('H:i');
        }
        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH."/".$attendance->id,$checkData);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');

    }
    // 備考欄が未入力の場合のエラーメッセージが表示される
    public function test_admin_validation_note(){
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // 勤怠詳細画面を開く
        $attendance = Attendance::with('breakTimes')->latest('id')->first();
        $response = $this->get($this->ATTENDANCE_PATH."/".$attendance->id);
        $response->assertStatus(200);

        // 休憩開始が退勤時間より後の時間で修正
        $clock_in = Carbon::parse($attendance->clock_in);
        $clock_out = Carbon::parse($attendance->clock_out);
        $clock_inFormat = $clock_in->format('H:i');
        $clock_outFormat = $clock_out->format('H:i');
        $checkData = [
            'date' => Carbon::parse($attendance->date)->format('Y-m-d'),
            'clock_in' => $clock_inFormat,
            'clock_out' => $clock_outFormat,
            'note' => ''
        ];
        foreach ($attendance->breakTimes as $breakTime) {
            $checkData['break_time']['start'][$breakTime->id] = $breakTime->getTimeFormatted('start');
            $checkData['break_time']['end'][$breakTime->id] = $breakTime->getTimeFormatted('end');
        }
        $response = $this->followingRedirects()->post($this->ATTENDANCE_PATH."/".$attendance->id,$checkData);
        $response->assertSee('備考を記入してください');
    }
}
