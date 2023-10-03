<?php

namespace App\Http\Services;

use App\Models\ConsultationBooking;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSubscriptionsDetail;


class PackagePlanService {

    public function userPurchasePlanHistory($id){

       $data['purches_plan_history']= UserSubscriptionsDetail::with('plan')->where('user_id',$id)->select('id','total_amount','quantity','created_at','plan_id','stripe_start_at','stripe_ended_at')->latest()->get();
        $data['consultation_booking_detail']= ConsultationBooking::where('user_id', $id)->latest()->get();
      if(!$data){
          sendSuccess('No Record Found',null);
      }

      return sendSuccess('success',$data);
    }
}
