<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActualVisitors extends Model
{
    use HasFactory;
    protected $table = 'actual_visitors';

    protected $primaryKey = 'actual_visitors_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'booking_id',
        'actual_children_qty',
        'actual_students_qty',
        'actual_adults_qty',
        'actual_kid_qty',
        'actual_disabled_qty',
        'actual_elderly_qty',
        'actual_monk_qty',
        'actual_free_teachers_qty',
    ];
    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'booking_id', 'booking_id');
    }
}
