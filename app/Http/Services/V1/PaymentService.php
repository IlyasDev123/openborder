<?php

namespace App\Http\Services\V1;

use App\Models\Plan;
use App\Jobs\FlatFeeData;
use App\Models\PlanPrice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\UserSubscriptionsDetail;
use Illuminate\Support\Facades\Validator;

class PaymentService
{

    protected $stripe;

    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function stripeConfigration()
    {
        return  $data =   $this->stripe->tokens->create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 8,
                'exp_year' => 2023,
                'cvc' => '314',
            ],
        ]);

        // }
        // $stripe_token = $this->stripe->paymentMethods->create([
        //     'type' => 'card',
        //     'card' => [
        //         'number' => '4242424242424242',
        //         'exp_month' => 2,
        //         'exp_year' => 2023,
        //         'cvc' => '314',
        //     ],
        // ]);
        // dd($stripe_token->id);
        // return $stripe_token->id;
    }

    // public function singlePayment($request)
    // {
    //     try{
    //         $this->stripeConfigration();
    //         $stripeToken = $request->stripe_token;
    //         $response = \Stripe\Charge::create ( array (
    //             "amount"   => $request->amount*100,
    //             "currency" => 'usd',
    //             'source'=>$request->stripe_token,
    //             "description" =>'consultation fees description'
    //         ));
    //         dd($response);
    //         return sendSuccess('Payment SuccessFully',$response );
    //     } catch(\Stripe\Exception\CardException $e) {
    //       return sendError($e->getMessage(),null);
    //     } catch (\Stripe\Exception\RateLimitException $e) {
    //       return sendError($e->getMessage(),null);
    //       // Too many requests made to the API too quickly
    //     } catch (\Stripe\Exception\InvalidRequestException $e) {
    //       return sendError($e->getMessage(),null);
    //     } catch (\Stripe\Exception\AuthenticationException $e) {
    //       return sendError($e->getMessage(),null);
    //     } catch (\Stripe\Exception\ApiConnectionException $e) {
    //       return sendError($e->getMessage(),null);
    //     } catch (\Stripe\Exception\ApiErrorException $e) {
    //       return sendError($e->getMessage(),null);
    //     }catch(\Exception $e){
    //         return sendError('Payment Failed',null);
    //     }

    // }

    /**
     * subscription
     *
     * @param  mixed $request
     * @return void
     */
    // public function subscription($request)
    // {
    //   $plan = Plan::find($request->get('plan_id'));
    //   if(!$plan){
    //     return sendError('Please select a Package Plan',null);
    //   }
    //   $user = $request->user();
    //   try{

    //     $response= \Stripe\SubscriptionSchedule::create([
    //       'customer' => $user->stripe_id,
    //       'start_date' => 'now',
    //       'end_behavior' => 'cancel',
    //       'phases' => [
    //         [
    //           'items' => [
    //             [
    //               'price' => $plan->stripe_price_id,
    //               'quantity' => $request->quantity,
    //             ],
    //           ],

    //           'iterations' => $plan->duration,
    //         ],
    //       ],
    //     ]);

    //     $this->paymentSuccess($response,$request,$plan->amount);
    //       $dataView['subscription_plan'] = $plan;

    //         Mail::send('emails.subscription-plan', $dataView, function ($m) use ($request) {
    //           $m->from(env("MAIL_FROM_ADDRESS"), env('MAIL_FROM_NAME'));
    //           $m->to(Auth::user()->email)->subject('successfully buy services');
    //       });
    //           return sendSuccess('Payment SuccessFully',$response );

    //   }catch(\Stripe\Exception\CardException $e) {
    //     return sendError($e->getMessage(),null);
    //   } catch (\Stripe\Exception\RateLimitException $e) {
    //     return sendError($e->getMessage(),null);
    //     // Too many requests made to the API too quickly
    //   } catch (\Stripe\Exception\InvalidRequestException $e) {
    //     return sendError($e->getMessage(),null);
    //   } catch (\Stripe\Exception\AuthenticationException $e) {
    //     return sendError($e->getMessage(),null);
    //   } catch (\Stripe\Exception\ApiConnectionException $e) {
    //     return sendError($e->getMessage(),null);
    //   } catch (\Stripe\Exception\ApiErrorException $e) {
    //     return sendError($e->getMessage(),null);
    //   }catch(\Exception $e){
    //       return sendError('paymentn failed',$e->getMessage());
    //   }

    // }


    /**
     * getPaymentIntent
     *
     * @param  mixed $request
     * @return void
     */
    public function paymentSubscription($request)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required |exists:plans,id',
            'stripe_payment_method' => 'required | string',
            // "card_holder_name" => 'required_if:is_card_save,false',
            // "expire_date" => 'required'
        ]);

        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }

        $user = Auth::user();
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
        $pm = null;
        if ($request->stripe_payment_method && ($request->is_card_save == false || $request->is_card_save == 0)) {
            $pm = $this->attachUserToPaymentMethod($request);
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
                'default_payment_method' => $request->is_other_payment_gateway == true ? $pm : $user->stripe_payment_method,
                'expand' => ["latest_invoice.payment_intent"],
                'description' => $plan->plan_name,
                "metadata" => [
                    "source" => "Open Borders",
                ],
                "cancel_at" =>  $newDateTime
            ]);
            $charageId['charge_id'] = $response->latest_invoice['payment_intent']->charges->data['id'] ?? null;
            $response['client_secret'] = $response->latest_invoice['payment_intent']->client_secret ?? null;
            $data = [
                'client_secret' => $response['client_secret'],
                'plan_detail' => $plan
            ];
            $payload = $this->paymentSuccess($response, $request, $amount, $durations);

            $now = now();
            createDebugLogFile("flat fee payment-{$now}:", 'flatfee-payment-details', [
                "user id" => $user->id,
                "flate fee name " => $plan->plan_name,
                "payment platform " => "openborder cp",
                "stripe_subscription_id" => $response->id ?? null
            ]);
            DB::commit();
            // dispatch(new FlatFeeData($payload->user_id, $payload->id));
            return sendSuccess('Your payment succeeded.Thank you for retaining us.', $data);
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
     * addCard
     *
     * @param  mixed $request
     * @return void
     */
    public function addCard($request)
    {
        // $request = dd($this->stripeConfigration());
        $validator = Validator::make($request->all(), [
            'stripe_token' => 'required',
            'pm_last_four' => 'required',
            "card_holder_name" => "required",
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }
        $data = Auth::user();
        $data['pm_last_four'] = isset($request->pm_last_four) ? $request->pm_last_four : "";
        $data['card_holder_name'] = isset($request->card_holder_name) ? $request->card_holder_name : "";
        $data['expire_date'] = isset($request->expire_date) ? $request->expire_date : "";

        $card = \Stripe\Customer::createSource(
            Auth::user()->stripe_id,
            ['source' => $request->stripe_token]
        );
        $data['stripe_payment_method'] = $card->id;
        $message = $data->card_token ? "Your payment method has been updated." : "Your payment method has been added.";
        $data['card_token'] = $card->id;
        if ($data->save()) {
            $this->stripe->paymentMethods->attach(
                $data->stripe_payment_method,
                ['customer' => $data->stripe_id]
            );
            return sendSuccess($message, $data);
        }
        return sendApiError('Fail to update your payment method, Please try again later.', null);
    }

    /**
     * paymentSuccess
     *
     * @param  mixed $response
     * @param  mixed $request
     * @return void
     */
    public function paymentSuccess($response, $request, $amount, $durations)
    {
        $stripe_fees = ($amount * 0.029) + 0.30;
        $paid_amount = $amount - $stripe_fees;
        $quantity = $request->quantity ? $request->quantity : 1;
        $data['user_id'] = Auth::user()->id;
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
    public function attachUserToPaymentMethod($request)
    {
        $data = Auth::user();

        if (isset($request->is_web) && $request->is_web == 1) {
            $card = \Stripe\Customer::createSource(
                $data->stripe_id,
                ['source' => $request->stripe_payment_method]
            );
            $data['card_token'] = $card->id;
            $data['stripe_payment_method'] = $card->id;
            $data['pm_last_four'] = isset($request->pm_last_four) ? $request->pm_last_four : "";
            $data['card_holder_name'] = isset($request->card_holder_name) ? $request->card_holder_name : "";
            $data['expire_date'] = isset($request->expire_date) ? $request->expire_date : "";
            if ($request->is_other_payment_gateway == false || $request->is_other_payment_gateway == 0) {
                return $data->save();
            }
            return $card->id;;
        }
        // $data['stripe_token'] = isset($request->stripe_token) ? $request->stripe_token : "";
        $data['stripe_payment_method'] = isset($request->stripe_payment_method) ? $request->stripe_payment_method : "";
        $data['pm_last_four'] = isset($request->pm_last_four) ? $request->pm_last_four : "";
        $data['card_holder_name'] =  $request->card_holder_name;
        $data['expire_date'] = $request->expire_date;
        $this->errorLogs($request->all());
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

    public function addCardOld($request)
    {
        // return $this->stripeConfigration();

        $validator = Validator::make($request->all(), [
            'stripe_token' => 'required',
            'pm_last_four' => 'required'
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }
        $data = Auth::user();
        $data['stripe_token'] = isset($request->stripe_token) ? $request->stripe_token : "";
        $data['stripe_payment_method'] = isset($request->stripe_payment_method) ? $request->stripe_payment_method : "";
        $data['pm_last_four'] = isset($request->pm_last_four) ? $request->pm_last_four : "";
        $message = $data->card_token ? "Your payment method has been updated." : "Your payment method has been added.";
        if ($request->is_card_required == 1) {
            $card = \Stripe\Customer::createSource(
                Auth::user()->stripe_id,
                ['source' => $request->stripe_token]
            );
            $data['card_token'] = $card->id;
        }
        if ($data->save()) {

            $this->stripe->paymentMethods->attach(
                $data->stripe_payment_method,
                ['customer' => $data->stripe_id]
            );
            return sendSuccess($message, $data);
        }
        return sendError('Fail to update your payment method, Please try again later', null);
    }
}
