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
        'user_id',
        'old_status',
        'new_status',
        'comments',
    ];

    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'booking_id', 'booking_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

}
