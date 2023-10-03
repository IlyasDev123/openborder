<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendDataToZapier implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    public $d;
    public $userDetail ;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $d, $userDetail)
    {
        $this->data = $data;
        $this->d = $d;
        $this->userDetail = $userDetail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(empty($this->data)){
            $questionnaires = [];
            array_push($questionnaires, ["data" => $this->d, "user_detail" => $this->userDetail]);
            $response = Http::post('https://hooks.zapier.com/hooks/catch/2967384/bwiydve/', [
                $questionnaires[0]
            ]);

            Log::build(['driver' => 'single',  'path' => storage_path('logs/zap-webhooks.log'),])
            ->debug("Zap webHook response :", [$response]);
            return $response;
        }
        $questionnaire = json_decode($this->data);
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
        array_push($questionnaires, ["data" => $this->d, "user_detail" => $this->userDetail, "immigrationHistory" => $immigrationHistory, "factorsOptions" => $factorsOptions, "inadmissibility" => $inadmissibility]);
        $response = Http::post('https://hooks.zapier.com/hooks/catch/2967384/bwiydve/', [
            $questionnaires[0]
        ]);

        Log::build(['driver' => 'single',  'path' => storage_path('logs/zap-webhooks.log'),])
            ->debug("Zap webHook response :", [$response]);
        return $response;
    }
}
