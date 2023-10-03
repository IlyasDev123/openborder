<?php

namespace App\Http\Services;

use App\Models\Plan;
use App\Models\User;
use App\Jobs\FlatFeeData;
use App\Models\PlanPrice;
use App\Models\Petitioner;
use Illuminate\Support\Carbon;
use App\Http\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\UserSubscriptionsDetail;
use Illuminate\Support\Facades\Validator;

class GustPaymentService
{

    protected $stripe;
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $this->userService  = $userService;
    }


    /**
     * getPaymentIntent
     *
     * @param  mixed $request
     * @return void
     */
    public function serviceSubscription($request)
    {

        $validator = Validator::make($request->all(), [
            'plan_id' => 'required |exists:plans,id',
            'stripe_payment_method' => 'required | string',
            'email' => 'sometimes|string|email',
            'phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10 |max:18',
            'first_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }

        $user  =  $this->userService->createUser($request);

        $plan = Plan::find($request->get('plan_id'));
        $stripe_price_id = $plan->stripe_price_id;
        $quantity = isset($request->quantity) ? $request->quantity : 1;
        $amount = $plan->amount;
        $durations = $plan->duration;
        if (isset($request->price_id) && $request->price_id != null) {
            $price = PlanPrice::find($request->price_id);
            $stripe_price_id = $price->stripe_price_id;
            $amount = $price->price;
        }

        if (!$plan) {
            return sendError('Please select a Package Plan', null);
        }

        $user_subscription = UserSubscriptionsDetail::where([
            ['user_id', $user->id],
            ['plan_id', $plan->id],
        ])->orderBy('id', 'DESC')->first();

        if ($plan->plan_type != 'single' && $plan->recurring_period != 'day') {
            if (!empty($user_subscription) && (isset($user_subscription->stripe_ended_at) && $user_subscription->stripe_ended_at > Carbon::now())) {
                return sendError('You have already purchased this plan', null);
            }
        }
        $payment_method_token = $this->attachUserToPaymentMethod($request, $user);

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
                'default_payment_method' => $payment_method_token,
                'expand' => ["latest_invoice.payment_intent"],
                'description' => $plan->plan_name,
                "metadata" => [
                    "source" => "Open Borders",
                ],
                "cancel_at" =>  $newDateTime
            ]);
            $charageId['charge_id'] = $response->latest_invoice['payment_intent']->charges->data['id'] ?? null;
            $response['client_secret'] = $response->latest_invoice['payment_intent']->client_secret ?? null;



            $payload = $this->paymentSuccess($response, $request, $amount, $durations, $user->id);

            $now = now();
            createDebugLogFile("as guest user flat fee payment-{$now}:", 'flatfee-payment-details', [
                "user id" => $user->id,
                "flate fee name " => $plan->plan_name,
                "payment platform " => "openborder cp",
                "stripe_subscription_id" => $response->id ?? null
            ]);

            DB::commit();
            if ($request->is_login != 1) {
                $response = [
                    'plan_detail' => $plan,
                    'client_secret' => $response['client_secret'],
                ];

                return sendSuccess('Your payment succeeded.Thank you for retaining us.', $response);
            }

            $tokenResult = $user->createToken('Personal Access Token');
            $data['access_token'] = $tokenResult->accessToken;
            $data['token_type'] = 'Bearer';
            $data['user'] = User::where('id', $user->id)->with('petitionerDetail')->first();
            return sendSuccess('Your payment succeeded.Thank you for retaining us.', $data);
            // dispatch(new FlatFeeData($payload->user_id, $payload->id));
        } catch (\Stripe\Exception\CardException $e) {
            DB::rollback();
            $this->errorLogs($e->getMessage());
            return sendApiError($e->getMessage(), null);
        } catch (\Stripe\Exception\RateLimitException $e) {
            DB::rollback();
            $this->errorLogs($e->getMessage());
            return sendApiError($e->getMessage(), null);
            // Too many requests made to the API too quickly
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            DB::rollback();
            $this->errorLogs($e->getMessage());
            return sendApiError($e->getMessage(), null);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            DB::rollback();
            $this->errorLogs($e->getMessage());
            return sendApiError($e->getMessage(), null);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            DB::rollback();
            $this->errorLogs($e->getMessage());
            return sendApiError($e->getMessage(), null);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            DB::rollback();
            $this->errorLogs($e->getMessage());
            return sendApiError($e->getMessage(), null);
        } catch (\Exception $e) {
            DB::rollback();
            $this->errorLogs($e->getMessage());
            return sendApiError('paymentn failed', $e->getMessage());
        }
    }


    /**
     * paymentSuccess
     *
     * @param  mixed $response
     * @param  mixed $request
     * @return void
     */
    public function paymentSuccess($response, $request, $amount, $durations, $user_id)
    {
        $stripe_fees = ($amount * 0.029) + 0.30;
        $paid_amount = $amount - $stripe_fees;
        $quantity = $request->quantity ? $request->quantity : 1;
        $data['user_id'] = $user_id;
        $data['plan_id'] = $request->plan_id;
        $data['quantity'] = $quantity;
        $data['total_amount'] = $quantity * $amount;
        $data['stripe_subscription_id'] = $response->id ?? "";
        $data['stripe_customer'] = $response->customer ?? "";
        $data["stripe_status"] = $response->status ?? "";
        $data['stripe_start_at'] = date('Y-m-d', $response->start_date);
        $data['stripe_ended_at'] = date('Y-m-d', $response->cancel_at);
        $date['stripe_invoice_no'] = $response->latest_invoice->id ?? "";
        $date['stripe_invoice_url'] = $response->latest_invoice->hosted_invoice_url ?? "";
        $data['paid_amount'] = $quantity * $paid_amount;
        $data['remaining_recurring_payment'] = $durations - 1;

        return  UserSubscriptionsDetail::create($data);
    }

    /**
     * attachUserToPaymentMethod
     *
     * @param  mixed $request
     * @return void
     */
    public function attachUserToPaymentMethod($request, $user)
    {
        $data = $user;

        if (isset($request->is_web) && $request->is_web == 1) {
            $card = \Stripe\Customer::createSource(
                $data->stripe_id,
                ['source' => $request->stripe_payment_method]
            );

            return $card->id;
        }
        $data['pm_last_four'] = isset($request->pm_last_four) ? $request->pm_last_four : "";
        if ($data->save()) {

            return $this->stripe->paymentMethods->attach(
                $request->stripe_payment_method,
                ['customer' => $data->stripe_id]
            );
        }
    }


    /**
     * @param mixed $error
     *
     * @return [type]
     */
    public function errorLogs($error)
    {
        createDebugLogFile("flat fee payment-error:", 'flatfee-payment-error', [
            "payment platform " => "openborder cp",
            "error" => $error
        ]);
    }
}
