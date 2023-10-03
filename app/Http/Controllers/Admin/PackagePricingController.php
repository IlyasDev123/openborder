<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use App\Models\Package;
use App\Models\PlanPrice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PackagePricingController extends Controller
{

    protected $stripe;

    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packages = Plan::with('package')->orderBy('sort_position', 'ASC')->get();
        return view('admin.package_price_plan.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $packages = Package::select('id', 'name')->get();
        return view('admin.package_price_plan.create', compact('packages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'plan_name' => 'required',
            'plan_type' => 'required|in:single,recurring',
            'amount' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:max_width=1000,max_height=1000',
            'recurring_period' => 'required|in:year,month,day,every_month',
            'duration' => 'required | integer',
        ]);

        if ($validator->fails()) {
            return \redirect()->back()->with('invalid', $validator->getMessageBag()->getMessages());
        }
        $data = $request->except('_token');

        DB::beginTransaction();
        try {
            $data['slug'] = Str::slug($data['plan_name']);
            $data['plan_name'] = $data['plan_name'];
            $data['plan_name_es'] = $data['plan_name_es'] ?? null;
            $data['description'] = $data['description'];
            $data['description_es'] = $data['description_es'] ?? null;
            $data['sort_position'] = 0;
            $data['is_quantity_enable'] =  $request->is_quantity_enable ?? 0;
            $image = addFile($data['image'], 'package_image/');
            //create stripe product
            $product = $this->stripe->products->create([
                'name' => $data['plan_name'],
            ]);

            $data['stripe_product_id'] = $product->id;
            $amounts = explode(',', $request->amount);
            $data['duration'] = $data['duration'];
            $data['plan_type'] = $data['plan_type'];
            $data['plan_type_es'] = $data['plan_type'] == 'single' ? 'único' : 'periódico';
            $data['recurring_period_es'] = $this->convertIntoSpnishRecurringType($data['recurring_period'], $data['duration']);
            $addPrice = [];
            $periods = $data['recurring_period'] == 'every_month' ? 'month' : $data['recurring_period'];
            foreach ($amounts as $amount) {
                $price = $this->stripe->prices->create([
                    'unit_amount' => $amount * 100,
                    'currency' => env('CASHIER_CURRENCY'),
                    'recurring' => ['interval' => $periods],
                    'product' => $product->id,
                ]);
                $priceData = ['price' => $amount, 'stripe_price_id' => $price->id];
                array_push($addPrice, $priceData);
            }
            $data['stripe_price_id'] = $addPrice[0]['stripe_price_id'];
            $data['amount'] = $amounts[0];


            $data['image'] = $image;
            $data['plan'] = Plan::create($data);
            foreach ($addPrice as $key => $price) {
                $price = PlanPrice::create($price + ['plan_id' => $data['plan']->id]);
            }
            DB::commit();
            return redirect()->route('package.plan.index')
                ->with('success', 'Package Plan create successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return \redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $packages = Package::select('id', 'name')->get();
        $packagePlan = Plan::with('planPrices')->findOrfail($id);
        return view('admin.package_price_plan.edit', compact('packages', 'packagePlan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'plan_name' => 'required',
            'image' => '',
        ]);

        if ($validator->fails()) {
            return \redirect()->back()->with('invalid', $validator->getMessageBag()->getMessages());
        }
        $packagePlan = Plan::findOrfail($id);

        $data = $request->except('_token');
        try {
            if ($request->file('image')) {
                $image = addFile($data['image'], 'package_image/');
            } else {
                $image = $packagePlan->image;
            }
            $data['image'] = $image;
            $data['slug'] = Str::slug($request->slug, '-');
            $data['amount'] = isset($request->amount[0]) ? $request->amount[0] : $packagePlan->amount;
            $data['is_quantity_enable'] =  $request->is_quantity_enable ?? 0;
            $data['plan_name_es'] = $data['plan_name_es'] ?? null;
            $data['plan_description_es'] = $data['plan_description_es'] ?? null;
            $data['plan_type_es'] = $data['plan_type'] == 'single' ? 'único' : 'periódico';
            $data['recurring_period_es'] = $this->convertIntoSpnishRecurringType($data['recurring_period'], $data['duration']);
            $recurringPeriod =  $packagePlan->recurring_period == "every_month" ? "month" : $packagePlan->recurring_period;
            $packagePlan->update($data);
            if (!isset($request->amount[0])) {
                PlanPrice::create([
                    'plan_id' => $packagePlan->id,
                    'price' => $packagePlan->amount,
                    'stripe_price_id' => $packagePlan->stripe_price_id,
                ]);
            } else {

                $data = \json_decode($request->data, true);
                foreach ($data as $key => $d) {
                    $planPrice = PlanPrice::find($request->id[$key]);
                    $priceId = $planPrice->stripe_price_id;

                    if ($d != $planPrice->amount) {
                        $price = $this->stripe->prices->create([
                            'unit_amount' => $request->amount[$key] * 100,
                            'currency' => env('CASHIER_CURRENCY'),
                            'recurring' => ['interval' => $recurringPeriod],
                            'product' => $packagePlan->stripe_product_id,
                        ]);
                        $priceId = $price->id;
                    }
                    $planPrice->update([
                        'price' => $request->amount[$key],
                        'stripe_price_id' => $priceId
                    ]);
                }
            }

            return redirect()->route('package.plan.index')
                ->with('success', 'Package Plan Update successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'failed to update ');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $plan = Plan::find($request->package_id);
        try {
            // if (!empty($plan->stripe_product_id)) {
            //     $this->stripe->products->delete(
            //         $plan->stripe_product_id,
            //         []
            //     );
            // }
            if ($plan->image) {
                File::delete(public_path($plan->image));
            }
            $plan->delete();
            return redirect()->route('package.plan.index')
                ->with('success', 'Deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to  deleted plan.');
        }
    }

    /**
     * activePackage
     *
     * @param  mixed $request
     * @return void
     */
    public function activePackagePlan(Request $request)
    {
        $package = Plan::findOrfail($request->id);
        $package->update(['status' => $request->status]);
        return redirect()->route('package.plan.index')
            ->with('success', 'Package Activate Successfully!');
    }

    /**
     * deactivePackage
     *
     * @param  mixed $request
     * @return void
     */
    public function deactivatePackagePlan(Request $request)
    {
        $package = Plan::findOrfail($request->id);
        $package->update(['status' => $request->status]);
        return redirect()->route('package.plan.index')
            ->with('success', 'Package  Deactivate Successfully!');
    }


    public function sortedPositionSave(Request $request)
    {

        $packages = Plan::get();

        foreach ($packages as $package) {
            foreach ($request->order as $order) {
                if ($order['id'] == $package->id) {
                    $package->update(['sort_position' => $order['position']]);
                }
            }
        }


        return sendSuccess("success", $package);
    }

    public function convertIntoSpnishRecurringType($recurringPeriod, $duration)
    {
        switch ($recurringPeriod) {
            case 'month':

                return $duration > 1 ? 'meses' : 'mes';
                break;
            case 'year':
                return $duration > 1 ? 'años' : 'año';
                break;
            case 'every_month':
                return 'cada mes';
                break;
            default:
                return $duration > 1 ? 'días' : 'día';
                break;
        }
    }

    /**
     * @return [type]
     */
    public function addProductOntoLive()
    {
        $plans = Plan::get();

        foreach ($plans as $plan) {


            $product = $this->stripe->products->create([
                'name' => $plan->plan_name,
            ]);

            $planPrices = PlanPrice::where('plan_id', $plan->id)->get();
            $periods = $plan->recurring_period == 'every_month' ? 'month' : $plan->recurring_period;
            $price_id = null;
            $index = 1;
            foreach ($planPrices as $amount) {
                $price = $this->stripe->prices->create([
                    'unit_amount' => $amount->price * 100,
                    'currency' => env('CASHIER_CURRENCY'),
                    'recurring' => ['interval' => $periods],
                    'product' => $product->id,
                ]);
                $amount->stripe_price_id = $price->id;
                $amount->plan_id = $plan->id;
                $amount->save();
                if ($index == 1) {
                    $price_id = $price->id;
                }
                $index + 1;
                // $priceData = ['price' => $price->amount, 'stripe_price_id' => $price->id];
                // array_push($addPrice, $priceData);
            }
            $plan->stripe_product_id = $product->id;
            $plan->stripe_price_id = $price_id;
            $plan->save();
        }

        return "success";
    }
}
