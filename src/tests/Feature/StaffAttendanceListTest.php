<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Tests\Feature\Helpers\AbstractTestCase;
use Symfony\Component\DomCrawler\Crawler;


class StaffAttendanceListTest extends AbstractTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    ////////////////////////勤怠一覧情報取得機能（一般ユーザー）////////////////////////////
    // 自分が行った勤怠情報が全て表示されている
    public function test_get_attendance_list(){
        // ユーザーログイン
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠一覧ページを開く
        $response = $this->get($this->ATTENDANCE_LIST_PATH);
        $response->assertStatus(200);

        // 自分の勤怠一覧を月ごとに取得して月ごとに分ける。
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
            $response = $this->get($this->ATTENDANCE_LIST_PATH.'?page='.$pageCount);
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
    // 勤怠一覧画面に遷移した際に現在の月が表示される
    public function test_get_attendance_list_current_month(){
        // ユーザーログイン
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠一覧ページを開く
        $response = $this->get($this->ATTENDANCE_LIST_PATH);
        $response->assertStatus(200);

        // 現在の年月の取得
        $currentYearMonth = Carbon::now()->format('Y/n');
        $response->assertSee($currentYearMonth);
    }
    // 「前月」を押下した時に表示月の前月の情報が表示される
    public function test_get_attendance_list_before_month(){
        // ユーザーログイン
        $user = User::find(1);
        $this->actingAs($user);
        $page = -1;
        // 勤怠一覧ページを開く
        $response = $this->get($this->ATTENDANCE_LIST_PATH);
        $response->assertStatus(200);

        // 前月に遷移
        $response = $this->get($this->ATTENDANCE_LIST_PATH."?page=".$page);
        $response->assertStatus(200);

        // 現在の年月の取得
        $beforeYearMonth = Carbon::now()->subMonth()->format('Y/n');
        $response->assertSee($beforeYearMonth);
    }
    // 「翌月」を押下した時に表示月の前月の情報が表示される
    public function test_get_attendance_list_after_month(){
        // ユーザーログイン
        $user = User::find(1);
        $this->actingAs($user);
        $page = 1;
        // 勤怠一覧ページを開く
        $response = $this->get($this->ATTENDANCE_LIST_PATH);
        $response->assertStatus(200);

        // 前月に遷移
        $response = $this->get($this->ATTENDANCE_LIST_PATH."?page=".$page);
        $response->assertStatus(200);

        // 現在の年月の取得
        $nextYearMonth = Carbon::now()->addMonth()->format('Y/n');
        $response->assertSee($nextYearMonth);
    }
    // 「詳細」を押下すると、その日の勤怠詳細画面に遷移する
    public function test_get_attendance_list_link_detail(){
        // ユーザーログイン
        $user = User::find(1);
        $this->actingAs($user);
        // 勤怠一覧ページを開く
        $response = $this->get($this->ATTENDANCE_LIST_PATH);
        $response->assertStatus(200);

        // aタグの取得
        $html = $response->getContent(); // FeatureテストのレスポンスからHTMLを取得
        preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>詳細<\/a>/i', $html, $matches);
        // データがなければ前月のリンクをクリック
        if (empty($matches[0])) {
            $page = -1;
            $response = $this->get($this->ATTENDANCE_LIST_PATH."?page=".$page);
            $response->assertStatus(200);
            preg_match_all('/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>詳細<\/a>/i', $html, $matches);
        }
        $detailLink = str_replace('http://localhost', '', $matches[1][0]);

        // 詳細へ遷移
        $response = $this->get($detailLink);
        $response->assertStatus(200);

    }
}
