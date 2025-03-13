<?php
namespace App\Http\Responses;

use App\Models\User;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect('/admin/attendance/list'); // 管理者用のダッシュボード
        } elseif
         (Auth::guard('web')->check()) {
            $user = User::where('email', $request->email)->first();
            // メール認証がまだなら `/email/verify` にリダイレクト
            if (!$user->hasVerifiedEmail()) {
                // 認証メールを送信
                $user->sendEmailVerificationNotification();
                return redirect('/email/verify');
            }

            return redirect('/attendance'); // 一般ユーザーのホーム画面
        }

        return redirect('/');
    }
}
