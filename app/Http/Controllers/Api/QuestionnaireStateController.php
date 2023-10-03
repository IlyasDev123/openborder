<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ConsultationBooking;
use App\Http\Controllers\Controller;
use App\Http\Services\QuestionnaireStateService;
use App\Http\Resources\ConsultationZapPayloadResource;

class QuestionnaireStateController extends Controller
{

    protected $questionnaireStateService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\UserRepository  $users
     * @return void
     */
    public function __construct(QuestionnaireStateService $questionnaireStateService)
    {
        $this->questionnaireStateService = $questionnaireStateService;
    }

    /**
     * questionnaire state
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function StoreQuestionnaireState(Request $request)
    {

        $data = $this->questionnaireStateService->StoreQuestionnaireState($request);
        if ($data['status'] == false) {
            return sendApiError($data['message'], null);
        }
        return sendApiSuccess($data['message'], $data['data'], null);
    }

    /**
     * get questionnaire state
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function getQuestionnaireState(Request $request)
    {

        $data = $this->questionnaireStateService->getQuestionnaireState($request);
        if ($data['status'] == false) {
            return sendApiError($data['message'], null);
        }
        return sendApiSuccess($data['message'], $data['data'], null);
    }

    /**
     * get questionnaire state
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function deleteQuestionnaireState(Request $request)
    {

        $data = $this->questionnaireStateService->deleteQuestionnaireState($request);
        if ($data['status'] == false) {
            return sendApiError($data['message'], null);
        }
        return sendApiSuccess($data['message'], $data['data'], null);
    }

    public function getQuestionnaireStateEmail(Request $request)
    {

        return  $this->questionnaireStateService->getQuestionnaireStateEmail($request->all());
        // if ($data['status'] == false) {
        //     return sendApiError($data['message'], null);
        // }
        // return sendApiSuccess($data['message'], $data['data'], null);
    }

    public function getQuestionnaireStateSummeryForZapier($transaction_id)
    {
        $data =  ConsultationBooking::where('transaction_id', $transaction_id)->first();
        $questionnaire = json_decode($data->questionnaire_summery);
        $immigrationHistory = [];
        $factorsOptions = [];
        $inadmissibility = [];
        $data = collect($data)->except('questionnaire_summery');
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
        $data = array_push($questionnaire, ["data" => $data, "immigrationHistory" => $immigrationHistory, "factorsOptions" => $factorsOptions, "inadmissibility" => $inadmissibility]);

        return sendApiSuccess("success", $questionnaire, 200);
    }


  

//  $qu = $this->questionnaire_summery;
//                 $questionnaire = json_decode($qu);
//                 $immigrationHistory = [];
//                 $factorsOptions = [];
//                 $inadmissibility = [];
//                 foreach ($questionnaire as $question) {
//                     $question = json_decode($question, true);
//                     $description = strip_tags($question['description']);
//                     switch ($question['category']) {
//                         case 'immigrationHistory':
//                             array_push($immigrationHistory, $description);
//                             break;
//                         case ('factorsOptions'):
//                             array_push($factorsOptions, $description);
//                             break;

//                         case ('inadmissibility'):
//                             array_push($inadmissibility, $description);
//                             break;

//                         default:
//                             break;
//                     }
//                 }
//                 $questionnaire = [];
//                 array_push($questionnaire, ["immigrationHistory" => $immigrationHistory, "factorsOptions" => $factorsOptions, "inadmissibility" => $inadmissibility]);
//                 return $questionnaire;
//             })

}
