<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $primaryKey = 'activity_id';

    protected $fillable = [
        'activity_name',
        'description',
        'children_price',
        'student_price',
        'adult_price',
        'kid_price',
        'disabled_price',
        'elderly_price',
        'monk_price',
        'max_capacity',
        'status',
        'duration_days',
        'max_subactivities',
        'target_yearly_count'
    ];

    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id', 'activity_type_id');
    }
    public function tmss()
    {
        return $this->hasMany(Tmss::class, 'activity_id', 'activity_id');
    }
    public function closedTmss()
    {
        return $this->hasMany(ClosedTmss::class, 'activity_id');
    }
    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'activity_id');
    }
    public function subactivities()
    {
        return $this->hasMany(Subactivity::class, 'activity_id');
    }
    public function images()
    {
        return $this->hasMany(ActivityImages::class, 'activity_id', 'activity_id');
    }
}
