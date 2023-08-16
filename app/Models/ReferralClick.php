<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'visitor_ip',
    ];
}
