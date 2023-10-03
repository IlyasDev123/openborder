<?php

namespace App\Http\Services\V1;

use DateTime;
use DateInterval;
use App\Models\User;
use App\Models\Consultation;
use App\Http\Services\UserService;
use App\Models\QuestionnaireState;
use Illuminate\Support\Facades\DB;
use App\Models\ConsultationBooking;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscriptionsDetail;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\AcuitySchedulingController;



class ConsultationService
{


    protected $paymentService;
    protected $userService;
    protected $stripe;

    public function __construct(PaymentService $paymentService, UserService $userService)
    {
        $this->paymentService = $paymentService;
        $this->userService = $userService;
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }
    /**
     * getConsultation
     *
     * @param  mixed $request
     * @return void
     */
    public function getConsultation()
    {

        $consultation = Consultation::get();
        if (empty($consultation)) {
            return sendSuccess('Record Not Found !', null);
        }
        return sendSuccess('Success', $consultation);
    }

    /**
     * getConsultation
     *
     * @param  mixed $request
     * @return void
     */
    public function bookConsultation($request)
    {

        $validator = Validator::make($request->all(), [
            'date' => 'required | date',
            'consultation_time' => 'required',
            'user_id' => 'required | exists:users,id',
            'consultation_id' => 'required | exists:consultations,id',
            'consultation_with' => 'required',
            'stripe_token' => 'required',
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }

        DB::beginTransaction();
        try {

            $user = Auth::user();
            $questionnaire = QuestionnaireState::where('user_id', $user->id)->first();
            $input = $request->except('_token');
            $input['date'] = formateDate($input['date']);
            $input['consultation_time'] = formateTime($input['consultation_time']);
            $input['amount'] = $request->amount;
            $input['paid_amount'] = $input['amount'] - $request->amount * 0.03;
            $time = new DateTime($request->date . ' ' . $request->consultation_time);
            $time->add(new DateInterval('PT' . 30 . 'M'));
            $endTime = $time->format('H:i');
            $input['consultation_end_time'] = $endTime;
            $input['appointment_type'] = $request->appointmentTypeID == 22885774  ? "Spanish" : "English";
            $input['questionnaire_summery'] = isset($questionnaire->current_summary) ? $questionnaire->current_summary : null;

            $immigrationType = ["What kind of immigration help do you need" => $request->description];
            $factorOption = isset($questionnaire->current_summary) ? questionnaireSummarySerlizeData($questionnaire->current_summary, $request) : null;
            // return sendSuccess('Consultation booked successfully.',$immigrationType );
            $stripeDescription = $user->first_name . ' ' . mt_rand(10000000, 99999999) . "-Video or Phone Consultation in" . ' ' . $input['appointment_type'] . ' ' .  $input['date'] . ' ' .  $input['consultation_time'];
            $appointment = $this->acuityAppointmentBooking($request, $user, $immigrationType, $factorOption);
            if (isset($appointment['status_code']) && $appointment['status_code'] == 400) {
                return sendError($appointment['message'], null);
            }
            if (isset($request->is_web) && $request->is_web == 1) {
                try {
                    if ($request->is_card_save == true) {
                        $card = $request->stripe_token;
                    } else {
                        $card = $this->stripe->customers->createSource(
                            $user->stripe_id,
                            ['source' => $request->stripe_token]
                        );
                        if ($request->is_other_payment_gateway == false) {
                            $this->saveCard($card, $request);
                        }
                        $card = $card->id;
                    }

                    $response = \Stripe\Charge::create(array(
                        "amount"   => $request->amount * 100,
                        "currency" => 'usd',
                        "customer" => $user->stripe_id,
                        'card' => $card,
                        "description" =>  $stripeDescription,
                        "metadata" => [
                            "source" => "Open Borders",
                        ]
                    ));
                    $input['transaction_id'] = $response->id;
                } catch (\Throwable $th) {
                    $data = $this->cancelAppointment($appointment['id']);
                    return sendError($th->getMessage(), $data);
                }
            } else {
                try {
                    $response = \Stripe\Charge::create(array(
                        "amount"   => $request->amount * 100,
                        "currency" => 'usd',
                        'source' => $request->stripe_token,
                        "description" =>  $stripeDescription,
                        "metadata" => [
                            "source" => "Open Borders",
                        ]
                    ));
                    // dd($response);
                    $input['transaction_id'] = $response->id;
                } catch (\Throwable $th) {
                    $data = $this->cancelAppointment($appointment['id']);
                    return sendError($th->getMessage(), $data);
                }
            }

            $input['acuity_response'] = $appointment['id'];
            $input['emigration_type'] = $request->description;
            $input['timezone'] = $request->timezone ?? "Asia/karachi";

            $consultation_booking = ConsultationBooking::create($input);

            $dataView['consultation_booking'] = $consultation_booking;

            $user->blance_amount = $user->blance_amount + $request->amount;
            $user->save();
        } catch (\Exception $e) {
            DB::rollback();
            return sendError($e->getMessage(), null);
        }
        DB::commit();
        return sendSuccess('The consultation is booked successfully.', $consultation_booking);
    }

    public function acuityAppointmentBooking($request, $user, $immigrationType, $factorOption)
    {
        return  AcuitySchedulingController::configAcuityScheduling()->request('/appointments', array(
            'method' => 'POST',
            'data' => array(
                'appointmentTypeID' => $request->appointmentTypeID,
                'calendarID' => $request->calendarID,
                'firstName' => $user->first_name,
                'lastName' => isset($user->last_name) ? $user->last_name : $user->first_name,
                'datetime' => $request->datetime,
                'email' => $request->email,
                "phone" => $request->phone_no,
                "fields" => $this->formFields($request, $immigrationType, $factorOption),
                'timezone' => isset($request->timezone) ? $request->timezone : "Asia/karachi",
                "notes" => "Consultation Booking"
            )
        ));
    }

    public function formFields($request, $immigrationType, $factorOption)
    {
        if ($request->appointmentTypeID == 16496767) {
            return [
                ["id" => 8330908, "value" => $request->street_address],
                ["id" => 8330909, "value" => $request->city],
                ["id" => 8330923, "value" => $request->state],
                ["id" => 8330927, "value" => $request->zip_code],
                ["id" => 8330931, "value" => $request->country],
                // ["id" => 5394955, "value" => $immigrationType],
                ["id" => 5394955, "value" => $request->description],

                // ["id" => 5394950, "value" => $factorOption],
                // ["id" => 5395755, "value" => "Pay from new platform Open Borders"],
                ["id" => 5395001, "value" => $request->term_and_condition],
            ];
        } elseif ($request->appointmentTypeID == 22885774) {
            return  [
                ["id" => 9776893, "value" => $request->street_address],
                ["id" => 9776894, "value" => $request->city],
                ["id" => 9776894, "value" => $request->state],
                ["id" => 9776896, "value" => $request->zip_code],
                ["id" => 9776897, "value" => $request->country],
                ["id" => 9776898, "value" => $request->description], //$request->description
                // ["id" => 9776890, "value" => $factorOption],
                // ["id" => 9777898, "value" => "Pay from new platform Open Borders"],
                ["id" => 9776899, "value" => $request->term_and_condition],
            ];
        } else {
            [];
        }
    }

    public function cancelAppointment($id)
    {
        return AcuitySchedulingController::configAcuityScheduling()->request("/appointments/{$id}/cancel", array(
            'method' => 'PUT',
            'data' => array(
                'cancelNote' => "Payment Failed"
            )
        ));
    }

    public function totalConsultationBooking()
    {
        return ConsultationBooking::count();
    }

    public function totalRevenue()
    {
        return UserSubscriptionsDetail::all()->sum('total_amount');
    }

    public function acuityForm()
    {
        $getForm = $this->configAcuityScheduling()->request("/forms");
        return sendSuccess('success', $getForm);
    }

    public function bookConsultationGuestuser($request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required | string',
            'last_name' => 'required | string',
            'date' => 'required | date',
            'consultation_time' => 'required',
            'consultation_id' => 'required | exists:consultations,id',
            'consultation_with' => 'required',
            'stripe_token' => 'required',
            'amount' => 'required',
            'email' => 'sometimes|string|email',
            'phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8 |max:18',
        ]);
        if ($validator->fails()) {
            return sendApiError($validator->messages()->first(), null);
        }

        $user = $this->userService->createUser($request);

        DB::beginTransaction();
        try {

            $questionnaire = QuestionnaireState::where('user_id', $user->id)->first();
            $input = $request->except('_token');
            $input['date'] = formateDate($input['date']);
            $input['consultation_time'] = formateTime($input['consultation_time']);
            $input['amount'] = $request->amount;
            $input['paid_amount'] = $input['amount'] - $request->amount * 0.03;
            $time = new DateTime($request->date . ' ' . $request->consultation_time);
            $time->add(new DateInterval('PT' . 30 . 'M'));
            $endTime = $time->format('H:i');
            $input['consultation_end_time'] = $endTime;
            $input['appointment_type'] = $request->appointmentTypeID == 22885774  ? "Spanish" : "English";
            $input['questionnaire_summery'] = isset($questionnaire->current_summary) ? $questionnaire->current_summary : null;

            $immigrationType = ["What kind of immigration help do you need" => $request->description];
            $factorOption = isset($questionnaire->current_summary) ? questionnaireSummarySerlizeData($questionnaire->current_summary, $request) : null;
            // return sendSuccess('Consultation booked successfully.',$immigrationType );
            $stripeDescription = $user->first_name . ' ' . mt_rand(10000000, 99999999) . "-Video or Phone Consultation in" . ' ' . $input['appointment_type'] . ' ' .  $input['date'] . ' ' .  $input['consultation_time'];
            $appointment = $this->acuityAppointmentBooking($request, $user, $immigrationType, $factorOption);
            if (isset($appointment['status_code']) && $appointment['status_code'] == 400) {
                return sendApiError($appointment['message'], null);
            }
            if (isset($request->is_web) && $request->is_web == 1) {
                try {
                    $card = $this->stripe->customers->createSource(
                        $user->stripe_id,
                        ['source' => $request->stripe_token]
                    );
                    $response = \Stripe\Charge::create(array(
                        "amount"   => $request->amount * 100,
                        "currency" => 'usd',
                        "customer" => $user->stripe_id,
                        'card' => $card->id,
                        "description" =>  $stripeDescription,
                        "metadata" => [
                            "source" => "Open Borders",
                        ]
                    ));
                    $input['transaction_id'] = $response->id;
                } catch (\Throwable $th) {
                    $data = $this->cancelAppointment($appointment['id']);
                    $this->errorLogs($th->getMessage());
                    return sendApiError($th->getMessage(), $data);
                }
            } else {
                try {
                    $response = \Stripe\Charge::create(array(
                        "amount"   => $request->amount * 100,
                        "currency" => 'usd',
                        'source' => $request->stripe_token,
                        "description" =>  $stripeDescription,
                        "metadata" => [
                            "source" => "Open Borders",
                        ]
                    ));

                    $input['transaction_id'] = $response->id;
                } catch (\Throwable $th) {
                    $data = $this->cancelAppointment($appointment['id']);

                    $this->errorLogs($th->getMessage());
                    return sendApiError($th->getMessage(), $data);
                }
            }

            $input['acuity_response'] = $appointment['id'];
            $input['emigration_type'] = $request->description;
            $input['timezone'] = $request->timezone ?? "Asia/karachi";

            $consultation_booking = ConsultationBooking::create($input);

            $dataView['consultation_booking'] = $consultation_booking;

            $user->blance_amount = $user->blance_amount + $request->amount;
            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return sendApiError($e->getMessage(), null);
        }

        if ($request->is_login == 1) {
            $tokenResult = $user->createToken('Personal Access Token');
            $data['access_token'] = $tokenResult->accessToken;
            $data['token_type'] = 'Bearer';
            $data['user'] = User::where('id', $user->id)->with('petitionerDetail')->first();
            return sendSuccess('The consultation is booked successfully.', $data);
        }
        $data = [
            "consulatation" => $consultation_booking,
        ];
        return sendApiSuccess('The consultation is booked successfully.', $data);
    }

    /**
     * @param mixed $error
     *
     * @return [type]
     */
    public function errorLogs($error)
    {
        createDebugLogFile("consulatation-error:", 'consulatation-error', [
            "payment platform " => "openborder cp",
            "error" => $error
        ]);
    }


    public function saveCard($card, $request)
    {
        $user = auth()->user();
        $user['card_token'] = $card->id;
        $user['stripe_payment_method'] = $card->id;
        $user['pm_last_four'] = isset($request->pm_last_four) ? $request->pm_last_four : "";
        $user['card_holder_name'] =  $request->card_holder_name;
        $user['expire_date'] = $request->expire_date;
        return $user->save();
    }


    public function testConsultation($request)
    {
        $user = Auth::user();

        if (isset($request->is_web) && $request->is_web == 1) {
            try {
                if ($request->is_card_save == true) {
                    $card = $request->stripe_token;
                } else {
                    $card = $this->stripe->customers->createSource(
                        $user->stripe_id,
                        ['source' => $request->stripe_token]
                    );
                    $this->saveCard($card, $request);
                    $card = $card->id;
                }

                $response = \Stripe\Charge::create(array(
                    "amount"   => $request->amount * 100,
                    "currency" => 'usd',
                    "customer" => $user->stripe_id,
                    'card' => $card,
                    "metadata" => [
                        "source" => "Open Borders",
                    ]
                ));
                $input['transaction_id'] = $response->id;
                return sendSuccess('The consultation is booked successfully.', $user);
            } catch (\Throwable $th) {
                return sendError($th->getMessage(), null);
            }
        } else {
            try {
                $response = \Stripe\Charge::create(array(
                    "amount"   => $request->amount * 100,
                    "currency" => 'usd',
                    'source' => $request->stripe_token,
                    "metadata" => [
                        "source" => "Open Borders",
                    ]
                ));
                // dd($response);
                $input['transaction_id'] = $response->id;
                return sendSuccess('The consultation is booked successfully.', $user);
            } catch (\Throwable $th) {
                return sendError($th->getMessage(), null);
            }
        }
    }
}
