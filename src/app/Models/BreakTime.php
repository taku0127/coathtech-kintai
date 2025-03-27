<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    use HasFactory;

    protected $fillable = ['attendance_id', 'start', 'end'];

    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }

    public function breakTimeFix(){
        return $this->hasMany(BreakTimeFixes::class);
    }

    public function getTimeFormatted($time){
        return $this->$time ? Carbon::parse($this->$time)->format('H:i') : null;
    }

    public function breakTimeLength(){
        $start = Carbon::parse($this->start);
        $end = Carbon::parse($this->end);
        $actualBrakTimeMinutes = $start && $end? $start->diffInMinutes($end) : 0;
        return $actualBrakTimeMinutes;
    }
}
