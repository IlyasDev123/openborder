<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\ZapierWebHookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ConsultationController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\PackagePricingController;
use App\Http\Controllers\StripeWebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/', [AuthController::class, 'home'])->name('login');
Route::post('/stripe-webhook', [StripeWebhookController::class, 'stripeWebHooks']);
Route::namespace('admin')->group(function () {
    // used for admin login
    // Route::get('/', [AuthController::class,'home'])->name('/admin');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login');

    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::group(['middleware' => 'auth.admin'], function () {

        Route::post('api/package/plan', [PlanController::class, 'getPackagePlanById']);
        // user details
        Route::namespace('users')->group(function () {
            Route::get('/index', [UserController::class, 'index'])->name('user.index');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
            Route::post('/update', [UserController::class, 'update'])->name('user.update');
            Route::post('/delete', [UserController::class, 'destroy'])->name('user.delete');
            Route::post('/change-password', [UserController::class, 'changePassword'])->name('user.change.password');
        });

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/plan-sortable', [PackagePricingController::class, 'sortedPositionSave'])->name('plan-sortable');
        Route::post('/package-sortable', [PackageController::class, 'sortedPositionSave'])->name('package-sortable');
        Route::get('/admin/users', [DashboardController::class, 'getUserList'])->name('admin.users');
        Route::get('/admin/profile', [AuthController::class, 'myProfile'])->name('update.profile');
        Route::post('/save_profile', [AuthController::class, 'save_profile'])->name('admin.save_profile');
        Route::post('/change_password', [AuthController::class, 'change_password'])->name('admin.change_password');
        Route::get('/package/index', [PackageController::class, 'index'])->name('package.index');
        Route::get('/package/create', [PackageController::class, 'create'])->name('admin.package.create');
        Route::post('/package/store', [PackageController::class, 'store'])->name('admin.package.store');
        Route::get('/package/edit/{id}', [PackageController::class, 'edit'])->name('admin.package.edit');
        Route::post('/package/update/{id}', [PackageController::class, 'update'])->name('admin.package.update');
        Route::post('/package/active', [PackageController::class, 'activePackage'])->name('admin.package.active');
        Route::post('/package/deactivate', [PackageController::class, 'deactivatePackage'])->name('admin.package.deactivate');
        Route::post('/package/delete', [PackageController::class, 'destroy'])->name('admin.package.delete');

        Route::namespace('package')->group(function () {
            Route::get('/plan/index', [PackagePricingController::class, 'index'])->name('package.plan.index');
            Route::get('/plan/create', [PackagePricingController::class, 'create'])->name('package.plan.create');
            Route::post('/plan/store', [PackagePricingController::class, 'store'])->name('package.plan.store');
            Route::get('/plan/edit/{id}', [PackagePricingController::class, 'edit'])->name('package.plan.edit');
            Route::post('/plan/update/{id}', [PackagePricingController::class, 'update'])->name('package.plan.update');
            Route::post('/plan/delete', [PackagePricingController::class, 'destroy'])->name('package.plan.delete');
            Route::post('/plan/active', [PackagePricingController::class, 'activePackagePlan'])->name('package.plan.active');
            Route::post('/plan/deactivate', [PackagePricingController::class, 'deactivatePackagePlan'])->name('package.plan.deactivate');
        });
        Route::get('/consultation', [ConsultationController::class, 'index'])->name('admin.consultation');
        Route::get('/subscriber-list', [SubscriptionController::class, 'getSubscriptionUserList'])->name('admin.subscriber.list');

        Route::get('/zap-data-for-flat-price', [ZapierWebHookController::class, 'subscriptionUserHistory']);
        Route::get('/consultation-booking-for-zap-data', [ZapierWebHookController::class, 'getAllQuestionnaireStateSummeryForZapier']);
    });
});
