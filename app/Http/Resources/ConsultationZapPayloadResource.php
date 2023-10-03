<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\Api\AcuitySchedulingController;

class ConsultationZapPayloadResource extends JsonResource
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
            "first_name" => $this->user->first_name,
            "last_name" => $this->user->last_name,
            "phone_number" => $this->user->phone_no ?? "",
            "email" => $this->user->email,
            "address" => $this->user->user_address ?? "",
            "stripe_customer" => $this->user->stripe_id ?? "",
            "transaction_id" => $this->transaction_id,
            "amount" => $this->amount,
            "paid_amount" => $this->paid_amount,
            "consultation_id" => $this->consultation_id,
            "date" => $this->date,
            "consultation_time" => $this->consultation_time,
            "consultation_type" =>  $this->appointment_type,
            "consultation_with" => $this->consultation_with,
            "emigration_type" => $this->emigration_type ?? "",
            "timezone" => $this->timezone ?? "",
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "consultation_end_time" => $this->consultation_end_time,
            "source" => "Open Borders",
            "zoom_url" =>  $this->when($this->acuity_response, function () {
                $id = $this->acuity_response;
                return (new AcuitySchedulingController())->getAppointmentById($id);
            }),
            "evaluation_summary" => $this->when($this->questionnaire_summery != null, function () {
                $qu = $this->questionnaire_summery;
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
                    if ($this->appointment_type == 'English') {
                        $description = strip_tags(html_entity_decode($question['description']));
                        $description = preg_replace('/[^(\x20-\x7F)]*/', '', $description);
                    } else {
                        $description = strip_tags(html_entity_decode($question['description_ES']));
                        $description = preg_replace('/[^(\x20-\x7F)]*/', '', $description);
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
            // "immigrationHistory" => $this->when($this->questionnaire_summery != null, function () {
            //     $qu = $this->questionnaire_summery;
            //     $questionnaire = json_decode($qu);
            //     $immigrationHistory = [];
            //     foreach ($questionnaire as $question) {
            //         if (isset($question->id)) {
            //             $question = json_encode($question);
            //         }
            //         $question = json_decode($question, true);
            //         if ($this->appointment_type == 'English') {
            //             $description = strip_tags(html_entity_decode($question['description']));
            //         } else {
            //             $description = strip_tags(html_entity_decode($question['description_ES']));
            //         }
            //         switch ($question['category']) {
            //             case 'immigrationHistory':

            //                 array_push($immigrationHistory, $description);
            //                 break;
            //             default:
            //                 break;
            //         }
            //     }
            //     // $questionnaire = [];
            //     // array_push($questionnaire, ["immigrationHistory" => $immigrationHistory, "factorsOptions" => $factorsOptions, "inadmissibility" => $inadmissibility]);
            //     return $immigrationHistory;
            // }),
            // "inadmissibility" => $this->when($this->questionnaire_summery != null, function () {
            //     $qu = $this->questionnaire_summery;
            //     $questionnaire = json_decode($qu);
            //     $inadmissibility = [];
            //     foreach ($questionnaire as $question) {
            //         if (isset($question->id)) {
            //             if (isset($question->id)) {
            //                 $question = json_encode($question);
            //             }
            //         }
            //         $question = json_decode($question, true);

            //         if ($this->appointment_type == 'English') {
            //             $description = strip_tags(html_entity_decode($question['description']));
            //         } else {
            //             $description = strip_tags(html_entity_decode($question['description_ES']));
            //         }
            //         switch ($question['category']) {
            //             case ('inadmissibility'):
            //                 array_push($inadmissibility, $description);
            //                 break;
            //             default:
            //                 break;
            //         }
            //     }
            //     // $questionnaire = [];
            //     // array_push($questionnaire, ["inadmissibility" => $inadmissibility]);
            //     return $inadmissibility;
            // }),
        ];
    }
}
