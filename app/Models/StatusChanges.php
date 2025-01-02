<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusChanges extends Model
{
    use HasFactory;
    protected $table = 'status_changes';

    protected $primaryKey = 'changed_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'booking_id',
        'old_status',
        'new_status',
        'comments',
        'number_of_visitors',
        'changed_by',
    ];

    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'booking_id', 'booking_id');
    }
    public function bookingHistories()
    {
        return $this->hasMany(BookingHistory::class, 'changed_id', 'changed_id');
    }
}
