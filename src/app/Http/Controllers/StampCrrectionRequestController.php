<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StampCrrectionRequestController extends Controller
{
    //
    public function userIndex(Request $request){
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
}
