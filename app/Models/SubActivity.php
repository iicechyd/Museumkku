<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubActivity extends Model
{
    use HasFactory;
    protected $table = 'sub_activities';
    protected $primaryKey = 'sub_activity_id';

    protected $fillable = [
        'activity_id',
        'sub_activity_name',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'activity_id');
    }
}
