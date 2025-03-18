<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;

    protected $table = 'verifications';
    protected $primaryKey = 'verification_id';

    protected $fillable = ['email', 'token', 'verified', 'expires_at'];
    public $timestamps = true;
}
