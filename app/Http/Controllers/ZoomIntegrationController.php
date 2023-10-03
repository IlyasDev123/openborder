<?php

namespace App\Http\Controllers;

use App\Http\Traits\ZoomJWT;
use Illuminate\Http\Request;
use App\CommanFunctions\Constants;
use Illuminate\Support\Facades\Validator;

class ZoomIntegrationController extends Controller
{
    use ZoomJWT;

    public function createZoomMeeting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic' => 'required|string',
            'start_date' => 'required|date',
            'agenda' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'data' => $validator->errors(),
            ];
        }
        $data = $validator->validated();

        $path = 'users/me/meetings';
        $response = $this->zoomPost($path, [
            'topic' => $data['topic'],
            'type' => Constants::MEETING_TYPE_SCHEDULE,
            'start_time' => $this->toZoomTimeFormat($data['start_date']),
            'duration' => Constants::MEETING_DURATION,
            'agenda' => $data['agenda'],
        ]);


        return [
            'success' => $response->status() === 201,
            'data' => json_decode($response->body(), true),
        ];
    }
}
