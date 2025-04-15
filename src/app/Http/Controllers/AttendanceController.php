<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    //
    private function isBreakStart($attendance_id){
        return BreakTime::where('attendance_id', $attendance_id)->whereNull('end')->exists();
    }

    public function index(){
        $user_id = Auth::id();
        $attendance = Attendance::where('user_id', $user_id)
        ->where('date', now()->format('Y-m-d'))
        ->first();
        $isBreak = $attendance ? $this->isBreakStart($attendance->id) : false;
        return view('pages.staff.attendance',compact('attendance','isBreak'));
    }

    public function storeClockIn(Request $request){
        Attendance::create([
            'user_id' => Auth::id(),
            'date' => now(),
            'clock_in' => $request->clock_in,
        ]);
        return redirect(route('user.attendance'));
    }

    public function storeClockOut(Request $request){
        $attendance = Attendance::find($request->attendance_id);
        $attendance->update(['clock_out' => $request->clock_out]);
        return redirect(route('user.attendance'));
    }

    public function storeBreakStart(Request $request){
        BreakTime::create([
            'attendance_id' => $request->attendance_id,
            'start' => $request->start,
        ]);
        return redirect(route('user.attendance'));
    }

    public function storeBreakEnd(Request $request){
        $breakTime = BreakTime::where('attendance_id', $request->attendance_id)
        ->whereNull('end')
        ->first();
        $breakTime->update(['end' => $request->end]);
        return redirect(route('user.attendance'));
    }
}
