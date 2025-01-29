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
        'actual_children_qty',
        'actual_students_qty',
        'actual_adults_qty',
        'actual_disabled_qty',
        'actual_elderly_qty',
        'actual_monk_qty',
        'changed_by',
    ];

    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'booking_id', 'booking_id');
    }
}
