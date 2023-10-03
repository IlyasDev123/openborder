<?php

namespace App\Http\Controllers\WordpressApis;

use Carbon\Carbon;
use Stripe\Charge;
use App\Models\Plan;
use App\Models\User;
use App\Models\PlanPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\UserSubscriptionsDetail;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function addToCart(Request $request)
    {
        $data =   $this->stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 8,
                'exp_year' => 2029,
                'cvc' => '314',
            ],
        ]);

        $method = $this->stripe->paymentMethods->create([
            'type' => 'card',
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 2,
                'exp_year' => 2029,
                'cvc' => '314',
            ],
        ]);

        return sendSuccess('success', [$data,  $method]);
    }



    public function paymentSubscription(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "email" => 'required',
            'is_onetime_payment' => 'required|boolean',
            'price_id' => 'exclude_if:is_onetime_payment,true|required',
            'plan_id' => "exclude_if:is_onetime_payment,true|required|exists:plans,id",
            'stripe_token' => "required| string"
        ]);

        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }
        $user = User::where('email', $request->email)->first();

        $card = \Stripe\Customer::createSource(
            $user->stripe_id,
            ['source' => $request->stripe_token]
        );

        if ($request->is_onetime_payment == true) {
            try {
                $data = $this->oneTimePayment($request, $user, $card->id);
                DB::commit();
                return sendSuccess('success', $data);
            } catch (\Throwable $th) {
                DB::rollback();
                return sendError("payment failed", $th->getMessage());
            }
        }
        $plan = Plan::find($request->get('plan_id'));

        if (!$plan) {
            return sendError('Please select a Package Plan', null);
        }

        $quantity = isset($request->quantity) ? $request->quantity : 1;
        if (isset($request->price_id) && $request->price_id != null) {
            $price = PlanPrice::where('stripe_price_id', $request->price_id)->first();
            $amount = $price->price;
        }
        $stripe_price_id = $request->price_id;
        $user_subscription = UserSubscriptionsDetail::where([
            ['user_id', $user->id],
            ['plan_id', $plan->id],
        ])->orderBy('id', 'DESC')->first();

        if (!empty($user_subscription) && (isset($user_subscription->stripe_ended_at) && $user_subscription->stripe_ended_at > Carbon::now())) {
            return sendError('You have already purchased this plan', null);
        }

        if ($request->stripe_payment_method && $user->stripe_payment_method == null) {
            $this->attachUserToPaymentMethod($user, $request);
        }
        switch ($plan->recurring_period) {
            case ('day'):
                $newDateTime = Carbon::now()->addDays($plan->duration);
                break;
            case ('month'):
                $newDateTime = Carbon::now()->addMonths($plan->duration);
                break;
            case ('year'):
                $newDateTime = Carbon::now()->addYears($plan->duration);
            case ('every_month'):
                $newDateTime = Carbon::now()->addMonths($plan->duration);
        }
        $newDateTime = strtotime($newDateTime);
        DB::beginTransaction();
        try {
            $response = $this->stripe->subscriptions->create([
                'customer' => $user->stripe_id,
                'items' => [
                    [
                        'price' => $stripe_price_id,
                        'quantity' => $quantity,
                    ],
                ],
                'default_payment_method' => $card->id,
                'expand' => ["latest_invoice.payment_intent"],
                "cancel_at" =>  $newDateTime
            ]);

            $response['client_secret'] = $response->latest_invoice['payment_intent']->client_secret;
            $data = [
                'client_secret' => $response['client_secret'],
                'plan_detail' => $plan
            ];
            $data["payment_detail"] = $this->paymentSuccess($response, $request, $amount, $user->id);
            DB::commit();
            return sendSuccess('success', $data);
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollback();
            return sendError($e->getMessage(), null);
        } catch (\Stripe\Exception\RateLimitException $e) {
            DB::rollback();
            return sendError($e->getMessage(), null);
            // Too many requests made to the API too quickly
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            DB::rollback();
            return sendError($e->getMessage(), null);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            DB::rollback();
            return sendError($e->getMessage(), null);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            DB::rollback();
            return sendError($e->getMessage(), null);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            DB::rollback();
            return sendError($e->getMessage(), null);
        } catch (\Exception $e) {
            DB::rollback();
            return sendError('payment failed', $e->getMessage());
        }
    }

    public function attachUserToPaymentMethod($user, $request)
    {

        $user['stripe_token'] = isset($request->stripe_token) ? $request->stripe_token : "";
        $user['stripe_payment_method'] = isset($request->stripe_payment_method) ? $request->stripe_payment_method : "";
        $user['pm_last_four'] = isset($request->pm_last_four) ? $request->pm_last_four : "";
        if ($user->save()) {

            return $this->stripe->paymentMethods->attach(
                $user->stripe_payment_method,
                ['customer' => $user->stripe_id]
            );
        }
    }

    public function oneTimePayment($request, $user, $card)
    {
        $data = $request->data;
        foreach ($data as $d) {
            $plan = Plan::findOrFail($d['plan_id']);
            $price = PlanPrice::where('stripe_price_id', $d['price_id'])->first();
            $amounts = $price->price * $d['quantity'];
            $response = \Stripe\Charge::create(array(
                "amount"   => $amounts * 100,
                "currency" => 'usd',
                'source' => $card,
                "customer" => $user->stripe_id,
                "description" => strip_tags($plan->description)
            ));
            UserSubscriptionsDetail::create([
                "user_id" => $user->id,
                "plan_id" => $d['plan_id'],
                "quantity" => $d['quantity'],
                "total_amount" => $amounts,
                'stripe_subscription_id' => $response->id,
                'stripe_customer' => $response->customer ?? "",
                'stripe_start_at' => date('Y-m-d', $response->created),
                'stripe_ended_at' => date('Y-m-d', $response->created),
                'subscription_type' => 3,
                'stripe_invoice_url' => $response->receipt_url ?? ""
            ]);
        }
        return $response;
    }

    public function paymentSuccess($response, $request, $amount, $user_id)
    {
        $quantity = $request->quantity ? $request->quantity : 1;
        $data['user_id'] = $user_id;
        $data['plan_id'] = $request->plan_id;
        $data['quantity'] = $quantity;
        $data['total_amount'] = $quantity * $amount;
        $data['stripe_subscription_id'] = $response->id ?? "";
        $data['stripe_customer'] = $response->customer ?? "";
        $data['stripe_start_at'] = date('Y-m-d', $response->start_date);
        $data['stripe_ended_at'] = date('Y-m-d', $response->cancel_at);
        $date['stripe_invoice_no'] = $response->latest_invoice->id ?? "";
        $date['stripe_invoice_url'] = $response->latest_invoice->hosted_invoice_url ?? "";
        $data["stripe_status"] = $response->status ?? "";
        $data["stripe_response"] = $response;
        return  UserSubscriptionsDetail::create($data);
    }
}
