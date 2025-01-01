<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bookingHistory extends Model
{
    use HasFactory;
    protected $primaryKey = 'history_id';

    protected $fillable = [
        'booking_id',
        'changed_id',
    ];
    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'booking_id', 'booking_id');
    }

    public function statusChange()
    {
        return $this->belongsTo(StatusChanges::class, 'changed_id', 'changed_id');
    }
}
