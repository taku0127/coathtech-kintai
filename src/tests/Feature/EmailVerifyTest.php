<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Helpers\AbstractTestCase;

class EmailVerifyTest extends AbstractTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /////////////////////////メール認証機能////////////////////////////////

    // 会員登録後、認証メールが送信される
    public function test_send_mail(){
        Notification::fake();

        // 会員登録
        // ページ確認
        $response = $this->get($this->REGISTER_PATH);
        $response->assertStatus(200);

        // ユーザー情報をポストする。
        $postData = [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => '00000000',
            'password_confirmation' => '00000000'
        ];
        $response = $this->followingRedirects()->post($this->REGISTER_PATH, $postData);
        $user = User::where('email',$postData['email'])->first();

        // メール送信チェック
        Notification::assertSentTo(
            $user,
            VerifyEmail::class,
            function ($notification, $channels) use ($user) {
                $mailMessage = $notification->toMail($user);
                $url = $mailMessage->actionUrl;
                $this->assertStringContainsString('/email/verify', $url);
                return true;
            }
        );
    }

    // メール認証誘導画面で「認証はこちらから」ボタンを押下するとメール認証サイトに遷移する
    public function test_verify_view(){

        // 会員登録
        // ページ確認
        $response = $this->get($this->REGISTER_PATH);
        $response->assertStatus(200);

        // ユーザー情報をポストする。
        $postData = [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => '00000000',
            'password_confirmation' => '00000000'
        ];
        $response = $this->followingRedirects()->post($this->REGISTER_PATH, $postData);

        // メール認証クリック
        $response = $this->get('/email/verify');
        $response->assertStatus(200);

    }
    // メール認証サイトのメール認証を完了すると、商品一覧ページに遷移する
    public function test_verify_ok(){
        Notification::fake();

        // 会員登録
        // ページ確認
        $response = $this->get($this->REGISTER_PATH);
        $response->assertStatus(200);

        // ユーザー情報をポストする。
        $postData = [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => '00000000',
            'password_confirmation' => '00000000'
        ];
        $response = $this->followingRedirects()->post($this->REGISTER_PATH, $postData);
        $user = User::where('email',$postData['email'])->first();

        // メール送信チェック
        Notification::assertSentTo(
            $user,
            VerifyEmail::class,
            function ($notification, $channels) use ($user,&$verificationUrl) {
                $mailMessage = $notification->toMail($user);
                $verificationUrl = $mailMessage->actionUrl;
                $this->assertStringContainsString('/email/verify', $verificationUrl);
                return true;
            }
        );
        $response = $this->get($verificationUrl);
        $response->assertRedirect($this->ATTENDANCE_PATH);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }
}
