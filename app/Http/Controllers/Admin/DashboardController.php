<?php

namespace App\Http\Controllers\Admin;


use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\ConsultationService;
use App\Models\UserSubscriptionsDetail;

class DashboardController extends Controller
{

    protected $consultationService;

    /**
     * __construct
     *
     * @param  mixed $consultationService
     * @return void
     */
    public function __construct(ConsultationService $consultationService)
    {
        $this->consultationService = $consultationService;
    }


    public function index(Request $request)
    {

        $data = [];

        // Register users (daily , weekly, monthly ,yearly)

        $data['total_users'] = User::where('is_guest', 0)->count();
        $data['total_subscriber'] = UserSubscriptionsDetail::count();
        $data['total_consultation'] = $this->consultationService->totalConsultationBooking();
        $data['total_revenue'] = $this->consultationService->totalRevenue();

        return view('admin.dashboard.dashboard', $data);
    }

    public function getUserList()
    {
        $users = User::where('is_guest',0)->with('user_address', 'questionnaireStatesSummary')->get();
        return view('admin.users.index', compact('users'));
    }
}
