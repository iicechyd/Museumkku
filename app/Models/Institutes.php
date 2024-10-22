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
}
