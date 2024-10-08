<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeslots extends Model
{
    protected $primaryKey = 'timeslots_id';

    use HasFactory;
    protected $fillable = ['activity_id', 'start_time', 'end_time', 'max_capacity'];


    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }
}
