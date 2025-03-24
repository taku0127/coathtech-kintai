<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceFixes;
use App\Models\BreakTimeFixes;
use Illuminate\Http\Request;

class AttendanceDetailController extends Controller
{
    //
    public function userIndex($id){
        $attendance = Attendance::find($id);
        return view('pages.attendance_detail', compact('attendance'));
    }
    public function userStore(Request $request,$id){
        $attendance = Attendance::find($id);
        $attendance->update([
            'approval' => false,
        ]);
        AttendanceFixes::create([
            'attendance_id' => $id,
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'note' => $request->note,
        ]);
        $breakTimesId = $attendance->breakTimes->pluck('id')->toArray();
        foreach ($breakTimesId as $breakTimeId) {
            BreakTimeFixes::create([
                'break_time_id' => $breakTimeId,
               'start' => $request->break_time['start'][$breakTimeId],
               'end' => $request->break_time['end'][$breakTimeId],
            ]);
        }

        return redirect(route('user.attendance_detail',['id' => $id]));
    }
}
