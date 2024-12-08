<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institutes extends Model
{
    use HasFactory;
    protected $primaryKey = 'institute_id';
    protected $fillable = [
        'instituteName',
        'instituteAddress',
        'province',
        'district',
        'subdistrict',
        'zipcode',
    ];
    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'institute_id', 'institute_id');
    }
}
