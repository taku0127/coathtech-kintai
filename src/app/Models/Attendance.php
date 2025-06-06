<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'date', 'clock_in', 'clock_out', 'note', 'approval'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function breakTimes(){
        return $this->hasMany(BreakTime::class);
    }

    public function attendanceFix(){
        return $this->hasMany(AttendanceFixes::class);
    }

    public function getTimeFormatted($time){
        return $this->$time ? Carbon::parse($this->$time)->format('H:i') : null;
    }

    public function getDateFormatted($date){
        return $this->$date ? Carbon::parse($this->$date)->format('Y/m/d') : null;
    }

    public function getActualWorkTime(){
        $actualWorkMinutes = $this->workHoursLength() - $this->breakTimesLength();
        return $actualWorkMinutes > 0 ? sprintf('%d:%02d', floor($actualWorkMinutes / 60), $actualWorkMinutes % 60) : 0;
    }

    private function workHoursLength(){
        $clockIn = Carbon::parse($this->clock_in);
        $clockOut = Carbon::parse($this->clock_out);
        $actualWorkMinutes = ($clockIn && $clockOut)? $clockIn->diffInMinutes($clockOut) : 0;
        return $actualWorkMinutes;
    }

    private function breakTimesLength(){
        $breakTimes = $this->breakTimes;
        $breakMinutes = $breakTimes->filter(function($break){
            return!is_null($break->end);
        })->sum(function($break) {
            return $break->breakTimeLength();
        });
        return $breakMinutes;
    }

    public function getBreakTimesShow(){
        $breakTimeLength = $this->breakTimesLength();
        return $breakTimeLength > 0 ? sprintf('%d:%02d', floor($breakTimeLength / 60), $breakTimeLength % 60) : null;
    }
}
