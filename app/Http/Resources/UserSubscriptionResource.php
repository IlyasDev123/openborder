<?php

namespace App\Http\Resources;

use App\CommanFunctions\Constants;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "status" => $this->status,
            "total_amount" => $this->total_amount,
            "paid_amount" => $this->paid_amount,
            "quantity" => $this->quantity,
            "stripe_customer" => $this->stripe_customer,
            "stripe_subscription_id" => $this->stripe_subscription_id,
            "start_at" => $this->stripe_start_at,
            "ended_at" => $this->stripe_ended_at,
            "stripe_status" => $this->stripe_status,
            "first_name" => $this->user->first_name,
            "last_name" => $this->user->last_name,
            "phone_number" => $this->user->phone_no,
            "email" => $this->user->email,
            "address" => $this->user->user_address,
            "plan_name" => $this->plan->plan_name,
            "description" => strip_tags($this->plan->description),
            "plan_type" => $this->plan->plan_type,
            "recurring_period" => $this->plan->recurring_period,
            "remaining_duration" => $this->remaining_recurring_payment,
            "petitioner_first_name" => $this->user->petitionerDetail->first_name ?? null,
            "petitioner_last_name" => $this->user->petitionerDetail->last_name ?? null,
            "petitioner_email" => $this->user->petitionerDetail->email ?? null,
            "source" => "Open Borders",

            "evaluation_summary" => $this->when(isset($this->user->questionnaireStatesSummary->current_summary), function () {
                $qu = $this->user->questionnaireStatesSummary->current_summary;
                $questionnaire = json_decode($qu, true);
                // $factorsOptions = [];
                $immigrationHistory = [];
                $inadmissibility = [];
                foreach ($questionnaire as $question) {
                    if (isset($question['id'])) {
                        $question = json_encode($question);
                    }
                    $question = json_decode($question, true);
                    // $description_eu = $question['description'];
                    if ($this->user->language == Constants::ENGLISH) {
                        $description = strip_tags(html_entity_decode($question['description']));
                        $description = str_replace('/[^(\x20-\x7F)]*/', '', $description);
                    } else {
                        $description = strip_tags(html_entity_decode(isset($question['description_ES']) ? $question['description_ES'] : ''));
                        $description = str_replace('/[^(\x20-\x7F)]*/', '', $description);
                    }

                    switch ($question['category']) {
                            // case ('factorsOptions'):
                            //     array_push($factorsOptions, $description);
                            //     break;
                        case 'immigrationHistory':
                            array_push($immigrationHistory, $description);
                            break;
                        case ('inadmissibility'):
                            array_push($inadmissibility, $description);
                            break;
                        default:
                            break;
                    }
                }
                $questionnaire = [];
                array_push($questionnaire, ["immigrationHistory" => $immigrationHistory, "inadmissibility" => $inadmissibility]);
                return $questionnaire;
            }),
        ];
    }
}
