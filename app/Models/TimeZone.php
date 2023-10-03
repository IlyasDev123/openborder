<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeZone extends Model
{
    use HasFactory;

    protected $fillable = [
        "time_zone",
        "time_zone_name",
        "code"
    ];
}
