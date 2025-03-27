<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StampCrrectionRequestController extends Controller
{
    //
    public function userIndex(){
        $user_id = Auth::id();
        $attendances = Attendance::where([
            ['user_id', '=', $user_id],
            ['approval', '=', false]
            ])->with(['attendanceFix' => function ($query){
                $query->notApproved();
            }])->get();
        return view('pages.stamp_correction_request_list', compact('attendances'));
    }
}
