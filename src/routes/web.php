<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/admin/login', function () {
    return view('auth/admin_login');
});
Route::get('/auth/mail', function () {
    return view('auth/mail');
});
Route::get('/attendance', function () {
    return view('pages/staff/attendance');
});
Route::get('/attendance/list', function () {
    return view('pages/staff/attendance_list');
});
Route::get('/stamp_correction_request/list', function () {
    return view('pages/stamp_correction_request_list');
});
Route::get('/attendance/{id}', function () {
    return view('pages/staff/attendance_detail');
});
