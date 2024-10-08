<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    protected $primaryKey = 'booking_id';

    use HasFactory;
    protected $fillable = [
        'booking_date',
        'activity_id',
        'timeslots_id',
        'instituteName',
        'instituteAddress',
        'province',
        'district',
        'subdistrict',
        'zip',
        'visitorName',
        'visitorEmail',
        'tel',
        'children_qty',
        'students_qty',
        'adults_qty',
        'status'
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function timeslot()
    {
        return $this->belongsTo(Timeslots::class, 'timeslots_id');
    }
}
