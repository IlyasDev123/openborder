<?php

namespace App\Http\Services;

use Illuminate\Support\Arr;
use App\Models\QuestionnaireState;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;



class QuestionnaireStateService
{

    /**
     * storeQuestionnaireState
     *
     * @return void
     */
    public function storeQuestionnaireState($request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required |exists:users,id',
            'last_question' => 'required',
            'prev_selections' => 'required',
            'current_summary' => 'required',
            'status' => ''
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }

        $QuestionnaireState = QuestionnaireState::updateOrcreate(
            ['user_id' => $request->user_id],
            [
                'user_id' => $request->user_id,
                'last_question' => $request->last_question,
                'prev_selections' => $request->prev_selections,
                'current_summary' => $request->current_summary,
                'questions_order' => $request->questions_order
            ]
        );
        if (!$QuestionnaireState) {
            return sendError('Some went wrong please try again!', null);
        }
        return sendSuccess('Your answers are saved.You may continue the guide later.', $QuestionnaireState);
    }

    /**
     * getQuestionnaireState
     *
     * @return void
     */
    public function getQuestionnaireState($request)
    {

        $QuestionnaireState = QuestionnaireState::where('user_id', $request->user_id)->first();
        if (!$QuestionnaireState) {
            return sendSuccess('Record Not Found', null);
        }
        return sendSuccess('Success', $QuestionnaireState);
    }

    /**
     * getQuestionnaireState
     *
     * @return void
     */
    public function deleteQuestionnaireState($request)
    {
        $QuestionnaireState = QuestionnaireState::where('user_id', $request->user_id)->first();;
        if (!$QuestionnaireState) {
            return sendSuccess('Record Not Found.', null);
        }
        $QuestionnaireState = $QuestionnaireState->delete();

        return sendSuccess('SuccessFull delete.', null);
    }

    public function getQuestionnaireStateEmail($request)
    {
        $arrData["data"] = $request;
        $user = auth()->user();
        try {
            if ($user->is_guest == true) {
                return sendError("You need to register first!", null);
            }
            Mail::send('emails.questionnaireSummery',  $arrData, function ($m) use ($user) {
                $m->from(env("MAIL_FROM_ADDRESS"), env('MAIL_FROM_NAME'));
                $m->to($user->email)->subject('Your Evaluation from Open Borders');
            });
            return sendSuccess('Email send successfully.', null);
        } catch (\Exception $e) {
            return sendError("Failed to send email ", $e->getMessage());
        }
    }
}
