<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceFixes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StampCrrectionRequestController extends Controller
{
    //
    public function index(Request $request){
        if (Auth::guard('admin')->check()) {
            // 管理者としてログイン中
            return $this->adminIndex($request);
        } elseif (Auth::guard('web')->check()) {
            // 一般ユーザーとしてログイン中
            return $this->userIndex($request);
        } else {
            // 未ログイン
            return redirect(route('/login'));
        }
    }

    private function userIndex(Request $request){
        $userId = Auth::id();
        $pageParam = $request->query('page');
        if($pageParam != 'approval'){
            $attendances = AttendanceFixes::where([
                ['approval', '=', false]
                ])->whereHas('attendance', function ($query) use ($userId){
                    $query->where('user_id',$userId);
                })->with('attendance')->get();
        }else {
            $attendances = AttendanceFixes::where([
                ['approval', '=', true]
                ])->whereHas('attendance', function ($query) use ($userId){
                    $query->where('user_id',$userId);
                })->with('attendance')->get();
        }
        return view('pages.stamp_correction_request_list', compact('attendances','pageParam'));
    }
    private function adminIndex(Request $request){
        $pageParam = $request->query('page');
        if($pageParam != 'approval'){
            $attendances = AttendanceFixes::where([
                ['approval', '=', false]
                ])->with('attendance')->get();
        }else {
            $attendances = AttendanceFixes::where([
                ['approval', '=', true]
                ])->with('attendance')->get();
        }
        return view('pages.stamp_correction_request_list', compact('attendances','pageParam'));
    }

    public function adminApprove($id){
        $attendance = AttendanceFixes::with('attendance.user','attendance.breakTimes.breakTimeFix')->find($id);
        return view('pages.stamp_correction_request_detail',compact('attendance'));
    }

    public function adminApproveStore($id){
        $attendanceFix = AttendanceFixes::with('attendance.breakTimes','breakTimeFixes')->find($id);
        $attendanceFix->attendance->update([
            'approval' => true,
            'clock_in' => $attendanceFix->clock_in,
            'clock_out' => $attendanceFix->clock_out,
            'note' => $attendanceFix->note,
        ]);
        $attendanceFix->update([
            'approval' => true,
        ]);
        foreach ($attendanceFix->breakTimeFixes as $breakTimeFix) {
            $breakTimeFix->update([
                'approval' => true,
            ]);
            $breakTimeFix->breakTime->update([
                'start' => $breakTimeFix->start,
                'end' => $breakTimeFix->end,
            ]);
        }

        return redirect(route('admin.approve',['id' => $attendanceFix->id]));
    }
}
