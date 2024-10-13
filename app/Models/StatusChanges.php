<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusChanges extends Model
{
    use HasFactory;
    protected $table = 'status_changes';

    protected $primaryKey = 'changed_id';
    public $incrementing = true; // ตั้งค่านี้เป็น true
    protected $keyType = 'int'; // ชนิดข้อมูลของ primary key
    protected $fillable = [
        'booking_id',
        'old_status',
        'new_status',
        'comments',
        'changed_by',
    ];

    public function booking()
    {
        return $this->belongsTo(Bookings::class, 'booking_id', 'booking_id');
    }

}
