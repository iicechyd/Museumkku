<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class booking_subactivity extends Model
{
    use HasFactory;
    protected $table = 'booking_subactivities';
    protected $guarded = [];
    
    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'booking_id', 'booking_id');
    }
}
