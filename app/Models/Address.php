<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'street_address',
        'country',
        'state',
        'city',
        'zip_code',
        'user_id',
        'country_name_code'
    ];

    /**
     * user_address
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCountryAttribute($value)
    {
        return ucfirst($value);
    }

    public function getStateAttribute($value)
    {
        return ucfirst($value);
    }

    public function getCityAttribute($value)
    {
        return ucfirst($value);
    }
   }
