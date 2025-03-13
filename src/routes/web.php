<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/auth/mail', function () {
    return view('auth/mail');
});
Route::get('/attendance', function () {
    return view('pages/staff/attendance');
})->middleware(['auth','verified']);
Route::get('/attendance/list', function () {
    return view('pages/attendance_list');
});
Route::get('/stamp_correction_request/list', function () {
    return view('pages/stamp_correction_request_list');
});
Route::get('/attendance/{id}', function () {
    return view('pages/attendance_detail');
});
Route::get('/admin/attendance/list', function () {
    return view('pages/admin/attendance_list');
});
Route::get('/admin/staff/list', function () {
    return view('pages/admin/staff_list');
});
Route::get('/admin/attendance/staff/{id}', function () {
    return view('pages/attendance_detail');
});

Route::post('/logout', function () {
    if (Auth::guard('admin')->check()) {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    } elseif (Auth::guard('web')->check()) {
        Auth::guard('web')->logout();
        return redirect('/login');
    }
    return redirect('/login');
})->name('logout');
Route::prefix('admin')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('admin.login');
    Route::post('login', [LoginController::class, 'store']);
});
