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
        if ($activity && $activity->duration_days) {
            return Carbon::parse($this->booking_date)->addDays($activity->duration_days - 1)->toDateString();
        }
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

    public function subActivities()
    {
        return $this->belongsToMany(SubActivity::class, 'booking_subactivities', 'booking_id', 'sub_activity_id');
    }

    public function timeslot()
    {
        return $this->belongsTo(Timeslots::class, 'timeslots_id');
    }
    public function statusChanges()
    {
        return $this->hasMany(StatusChanges::class, 'booking_id', 'booking_id');
    }
    public function latestStatusChange()
    {
        return $this->hasOne(StatusChanges::class, 'booking_id', 'booking_id')
            ->orderBy('changed_id', 'desc');
    }

    public function getTotalVisitorsAttribute()
    {
        return $this->children_qty + $this->students_qty + $this->adults_qty;
    }

    public function documents()
    {
        return $this->hasMany(Documents::class, 'booking_id', 'booking_id');
    }
    public function getTotalPriceAttribute()
    {
        $activity = $this->activity;

        if (!$activity) {
            return 0;
        }

        $total = 0;
        $total += ($this->children_qty ?? 0) * ($activity->children_price ?? 0);
        $total += ($this->students_qty ?? 0) * ($activity->student_price ?? 0);
        $total += ($this->adults_qty ?? 0) * ($activity->adult_price ?? 0);
        $total += ($this->disabled_qty ?? 0) * ($activity->disabled_price ?? 0);
        $total += ($this->elderly_qty ?? 0) * ($activity->elderly_price ?? 0);
        $total += ($this->monk_qty ?? 0) * ($activity->monk_price ?? 0);

        return $total;
    }
}
