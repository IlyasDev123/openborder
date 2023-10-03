<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\V1\ConsultationService;

class ConsultationController extends Controller
{
    /**
     * bookConsultation
     *
     * @param  mixed $request
     * @param  mixed $consultationService
     * @return void
     */
    public function bookConsultation(Request $request, ConsultationService $consultationService)
    {
        $consultation = $consultationService->bookConsultation($request);
        if ($consultation['status'] == false) {
            return sendApiError($consultation['message'], null);
        }
        return sendApiSuccess($consultation['message'], $consultation['data'], null);
    }

    /**
     * @param Request $request
     * @param ConsultationService $consultationService
     *
     * @return [type]
     */
    public function bookConsultationGuestuser(Request $request, ConsultationService $consultationService)
    {
        return $consultationService->bookConsultationGuestuser($request);
    }

    /**
     * @param Request $request
     * @param ConsultationService $consultationService
     *
     * @return [type]
     */
    public function getConsultation(Request $request, ConsultationService $consultationService)
    {
        $consultation = $consultationService->getConsultation();

        if ($consultation['status'] == false) {
            return sendApiError($consultation['message'], null);
        }
        return sendApiSuccess($consultation['message'], $consultation['data'], null);
    }
}
