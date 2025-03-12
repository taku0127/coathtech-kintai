<?php
namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // ログインしているガードに応じてリダイレクト先を変更
        if (Auth::guard('admin')->check()) {
            return redirect('/admin/attendance/list'); // 管理者用のダッシュボード
        } elseif
         (Auth::guard('web')->check()) {
            return redirect('/attendance'); // 一般ユーザーのホーム画面
        }

        return redirect('/'); // デフォルトのリダイレクト
    }
}
