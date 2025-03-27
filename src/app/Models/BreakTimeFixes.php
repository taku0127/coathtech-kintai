<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTimeFixes extends Model
{
    use HasFactory;
    protected $fillable = ['break_time_id', 'start', 'end','approval'];

    public function breakTime(){
        return $this->belongsTo(BreakTime::class);
    }

    public function scopeNotApproved($query)
    {
        return $query->where('approval', 'false');
    }

    public function getTimeFormatted($time){
        return $this->$time ? Carbon::parse($this->$time)->format('H:i') : null;
    }
}
