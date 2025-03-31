<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceDetailController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StampCrrectionRequestController;
use App\Http\Controllers\StaffController;
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

});

Route::middleware(['auth:admin'])->group(function(){
    Route::get('/admin/attendance/list',[AttendanceListController::class,'adminIndex'])->name('admin.attendance_list');
    Route::get('/admin/staff/list', [StaffController::class, 'staffListIndex'])->name('admin.staff_list');
    Route::get('/admin/attendance/staff/{id}', [StaffController::class, 'staffAttendanceIndex'])->name('admin.staff_detail');
    Route::get('/export_csv' , [StaffController::class, 'exportCsv'])->name('exportCsv');
});

Route::middleware(['auth:web,admin'])->group(function(){
    Route::get('/attendance/{id}', [AttendanceDetailController::class, 'index'])->name('attendance_detail');
    Route::post('/attendance/{id}', [AttendanceDetailController::class, 'store']);
    Route::get('/stamp_correction_request/list', [StampCrrectionRequestController::class, 'userIndex'])->name('stamp_correction_request');
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
