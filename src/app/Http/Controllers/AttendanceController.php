<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    //
    public function index(){
        $attendance = Attendance::where('user_id', 1)
        ->where('date', now()->format('Y-m-d'))
        ->first();
        return view('pages.staff.attendance',compact('attendance'));
    }

    public function storeClockIn(Request $request){
        Attendance::create([
            'user_id' => Auth::id(),
            'date' => now(),
            'clock_in' => $request->clock_in,
        ]);
        return redirect(route('user.attendance'));
    }
}
