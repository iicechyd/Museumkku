<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bookings extends Model
{
    protected $primaryKey = 'booking_id';

    use HasFactory;
    protected $fillable = [
        'booking_date',
        'activity_id',
        'timeslots_id',
        'children_qty',
        'students_qty',
        'adults_qty',
        'disabled_qty',
        'elderly_qty',
        'monk_qty',
        'status'
    ];
    protected $appends = ['end_date'];
    public function getEndDateAttribute()
    {
        $activity = Activity::find($this->activity_id);

        // คำนวณ end_date ตาม duration ของกิจกรรม
        if ($activity && $activity->duration_days) {
            return Carbon::parse($this->booking_date)->addDays($activity->duration_days - 1)->toDateString();
        }

        // ถ้าไม่มี duration ให้ใช้ booking_date
        return $this->booking_date;
    }
    public function visitor()
    {
        return $this->belongsTo(Visitors::class, 'visitor_id');
    }

    public function institute()
    {
        return $this->belongsTo(Institutes::class, 'institute_id', 'institute_id');
    }
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function timeslot()
    {
        return $this->belongsTo(Timeslots::class, 'timeslots_id');
    }
    public function statusChanges()
    {
        return $this->hasMany(StatusChanges::class, 'booking_id', 'booking_id');
    }
    // Method สำหรับดึง statusChange ล่าสุด
    public function latestStatusChange()
    {
        return $this->hasOne(StatusChanges::class, 'booking_id', 'booking_id')
            ->orderBy('changed_id', 'desc'); // ใช้ changed_id แทน id
    }
    
    public function getTotalVisitorsAttribute()
    {
        return $this->children_qty + $this->students_qty + $this->adults_qty;
    }
    
}
