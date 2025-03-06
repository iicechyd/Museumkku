<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tmss extends Model
{
    protected $table = 'tmss';
    protected $primaryKey = 'tmss_id';

    use HasFactory;
    protected $fillable = ['activity_id', 'start_time', 'end_time', 'status'];


    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }
    public function closedTmss()
    {
        return $this->hasMany(ClosedTmss::class, 'tmss_id');
    }
}
