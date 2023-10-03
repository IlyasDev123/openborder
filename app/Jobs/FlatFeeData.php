<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\QuestionnaireState;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use App\Models\UserSubscriptionsDetail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Http\Resources\UserSubscriptionResource;

class FlatFeeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payload;
    public $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload, $id)
    {
        $this->payload = $payload;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data =   UserSubscriptionsDetail::with('user', 'plan')->find($this->id);
        $questionnaireSummery = QuestionnaireState::where('user_id', $this->payload)->first();
        $data = new UserSubscriptionResource($data);
        if (empty($questionnaireSummery)) {
            $response = Http::post('https://hooks.zapier.com/hooks/catch/2967384/bwiya72/', [
                $data
            ]);
            return $response;
        }
        $questionnaire = json_decode($questionnaireSummery->current_summary);
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
        $questionnaires = [];

        array_push($questionnaires, ["data" => $data, "immigrationHistory" => $immigrationHistory, "factorsOptions" => $factorsOptions, "inadmissibility" => $inadmissibility]);
        // dd($questionnaires[0]);
        $response = Http::post('https://hooks.zapier.com/hooks/catch/2967384/bwiya72/', [
            $questionnaires[0]
        ]);
        Log::build(['driver' => 'single',  'path' => storage_path('logs/zap-webhooks.log'),])
            ->debug("Zap webHook response flat fees :", [$response]);
        return $response;
    }
}
