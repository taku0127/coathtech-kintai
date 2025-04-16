<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceRequest;
use App\Models\Attendance;
use App\Models\AttendanceFixes;
use App\Models\BreakTimeFixes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceDetailController extends Controller
{
    //
    public function index($id){
        $attendance = Attendance::with(['attendanceFix' => function ($query){
            $query->notApproved()->first();
        }])->with(['breakTimes.breakTimeFix' => function($query){
            $query->notApproved();
        }])->find($id);
        return view('pages.attendance_detail', compact('attendance'));
    }

    public function store(AttendanceRequest $request,$id){
        if (Auth::guard('admin')->check()) {
            // 管理者としてログイン中
            return $this->adminStore($request,$id);
        } elseif (Auth::guard('web')->check()) {
            // 一般ユーザーとしてログイン中
            return $this->userStore($request,$id);
        } else {
            // 未ログイン
            return redirect(route('/login'));
        }
    }
    private function userStore(AttendanceRequest $request,$id){
        $attendance = Attendance::find($id);
        $attendance->update([
            'approval' => false,
        ]);
        $attendanceFix = AttendanceFixes::create([
            'attendance_id' => $id,
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'note' => $request->note,
        ]);
        $breakTimesId = $attendance->breakTimes->pluck('id')->toArray();
        foreach ($breakTimesId as $breakTimeId) {
            BreakTimeFixes::create([
                'break_time_id' => $breakTimeId,
                'attendance_fix_id' => $attendanceFix->id,
               'start' => $request->break_time['start'][$breakTimeId],
               'end' => $request->break_time['end'][$breakTimeId],
            ]);
        }

        return redirect(route('attendance_detail',['id' => $id]));
    }
    private function adminStore(AttendanceRequest $request,$id){
        $attendance = Attendance::find($id);
        $attendance->update([
            'date' => $request->date,
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'note' => $request->note,
        ]);
        $breakTimes = $attendance->breakTimes;
        foreach ($breakTimes as $breakTime) {
            $breakTime->update([
                'start' => $request->break_time['start'][$breakTime->id],
                'end' => $request->break_time['end'][$breakTime->id],
            ]);
        }

        return redirect(route('attendance_detail',['id' => $id]));
    }
}
