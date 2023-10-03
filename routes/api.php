<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ReportBugController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\ZapierWebHookController;
use App\Http\Controllers\Api\PackagePlanController;
use App\Http\Controllers\ZoomIntegrationController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Admin\PackagePricingController;
use App\Http\Controllers\Api\AcuitySchedulingController;
use App\Http\Controllers\Api\QuestionnaireStateController;
use App\Http\Controllers\WordpressApis\CheckoutController;
use App\Http\Controllers\WordpressApis\WordPressAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::get('/product-shifting', [PackagePricingController::class, 'addProductOntoLive']);

Route::get('/test-email', [ReportBugController::class, 'sendTextEmail']);
Route::post('/register-by-web', [WordPressAuthController::class, 'signUp']);
Route::post('/create-user-by-web', [WordPressAuthController::class, 'createUser']);
Route::post('/update-user-by-web', [WordPressAuthController::class, 'updateUser']);
Route::post('/get-user-by-web', [WordPressAuthController::class, 'userDetail']);
Route::post('/add-to-card', [CheckoutController::class, 'addToCart']);

Route::post('/login-as-gust', [AuthController::class, 'loginAsGustUser']);
Route::post('/update-guest-user', [AuthController::class, 'updateGustUserData']);
Route::post('/admin-login', [App\Http\Controllers\Admin\AuthController::class, 'signIn']);
Route::post('/availability/dates', [AcuitySchedulingController::class, 'getAcuityDate']);
Route::post('/availability/times', [AcuitySchedulingController::class, 'getAcuityTime']);
Route::get('/appointment-types', [AcuitySchedulingController::class, 'getAcuityAppointmentType']);
Route::get('/calendars', [AcuitySchedulingController::class, 'getAcuityCalenders']);
Route::post('/availability/date-time', [AcuitySchedulingController::class, 'getAcuityDateTime']);
Route::get('/acuity/forms', [AcuitySchedulingController::class, 'acuityForm']);
Route::post('/get-appointment-by-id', [AcuitySchedulingController::class, 'getAppointmentById']);
Route::get('/get-appointments', [AcuitySchedulingController::class, 'getAppointmentAcuity']);

Route::post('/sign-up', [AuthController::class, 'signUp']);
Route::post('/sign-in', [AuthController::class, 'signIn']);
Route::post('/forget-password', [AuthController::class, 'forgetPassword']);
Route::post('/recover-password', [AuthController::class, 'recoverPassword']);
Route::post('/send-otp', [AuthController::class, 'sendCode']);
Route::post('/email-verify', [AuthController::class, 'emailVerify']);
Route::post('/verify-otp', [AuthController::class, 'verifyCode']);
Route::post('/phone-verify', [AuthController::class, 'phoneVerify']);
Route::get('/booking-detail', [ConsultationController::class, 'getConsultationBooking']);

Route::group(['middleware' => ['auth:api']], function () {

    Route::post('/sign-out', [AuthController::class, 'signOut']);
    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/delete-account', [AuthController::class, 'deleteUserAccount']);
    Route::get('/get-profile', [AuthController::class, 'getProfile']);
    Route::post('/update-language', [AuthController::class, 'setUserLanguage']);
    Route::post('/report-bugs', [ReportBugController::class, 'storeUserBugsReport']);


    Route::prefix('questionnaire-state')->group(function () {
        Route::post('/store', [QuestionnaireStateController::class, 'StoreQuestionnaireState']);
        Route::post('/show', [QuestionnaireStateController::class, 'getQuestionnaireState']);
        Route::post('/delete', [QuestionnaireStateController::class, 'deleteQuestionnaireState']);
        Route::post('/send-email', [QuestionnaireStateController::class, 'getQuestionnaireStateEmail']);
    });

    Route::prefix('payments')->group(function () {
        Route::post('/add-card', [PaymentController::class, 'addCard']);
        Route::post('/single-payment', [PaymentController::class, 'payment']);
        Route::get('/packages/list', [PlanController::class, 'index']);
        Route::post('/packages/plan', [PlanController::class, 'packagePlan']);
        Route::post('/package/plan', [PlanController::class, 'getPackagePlanById']);
        // Route::post('/user-subscription',[PaymentController::class,'subscription']);
        Route::post('/subscription', [PaymentController::class, 'paymentSubscription']);
    });

    Route::prefix('consultation')->group(function () {
        Route::get('/fees', [ConsultationController::class, 'getConsultation']);
        Route::post('/booking', [ConsultationController::class, 'bookConsultation']);
    });

    Route::get('/timezone-list', [AcuitySchedulingController::class, 'getTimeZone']);
    Route::get('/user/buy-plan-history', [PackagePlanController::class, 'userPurchasePlans']);

    /******************************************** API Versioning V1 ***************************************************/

    Route::prefix('payments')->group(function () {
        Route::post('/v1/add-card', [App\Http\Controllers\Api\v1\PaymentController::class, 'addCard']);
        Route::post('/v1/single-payment', [App\Http\Controllers\Api\v1\PaymentController::class, 'payment']);
        Route::post('/v1/subscription', [App\Http\Controllers\Api\v1\PaymentController::class, 'paymentSubscription']);
    });

    Route::prefix('consultation')->group(function () {
        Route::post('/v1/booking', [App\Http\Controllers\Api\v1\ConsultationController::class, 'bookConsultation']);
    });

});



// this api are using for only  wordpress web site ...

Route::get('/packages/list', [PlanController::class, 'index']);
Route::get('/packages/plan', [PlanController::class, 'packagePlan']);
Route::get('/package/plan/{id}', [PlanController::class, 'getPackagePlanByIdForWeb']);
Route::get('/package/plan-list/{id}', [PlanController::class, 'getPlanListByPackageId']);
Route::post('/user-subscription', [CheckoutController::class, 'paymentSubscription']);
Route::get('/zap-data/{id}', [QuestionnaireStateController::class, 'getQuestionnaireStateSummeryForZapier']);
Route::get('/zap-data-for-flat-price', [ZapierWebHookController::class, 'subscriptionUserHistory']);
Route::get('/consultation-booking-for-zap-data', [ZapierWebHookController::class, 'getAllQuestionnaireStateSummeryForZapier']);
Route::post('/zoom-meeting', [ZoomIntegrationController::class, 'createZoomMeeting']);

Route::post('/subscription', [PaymentController::class, 'serviceSubscription']);
Route::post('/booking', [ConsultationController::class, 'bookConsultationGuestuser']);
Route::get('/delete-guest-user', [AuthController::class, 'deleteGuestUser']);

/******************************************** API Versioning V1 ***************************************************/
Route::post('/v1/subscription', [App\Http\Controllers\Api\v1\PaymentController::class, 'serviceSubscription']);
Route::post('/v1/booking', [App\Http\Controllers\Api\v1\ConsultationController::class, 'bookConsultationGuestuser']);
Route::post('/v1/update-card', [App\Http\Controllers\Api\v1\PaymentController::class, 'updateCard']);
Route::post('/token', [App\Http\Controllers\Api\v1\PaymentController::class, 'stripeConfigration']);
Route::post('/v1/get-user', [App\Http\Controllers\Api\v1\PaymentController::class, 'getUser']);