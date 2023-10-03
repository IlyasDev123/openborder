<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConsultationBooking;
use App\Models\UserSubscriptionsDetail;
use App\Http\Resources\UserSubscriptionResource;
use App\Http\Resources\ConsultationZapPayloadResource;

class ZapierWebHookController extends Controller
{
    public function getAllQuestionnaireStateSummeryForZapier()
    {
        $data =  ConsultationBooking::with('consultation', 'user')->latest()->get();
        $data = ConsultationZapPayloadResource::collection($data);
        return $data->toArray($data);
    }

    public function subscriptionUserHistory()
    {
        $response =   UserSubscriptionsDetail::with('user.questionnaireStates', 'plan','user.petitionerDetail')->latest()->get();
        $response =  UserSubscriptionResource::collection($response);
        return $response->toArray($response);
    }
}
