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
        'tmss_id',
        'user_id',
        'children_qty',
        'students_qty',
        'adults_qty',
        'kid_qty',
        'disabled_qty',
        'elderly_qty',
        'monk_qty',
        'note',
        'status',
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
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }

    public function subActivities()
    {
        return $this->belongsToMany(SubActivity::class, 'booking_subactivities', 'booking_id', 'sub_activity_id');
    }

    public function tmss()
    {
        return $this->belongsTo(Tmss::class, 'tmss_id');
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

    public function actualVisitors()
    {
        return $this->hasOne(ActualVisitors::class, 'booking_id', 'booking_id');
    }

    public function getTotalVisitorsAttribute()
    {
        return $this->children_qty + $this->students_qty + $this->adults_qty;
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
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
        $total += ($this->kid_qty ?? 0) * ($activity->kid_price ?? 0);
        $total += ($this->disabled_qty ?? 0) * ($activity->disabled_price ?? 0);
        $total += ($this->elderly_qty ?? 0) * ($activity->elderly_price ?? 0);
        $total += ($this->monk_qty ?? 0) * ($activity->monk_price ?? 0);

        return $total;
    }
}
