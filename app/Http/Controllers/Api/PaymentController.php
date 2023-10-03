<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\User;
use AcuityScheduling;
use function Psy\debug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Services\PaymentService;
use App\Http\Services\GustPaymentService;

use App\Notifications\SubscriptionCreate;
use Illuminate\Support\Facades\Notification;

class PaymentController extends Controller
{

    protected $paymentService;
    protected $gustPaymentService;

    /**
     * __construct
     *
     * @param  mixed $users
     * @return void
     */
    public function __construct(PaymentService $paymentService, GustPaymentService $gustPaymentService)
    {
        $this->paymentService = $paymentService;
        $this->gustPaymentService = $gustPaymentService;
    }

    public function payment(Request $request)
    {
        return $this->paymentService->singlePayment($request);
    }

    public function subscription(Request $request)
    {
        return $this->paymentService->subscription($request);
    }

    public function serviceSubscription(Request $request)
    {
        return $this->gustPaymentService->serviceSubscription($request);
    }

    public function updateCard(Request $request)
    {
        return $this->gustPaymentService->updateCard($request);
    }

    public function getUser(Request $request)
    {
        return $this->gustPaymentService->getUser($request);
    }
    /**
     * stripeWebHooks
     *
     * @return void
     */

    public function stripeConfigration()
    {
        return $this->paymentService->stripeConfigration();
    }

    public function paymentSubscription(Request $request)
    {
        return $this->paymentService->paymentSubscription($request);
    }

    /**
     * addCard
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function addCard(Request $request)
    {

        $add_card = $this->paymentService->addCard($request);
        if ($add_card['status'] == false) {
            return sendApiError($add_card['message'], null);
        }
        return sendApiSuccess($add_card['message'], $add_card['data'], null);
    }
}
