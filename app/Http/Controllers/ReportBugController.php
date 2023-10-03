<?php

namespace App\Http\Controllers;

use App\CommanFunctions\Constants;
use App\Models\ReportBug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ReportBugController extends Controller
{
    public function storeUserBugsReport(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'node_url' => 'required',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return sendError($validator->messages()->first(), null);
            }
            $input = $request->except('_token');
            $input['user_id'] = auth()->id();
            $reportBugs = ReportBug::create($input);
            $reportBugs= $reportBugs ->load('user')->toArray();

            try {
                Mail::send('emails.bugs-report',$reportBugs, function ($m) {
                    $m->from(env("MAIL_FROM_ADDRESS"), env('MAIL_FROM_NAME'));
                    $m->to("shahid@bordercrossinglaw.com")
                    ->cc(env("MAIL_CC_ADDRESS"))->subject(Constants::BUG_REPORT_EMAIL_SUBJECT);
                });
            } catch (\Exception $e) {
                return sendError(" Failed to send Email . But create bugs successfully ", $e->getMessage());
            }
            return sendSuccess('Thanks for reporting this issue. We will look into it right away.',  $reportBugs);
        } catch (\Throwable $th) {
            return sendError('Failed', $th->getMessage());
        }
    }

    public function sendTextEmail()
    {
        try {
            Mail::send('emails.test-email', [], function ($m) {
                $m->from(env("MAIL_FROM_ADDRESS"), env('MAIL_FROM_NAME'));
                $m->to("openborder@yopmail.com")
                ->cc(env("MAIL_CC_ADDRESS"))->subject("Test Email");
            });
        } catch (\Exception $e) {
            return sendError(" Test sending email", $e->getMessage());
        }
    }
}
