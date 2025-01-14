<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityImages extends Model
{
    use HasFactory;
    protected $primaryKey = 'image_id';

    protected $fillable = [
        'image_path'
    ];
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
    
}
