<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class closedTmss extends Model
{
    use HasFactory;
    protected $table = 'closed_tmss';
    protected $primaryKey = 'closed_tmss_id';
    protected $fillable = [
        'activity_id',
        'tmss_id',
        'closed_on',
        'comments'
    ];
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function tmss()
    {
        return $this->belongsTo(Tmss::class, 'tmss_id');
    }
}

