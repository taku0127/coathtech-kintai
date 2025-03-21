<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceListController extends Controller
{
    //
    public function userIndex(Request $request){

        $page = $request->query('page',0);
        $targetDate = Carbon::now()->addMonths($page);
        $year = $targetDate->year;
        $month = $targetDate->month;
        $user_id = Auth::id();
        $attendances = Attendance::where('user_id', $user_id)->whereYear('date',$year)->whereMonth('date',$month)->orderBy('date','desc')->get();
        return view('pages.attendance_list', compact('attendances','year','month','page'));
    }
}
