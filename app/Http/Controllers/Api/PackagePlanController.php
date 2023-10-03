<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\QuestionnaireState;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscriptionsDetail;
use App\Http\Services\PackagePlanService;
use App\Http\Resources\UserSubscriptionResource;

class PackagePlanController extends Controller
{
    public function userPurchasePlans(PackagePlanService $packagePlanService)
    {
        $id = Auth::user()->id;
        return $packagePlanService->userPurchasePlanHistory($id);
    }

    public function subscriptionUserHistory()
    {
        $data =   UserSubscriptionsDetail::with('user', 'plan')->latest()->first();
        $response =  new UserSubscriptionResource($data);
        $questionnaire =  $this->getQuestionnaireStateSummeryForZapier($data->user->id, $response);
        return sendApiSuccess("success", $questionnaire, 200);
    }

    public function getQuestionnaireStateSummeryForZapier($userId, $data)
    {
        $res = QuestionnaireState::where('user_id', $userId)->first();
        if(empty($res)){
            return $data;
        }
        $questionnaire = json_decode($res->current_summary);
        $immigrationHistory = [];
        $factorsOptions = [];
        $inadmissibility = [];
        foreach ($questionnaire as $question) {
            $question = json_decode($question, true);
            $description = strip_tags($question['description']);
            switch ($question['category']) {
                case 'immigrationHistory':
                    array_push($immigrationHistory, $description);
                    break;
                case ('factorsOptions'):
                    array_push($factorsOptions, $description);
                    break;

                case ('inadmissibility'):
                    array_push($inadmissibility, $description);
                    break;

                default:
                    # code...
                    break;
            }
        }
        $questionnaire = [];
        array_push($questionnaire, ["data" => $data, "immigrationHistory" => $immigrationHistory, "factorsOptions" => $factorsOptions, "inadmissibility" => $inadmissibility]);
        return $questionnaire;
    }
}
