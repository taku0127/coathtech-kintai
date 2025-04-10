<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Helpers\AbstractTestCase;

class AuthTest extends AbstractTestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */


    ///////////////////////////認証機能（一般ユーザー）//////////////////////////////////
    // 名前が未入力の場合、バリデーションメッセージが表示される
    public function test_register_required_name()
    {
        // ページ確認
        $response = $this->get($this->REGISTER_PATH);
        $response->assertStatus(200);

        // ユーザー情報をポストする。
        $postData = [
            'email' => 'test@example.com',
            'password' => '00000000',
            'password_confirmation' => '00000000'
        ];
        $response = $this->followingRedirects()->post($this->REGISTER_PATH, $postData);

        // エラーが正しく表示されているか確認
        $response->assertSee('お名前を入力してください');
    }

    //メールアドレスが未入力の場合、バリデーションメッセージが表示される
    public function test_register_required_mail()
    {
        // ページ確認
        $response = $this->get($this->REGISTER_PATH);
        $response->assertStatus(200);

        // ユーザー情報をポストする。
        $postData = [
            'name' => 'テスト',
            'password' => '00000000',
            'password_confirmation' => '00000000'
        ];
        $response = $this->followingRedirects()->post($this->REGISTER_PATH, $postData);

        // エラーが正しく表示されているか確認
        $response->assertSee('メールアドレスを入力してください');
    }

    // パスワードが8文字未満の場合、バリデーションメッセージが表示される
    public function test_register_short_password()
    {
        // ページ確認
        $response = $this->get($this->REGISTER_PATH);
        $response->assertStatus(200);

        // ユーザー情報をポストする。
        $postData = [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => '0000',
            'password_confirmation' => '0000'
        ];
        $response = $this->followingRedirects()->post($this->REGISTER_PATH, $postData);

        // エラーが正しく表示されているか確認
        $response->assertSee('パスワードは8文字以上で入力してください');
    }

    //パスワードが一致しない場合、バリデーションメッセージが表示される
    public function test_register_not_match_password()
    {
        // ページ確認
        $response = $this->get($this->REGISTER_PATH);
        $response->assertStatus(200);

        // ユーザー情報をポストする。
        $postData = [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => '00000000',
            'password_confirmation' => '11111111'
        ];
        $response = $this->followingRedirects()->post($this->REGISTER_PATH, $postData);

        // エラーが正しく表示されているか確認
        $response->assertSee('パスワードと一致しません');
    }

    //パスワードが未入力の場合、バリデーションメッセージが表示される
    public function test_register_require_password()
    {
        // ページ確認
        $response = $this->get($this->REGISTER_PATH);
        $response->assertStatus(200);

        // ユーザー情報をポストする。
        $postData = [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password_confirmation' => '00000000'
        ];
        $response = $this->followingRedirects()->post($this->REGISTER_PATH, $postData);

        // エラーが正しく表示されているか確認
        $response->assertSee('パスワードを入力してください');
    }

    //フォームに内容が入力されていた場合、データが正常に保存される
    public function test_register_ok()
    {
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

        // データベースに情報が登録されているか確認
        $checkData = $postData;
        unset($checkData['password_confirmation'],$checkData['password']);  //password_comfirmationは登録されないので削除,passwordはハッシュ化でチェックできないので削除
        $this->assertDatabaseHas('users',$checkData);
    }

    ///////////////////////////ログイン認証機能（一般ユーザー）//////////////////////////////////
    // メールアドレスが未入力の場合、バリデーションメッセージが表示される
    public function test_user_login_required_mail(){
        // ユーザーの登録
        $postData = [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => '00000000',
            'password_confirmation' => '00000000'
        ];
        $this->post($this->REGISTER_PATH, $postData);

        // データベースに情報が登録されているか確認
        $checkData = $postData;
        unset($checkData['password_confirmation'],$checkData['password']);  //password_comfirmationは登録されないので削除,passwordはハッシュ化でチェックできないので削除
        $this->assertDatabaseHas('users',$checkData);
        // loginされてしまうのでlogoutする
        $this->post($this->LOGOUT_PATH);
        $this->assertGuest();

        // ログイン画面チェック
        $response = $this->get($this->USER_LOGIN_PATH);
        $response->assertStatus(200);

        // ログイン処理・email欠損
        $loginData = $postData;
        unset($loginData['name'],$loginData['password_confirmation'],$loginData['email']);
        $response = $this->followingRedirects()->post($this->USER_LOGIN_PATH, $loginData);

        // エラー文のチェック
        $response->assertSee('メールアドレスを入力してください');
    }

    // パスワードが未入力の場合、バリデーションメッセージが表示される
    public function test_user_login_required_password(){
        // ユーザーの登録
        $postData = [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => '00000000',
            'password_confirmation' => '00000000'
        ];
        $this->post($this->REGISTER_PATH, $postData);

        // データベースに情報が登録されているか確認
        $checkData = $postData;
        unset($checkData['password_confirmation'],$checkData['password']);  //password_comfirmationは登録されないので削除,passwordはハッシュ化でチェックできないので削除
        $this->assertDatabaseHas('users',$checkData);

        // loginされてしまうのでlogoutする
        $this->post($this->LOGOUT_PATH);
        $this->assertGuest();

        // ログイン画面チェック
        $response = $this->get($this->USER_LOGIN_PATH);
        $response->assertStatus(200);

        // ログイン処理・パスワード欠損
        $loginData = $postData;
        unset($loginData['name'],$loginData['password_confirmation'],$loginData['password']);
        $response = $this->followingRedirects()->post($this->USER_LOGIN_PATH, $loginData);

        // エラー文のチェック
        $response->assertSee('パスワードを入力してください');
    }
    // 登録内容と一致しない場合、バリデーションメッセージが表示される
    public function test_user_login_missmatch(){
        // ユーザーの登録
        $postData = [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => '00000000',
            'password_confirmation' => '00000000'
        ];
        $this->post($this->REGISTER_PATH, $postData);

        // データベースに情報が登録されているか確認
        $checkData = $postData;
        unset($checkData['password_confirmation'],$checkData['password']);  //password_comfirmationは登録されないので削除,passwordはハッシュ化でチェックできないので削除
        $this->assertDatabaseHas('users',$checkData);

        // loginされてしまうのでlogoutする
        $this->post($this->LOGOUT_PATH);
        $this->assertGuest();

        // ログイン画面チェック
        $response = $this->get($this->USER_LOGIN_PATH);
        $response->assertStatus(200);

        // ログイン処理・情報の不一致
        $loginData = $postData;
        unset($loginData['name'],$loginData['password_confirmation']);
        $loginData['email'] = 'tes@example.com';
        $response = $this->followingRedirects()->post($this->USER_LOGIN_PATH, $loginData);

        // エラー文のチェック
        $response->assertSee('ログイン情報が登録されていません');
    }

    ///////////////////////////ログイン認証機能（管理者）//////////////////////////////////
    // メールアドレスが未入力の場合、バリデーションメッセージが表示される
    public function test_admin_login_required_mail(){
        // 手動でデータを挿入
        $postData = [
            'email' => 'testadmin@example.com',
            'password' => bcrypt('00000000')
        ];
        DB::table('admins')->insert($postData);
        // データベースに情報が登録されているか確認
        $checkData = $postData;
        $this->assertDatabaseHas('admins',$checkData);

        // adminのログインページへ遷移
        $response = $this->get($this->ADMIN_LOGIN_PATH);
        $response->assertStatus(200);

        // ログイン処理・メールアドレス欠損
        $loginData = $postData;
        unset($loginData['email']);
        $response = $this->followingRedirects()->post($this->ADMIN_LOGIN_PATH, $loginData);

        // エラー文が表示されているか確認
        $response->assertSee('メールアドレスを入力してください');
    }

    // パスワードが未入力の場合、バリデーションメッセージが表示される
    public function test_admin_login_required_password(){
        // 手動でデータを挿入
        $postData = [
            'email' => 'testadmin@example.com',
            'password' => bcrypt('00000000')
        ];
        DB::table('admins')->insert($postData);
        // データベースに情報が登録されているか確認
        $checkData = $postData;
        $this->assertDatabaseHas('admins',$checkData);

        // adminのログインページへ遷移
        $response = $this->get($this->ADMIN_LOGIN_PATH);
        $response->assertStatus(200);

        // ログイン処理・パスワード欠損
        $loginData = $postData;
        unset($loginData['password']);
        $response = $this->followingRedirects()->post($this->ADMIN_LOGIN_PATH, $loginData);

        // エラー文が表示されているか確認
        $response->assertSee('パスワードを入力してください');
    }
    // 登録内容と一致しない場合、バリデーションメッセージが表示される
    public function test_admin_login_missmatch(){
        // 手動でデータを挿入
        $postData = [
            'email' => 'testadmin@example.com',
            'password' => bcrypt('00000000')
        ];
        DB::table('admins')->insert($postData);
        // データベースに情報が登録されているか確認
        $checkData = $postData;
        $this->assertDatabaseHas('admins',$checkData);

        // adminのログインページへ遷移
        $response = $this->get($this->ADMIN_LOGIN_PATH);
        $response->assertStatus(200);

        // ログイン処理・不一致
        $loginData = $postData;
        $loginData['email'] = 'testadmi@example.com';
        $response = $this->followingRedirects()->post($this->ADMIN_LOGIN_PATH, $loginData);

        // エラー文が表示されているか確認
        $response->assertSee('ログイン情報が登録されていません');
    }
}
