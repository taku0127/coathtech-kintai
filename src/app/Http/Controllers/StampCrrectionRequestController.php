<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
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
            $attendances = Attendance::where([
                ['user_id', '=', $userId],
                ['approval', '=', false]
                ])->with(['attendanceFix' => function ($query){
                    $query->notApproved();
                }])->get();
        }else {
            $attendances = Attendance::where([
                ['user_id', '=', $userId],
                ['approval', '=', true]
                ])->whereHas('attendanceFix', function($query){
                    $query->approved();
                })->get();
        }
        return view('pages.stamp_correction_request_list', compact('attendances','pageParam'));
    }
    private function adminIndex(Request $request){
        $pageParam = $request->query('page');
        if($pageParam != 'approval'){
            $attendances = Attendance::where([
                ['approval', '=', false]
                ])->with(['attendanceFix' => function ($query){
                    $query->notApproved();
                }])->get();
        }else {
            $attendances = Attendance::where([
                ['approval', '=', true]
                ])->whereHas('attendanceFix', function($query){
                    $query->approved();
                })->get();
        }
        return view('pages.stamp_correction_request_list', compact('attendances','pageParam'));
    }

    public function adminApprove($id){
        $attendance = Attendance::with(['attendanceFix' => function ($query){
            $query->notApproved()->first();
        }])->with(['breakTimes.breakTimeFix' => function($query){
            $query->notApproved();
        }])->find($id);
        return view('pages.attendance_detail',compact('attendance'));
    }

    public function adminApproveStore($id){
        $attendance = Attendance::with(['attendanceFix' => function ($query){
            $query->notApproved()->first();
        }])->with(['breakTimes.breakTimeFix' => function($query){
            $query->notApproved();
        }])->find($id);
        $attendanceFix = $attendance->attendanceFix->first();
        $attendance->update([
            'approval' => true,
            'clock_in' => $attendanceFix->clock_in,
            'clock_out' => $attendanceFix->clock_out,
            'note' => $attendanceFix->note,
        ]);
        $attendanceFix->update([
            'approval' => true,
        ]);
        foreach ($attendance->breakTimes as $breakTime) {
            $breakTimeFix = $breakTime->breakTimeFix->first();
            $breakTimeFix->update([
                'approval' => true,
            ]);
            $breakTime->update([
                'start' => $breakTimeFix->start,
                'end' => $breakTimeFix->end,
            ]);
        }

        return redirect(route('admin.approve',['id' => $attendance->id]));
    }
}
