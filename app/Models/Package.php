<?php

namespace App\Models;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Model;


class Package extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'status', 'image','description','sort_position','package_name_es','package_description_es'
    ];

    /**
     * Get the pricing
     */
    public function packagePlan() {
        return $this->hasMany(Plan::class,'id','package_id');
    }


}
