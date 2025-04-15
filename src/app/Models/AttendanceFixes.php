<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceFixes extends Model
{
    use HasFactory;
    protected $fillable = ['attendance_id', 'clock_in', 'clock_out', 'note','approval'];

    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }

    public function scopeNotApproved($query)
    {
        return $query->where('approval', false);
    }

    public function scopeApproved($query)
    {
        return $query->where('approval', true);

    }

    public function getTimeFormatted($time){
        return $this->$time ? Carbon::parse($this->$time)->format('H:i') : null;
    }

    public function getDateFormatted($date){
        return $this->$date ? Carbon::parse($this->$date)->format('Y/m/d') : null;
    }
}
