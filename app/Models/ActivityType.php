<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityType extends Model
{
    use HasFactory;
    protected $primaryKey = 'activity_type_id';

    protected $fillable = [
        'type_name'
    ];

    public function activities()
    {
        return $this->hasMany(Activity::class, 'activity_type_id', 'activity_type_id');
    }
}
