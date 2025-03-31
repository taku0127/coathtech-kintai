<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Services\CsvExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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

    public function exportCsv(Request $request){
        $page = $request->query('page',0);
        $targetDate = Carbon::now()->startOfMonth()->addMonths($page);
        $year = $targetDate->year;
        $month = $targetDate->month;
        $user = User::find($request->query('id'));
        $attendances = Attendance::where('user_id', $user->id)->whereYear('date',$year)->whereMonth('date',$month)->orderBy('date','desc')->get();
        $header = ['日付','出勤','退勤','休憩','合計'];
        $rows = [];
        foreach ($attendances as $attendance) {
            $row = [
                Carbon::parse($attendance->date)->format('m/d').'('. ['日', '月', '火', '水', '木', '金', '土'][Carbon::parse($attendance->date)->dayOfWeek].')',
                $attendance->getTimeFormatted('clock_in'),
                $attendance->getTimeFormatted('clock_out'),
                $attendance->getBreakTimesShow(),
                $attendance->getActualWorkTime()
            ];
            $rows[] = $row;
        }
        $csv = CsvExportService::generateCsv($header,$rows);
        $filename = $user->name.'さんの'.$year.'年'.$month.'月'.'勤怠一覧.csv';
        $headers = array('Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename=' .$filename,);
        return Response::make($csv, 200, $headers);
    }
}
