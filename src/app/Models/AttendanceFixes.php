<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceFixes extends Model
{
    use HasFactory;
    protected $fillable = ['attendance_id', 'clock_in', 'clock_out', 'note'];

    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }

    public function getTimeFormatted($time){
        return $this->$time ? Carbon::parse($this->$time)->format('H:i') : null;
    }
}
