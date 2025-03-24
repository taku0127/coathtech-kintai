<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\Auth\LoginController;
use App\Models\Attendance;
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

Route::middleware(['auth:web','verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class , 'index'])
    ->name('user.attendance');
    Route::post('/attendance',[AttendanceController::class, 'storeClockIn']);
    Route::post('/attendance/break_start',[AttendanceController::class, 'storeBreakStart'])->name('user.break_start');
    Route::patch('/attendance/break_end',[AttendanceController::class, 'storeBreakEnd'])->name('user.break_end');
    Route::patch('/attendance/clock_out',[AttendanceController::class, 'storeClockOut'])->name('user.clock_out');
    Route::get('/attendance/list',[AttendanceListController::class, 'userIndex'])->name('user.attendance_list');
    Route::get('/attendance/{id}', [AttendanceDetailController::class, 'userIndex'])->name('user.attendance_detail');
    Route::post('/attendance/{id}', [AttendanceDetailController::class, 'userStore']);
});
Route::get('/stamp_correction_request/list', function () {
    return view('pages/stamp_correction_request_list');
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
