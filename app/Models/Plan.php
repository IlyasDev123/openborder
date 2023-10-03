<?php

namespace App\Models;

use App\Models\Package;
use App\Models\PlanPrice;
use App\Models\UserSubscriptionsDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_name',
        'slug',
        'package_id',
        'stripe_price_id',
        'amount',
        'duration',
        'recurring_period',
        'plan_type',
        'image',
        'description',
        'stripe_product_id',
        'status',
        'is_quantity_enable',
        'sort_position',
        'plan_name_es',
        'description_es',
        'plan_type_es',
        'recurring_period_es'
    ];

      /**
     * Get the packages
     */
    public function package() {
        return $this->hasOne(Package::class, 'id', 'package_id');
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
    public function userBySubscriptions(){
        return $this->hasMany(UserSubscriptionsDetail::class);
    }

    public function planPrices()
    {
        return $this->hasMany(PlanPrice::class);
    }
}
