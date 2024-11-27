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
        'image',
        'max_capacity',
        'status'
    ];

    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id', 'activity_type_id');
    }
    public function timeslots()
    {
        return $this->hasMany(Timeslots::class, 'activity_id', 'activity_id');
    }
}
