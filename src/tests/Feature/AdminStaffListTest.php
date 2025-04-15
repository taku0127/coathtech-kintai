<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Tests\Feature\Helpers\AbstractTestCase;

class AdminStaffListTest extends AbstractTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    ////////////////////////ユーザー情報取得機能（管理者）////////////////////////
    // 管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる
    public function test_view(){
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // スタッフ一覧ページ
        $response = $this->get($this->ADMIN_STAFF_LIST);
        $response->assertStatus(200);

        // user情報を取得できているか確認
        $users = User::all();
        foreach ($users as $user) {
            $checkData = [
                $user->name,
                $user->email
            ];
            $response->assertSeeInOrder($checkData);
        }
    }
    // ユーザーの勤怠情報が正しく表示される / 「前月」を押下した時に表示月の前月の情報が表示される
    public function test_attendance_list(){
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // user情報の取得
        $user = User::find(1);

        // スタッフ別詳細ページ
        $response = $this->get($this->ADMIN_ATTENDANCE_STAFF."/".$user->id);
        $response->assertStatus(200);

        // userの勤怠一覧を月ごとに取得して月ごとに分ける。
        $attendances = Attendance::where('user_id', $user->id)->get();
        $groupByMonthDesc = $attendances->groupBy(function($attendance){
            return Carbon::parse($attendance->date)->format('Y-m');
        })->sortKeysDesc();
        // データの確認
        $pageCount = 0;
        foreach($groupByMonthDesc as $key => $attendanceByMonth){
            // 今月と一番新しい月のデータが一致しているか
            if(Carbon::now()->format('Y-m') != $key){
            $pageCount--;
            }
            // 一致していたらそのデータとattendance_listページのデータで一致確認
            $response = $this->get($this->ADMIN_ATTENDANCE_STAFF."/".$user->id.'?page='.$pageCount);
            foreach($attendanceByMonth as $attendance){
                $checkData = [
                    'date' => Carbon::parse($attendance->date)->format('m/d'),
                    'clock_in' => Carbon::createFromTimeString($attendance->clock_in)->format('H:i'),
                    'clock_out' => Carbon::createFromTimeString($attendance->clock_out)->format('H:i')
                ];
                $response->assertSeeInOrder($checkData);
            }
        }
    }
    // 「翌月」を押下した時に表示月の前月の情報が表示される
    public function test_attendance_list_after_month(){
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // user情報の取得
        $user = User::find(1);

        // スタッフ別詳細ページ
        $response = $this->get($this->ADMIN_ATTENDANCE_STAFF."/".$user->id."?page=1");
        $response->assertStatus(200);

        // userの勤怠一覧を翌月の情報を取得。
        $after1Month = Carbon::now()->addMonth();
        $attendances = Attendance::where('user_id', $user->id)->whereDate('date',$after1Month)->get();
        // データの確認
        foreach($attendances as $attendance){
            $checkData = [
                'date' => Carbon::parse($attendance->date)->format('m/d'),
                'clock_in' => Carbon::createFromTimeString($attendance->clock_in)->format('H:i'),
                'clock_out' => Carbon::createFromTimeString($attendance->clock_out)->format('H:i')
            ];
            $response->assertSeeInOrder($checkData);
        }
    }
    // 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function test_attendance_list_check_detail(){
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // user情報の取得
        $user = User::find(1);

        // スタッフ別詳細ページ
        $response = $this->get($this->ADMIN_ATTENDANCE_STAFF."/".$user->id."?page=1");
        $response->assertStatus(200);

        //詳細リンクがあるかチェック
        $html = $response->getContent();
        $detailLink = preg_match('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>詳細<\/a>/i', $html, $match);
        $pageCount = 0;
        while(!$detailLink){
            $pageCount--;
            $response = $this->get($this->ADMIN_ATTENDANCE_STAFF."/".$user->id.'?page='.$pageCount);
            $html = $response->getContent();
            $detailLink = preg_match('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>詳細<\/a>/i', $html, $match);
        }
        $datailLink = str_replace('http://localhost', '', $match[1]);
        $response = $this->get($datailLink);
        $response->assertStatus(200);
    }


}
