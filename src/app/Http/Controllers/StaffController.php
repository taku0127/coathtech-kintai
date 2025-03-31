<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    //
    public function staffListIndex(){
        $users = User::all();
        return view('pages.admin.staff_list',compact('users'));
    }

    public function staffAttendanceIndex(Request $request,$id){
        $user = User::find($id);
        $page = $request->query('page',0);
        $targetDate = Carbon::now()->startOfMonth()->addMonths($page);
        $year = $targetDate->year;
        $month = $targetDate->month;
        $attendances = Attendance::where('user_id', $user->id)->whereYear('date',$year)->whereMonth('date',$month)->orderBy('date','desc')->get();
        $title = $user->name.'さんの勤怠';
        return view('pages.attendance_list',compact('attendances','page','year','month','title'));
    }
}
