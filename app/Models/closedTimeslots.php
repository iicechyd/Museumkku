<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class closedTimeslots extends Model
{
    use HasFactory;
    protected $primaryKey = 'closed_timeslots_id';
    protected $fillable = [
        'activity_id',
        'timeslots_id',
        'closed_on'
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

