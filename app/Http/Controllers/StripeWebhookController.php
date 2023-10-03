<?php

namespace App\Http\Controllers;

use Mail;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Plan;
use App\Models\User;
use Stripe\Customer;
use Stripe\StripeClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UserSubscriptionsDetail;
use App\Mail\SendSubscriptionFailNotificationMail;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{

    private function handleSubscriptionFailure($subscription)
    {
        $user = User::where('stripe_id', $subscription->customer)->first()->toArray();
        $plan = $this->getSubscriptionDetail($subscription);
        $user = [
            "first_name" => $user['first_name'],
            "last_name" => $user['last_name'],
            "email" => $user['email'],
            "package_name" => $plan->plan_name,
            "template_path" => "emails.stripe-hook"
        ];
        // Send email to client
        $this->sendEmailWhenFailSubscription($user);

        // Send email to admin
        $admin = [
            "first_name" => $user['first_name'],
            "last_name" => $user['last_name'],
            "email" => $user['email'],
            "package_name" => $plan->plan_name,
            "template_path" => "emails.admin-notification",
        ];
        Mail::to(env('MAIL_TO_ADDRESS'))->cc(env('MAIL_CC_ADDRESS'))
        ->send(new SendSubscriptionFailNotificationMail($admin));
    }

    public function sendEmailWhenFailSubscription($data)
    {
        Mail::to($data['email'])
        ->send(new SendSubscriptionFailNotificationMail($data));
    }

    public function getSubscriptionDetail($subscription)
    {
        $planId = UserSubscriptionsDetail::where('stripe_subscription_id', $subscription->id)->pluck('plan_id');
        $plan = Plan::find($planId);
        return $plan;
    }

    public function stripeWebHooks(Request $request)
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                env('STRIPE_WEBHOOK_SECRET')
            );
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'customer.subscription.deleted':
                $this->handleSubscriptionFailure($event->data->object);
                // ... handle other event types
                break;
            case 'invoice.payment_failed':
                $this->handleSubscriptionFailure($event->data->object);
                break;
            default:
                break;
        }

        return response()->json(['status' => 'success']);
    }
}
