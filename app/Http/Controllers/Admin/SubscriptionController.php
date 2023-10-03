<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserSubscriptionsDetail;

class SubscriptionController extends Controller
{
    public function getSubscriptionUserList()
    {
        $subscriptionList = UserSubscriptionsDetail::with('user', 'plan')->get();
        return view('admin.subscription_list.index', compact('subscriptionList'));
    }
}
