<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;
use Tests\Feature\Helpers\AbstractTestCase;

class AdminAttendanceListTest extends AbstractTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //////////////////////勤怠一覧情報取得機能（管理者）//////////////////////////
    // その日になされた全ユーザーの勤怠情報が正確に確認できる
    public function test_attendance_list()
    {
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // 勤怠一覧画面を開く
        $response = $this->get($this->ADMIN_ATTENDANCE_LIST);
        //詳細リンクがあるかチェック
        $html = $response->getContent();
        $detailLink = preg_match('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>詳細<\/a>/i', $html, $match);
        $pageCount = 0;
        while(!$detailLink){
            $pageCount--;
            $response = $this->get($this->ADMIN_ATTENDANCE_LIST.'?page='.$pageCount);
            $html = $response->getContent();
            $detailLink = preg_match('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>詳細<\/a>/i', $html, $match);
        }
        // 詳細リンクがあれば日付取得
        preg_match('/<span class="c-pageNation_current">([^<]*)<\/span>/', $html, $match);
        $date = Carbon::parse($match[1]);

        // 該当日付のデータをDBから取得
        $attendances = Attendance::with('user')->whereDate('date',$date)->get();
        foreach ($attendances as $attendance) {
            $response->assertSeeInOrder([$attendance->user->name,Carbon::createFromTimeString($attendance->clock_in)->format('H:i'),Carbon::createFromTimeString($attendance->clock_out)->format('H:i')]);
        }

    }
    // 遷移した際に現在の日付が表示される
    public function test_check_date()
    {
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // 勤怠一覧画面を開く
        $response = $this->get($this->ADMIN_ATTENDANCE_LIST);

        // 現在の日付が表示されているか確認
        $dateView = Carbon::now()->format("Y/m/d");
        $response->assertSee($dateView);
    }
    // 「前日」を押下した時に前の日の勤怠情報が表示される
    public function test_check_yesterday()
    {
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        // 勤怠一覧画面を開く
        $response = $this->get($this->ADMIN_ATTENDANCE_LIST."/?page=-1");

        // 前日の日付が表示されているか確認
        $dateView = Carbon::now()->subDay()->format("Y/m/d");
        $response->assertSee($dateView);

         // 該当日付のデータをDBから取得し表示確認
         $attendances = Attendance::with('user')->whereDate('date',$dateView)->get();
         foreach ($attendances as $attendance) {
             $response->assertSeeInOrder([$attendance->user->name,Carbon::createFromTimeString($attendance->clock_in)->format('H:i'),Carbon::createFromTimeString($attendance->clock_out)->format('H:i')]);
         }
    }
    // 「翌日」を押下した時に次の日の勤怠情報が表示される
    public function test_check_tommorow()
    {
        //管理者ログイン
        $admin = Admin::find(1);
        $this->actingAs($admin,'admin');

        $checkPages = [-2,-1,1]; // 二日前、一日前、翌日をチェック

        foreach($checkPages as $checkPage){
            // 2日前の勤怠一覧画面を開く
            $response = $this->get($this->ADMIN_ATTENDANCE_LIST."/?page=".$checkPage);

            // 2日前の日付が表示されているか確認
            $dateView = Carbon::now()->addDays($checkPage)->format("Y/m/d");
            $response->assertSee($dateView);

             // 該当日付のデータをDBから取得し表示確認
             $attendances = Attendance::with('user')->whereDate('date',$dateView)->get();
             foreach ($attendances as $attendance) {
                 $response->assertSeeInOrder([$attendance->user->name,Carbon::createFromTimeString($attendance->clock_in)->format('H:i'),Carbon::createFromTimeString($attendance->clock_out)->format('H:i')]);
             }
        }
    }
}
