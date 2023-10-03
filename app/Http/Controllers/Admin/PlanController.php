<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use App\Models\Package;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{

    protected $stripe;

    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        $packages = Package::where('status', 1)->orderBy('sort_position', 'ASC')->get();
        if (!$packages) {
            return sendApiSuccess("Success", null);
        }
        return sendApiSuccess("Success", $packages, null);
    }

    public function packagePlan(Request $request)
    {
        $plans = Plan::where('status', 1)->when($request->package_id > 0, function ($q) use ($request) {
            $q->where("package_id", $request->package_id);
        })->with('planPrices')->orderBy('sort_position', 'ASC')->get();
        if (!$plans) {
            return sendApiSuccess("No record Found", null);
        }
        return sendApiSuccess("Success", $plans, null);
    }



    public function getPackagePlanById(Request $request)
    {
        $plan = Plan::where('status', 1)->where('id', $request->plan_id)->with('planPrices')->first();
        if (!$plan) {
            return sendApiSuccess("No record Found", null);
        }
        return sendApiSuccess("Success", $plan, null);
    }

    public function getPlanListByPackageId($id)
    {
        $plan = plan::where('package_id', $id)->where('status', 1)->with('planPrices')->orderBy('sort_position', 'ASC')->get();
        if (!$plan) {
            return sendApiSuccess("No record Found", null);
        }
        return sendApiSuccess("Success", $plan, null);
    }

    public function getPackagePlanByIdForWeb($id)
    {
        $plan = Plan::with('planPrices')->where('slug', $id)->get();
        if (!$plan) {
            return sendApiSuccess("No record Found", null);
        }
        return sendApiSuccess("Success", $plan, null);
    }
}
