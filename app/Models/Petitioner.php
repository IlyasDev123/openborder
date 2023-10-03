<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petitioner extends Model
{
    use HasFactory;
    protected $fillable = ['email', 'first_name', 'last_name', 'is_us_citizen','user_id'];

}
