<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitors extends Model
{
    use HasFactory;
    protected $primaryKey = 'visitor_id';

    protected $fillable = [
        'visitorName',
        'visitorEmail',
        'tel',
    ];

    public function institute()
    {
        return $this->belongsTo(Institutes::class, 'institute_id');
    }
    // public function bookings()
    // {
    //     return $this->hasMany(Bookings::class, 'booking_id', 'booking_id');
    // }
    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'visitor_id');
    }
    
}
