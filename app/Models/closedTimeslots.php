<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class closedTimeslots extends Model
{
    use HasFactory;
    protected $primaryKey = 'closed_timeslots_id';
    protected $fillable = [
        'closed_on',
    ];
}
