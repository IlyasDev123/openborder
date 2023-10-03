<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Services\AuthService;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{

    /**
     * signUp
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function signUp(Request $request, AuthService $authService)
    {
        $sign_up = $authService->signUp($request);
        if ($sign_up['status'] == false) {
            return sendApiError($sign_up['message'], null);
        }
        return sendApiSuccess($sign_up['message'], $sign_up['data'], null);
    }


    /**
     * signIn
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function signIn(Request $request, AuthService $authService)
    {
        $sign_in = $authService->signIn($request);

        if ($sign_in['status'] == false) {
            return sendApiError($sign_in['message'], null);
        }
        return sendApiSuccess($sign_in['message'], $sign_in['data'], null);
    }

    /**
     * forgetPassword
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function forgetPassword(Request $request, AuthService $authService)
    {
        $forget_password = $authService->forgetPassword($request);

        if ($forget_password['status'] == false) {
            return sendApiError($forget_password['message'], null);
        }
        return sendApiSuccess($forget_password['message'], $forget_password['data'], null);
    }

    /**
     * sendCode
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function sendCode(Request $request, AuthService $authService)
    {
        $send_code = $authService->sendCode($request);
        if ($send_code['status'] == false) {
            return sendApiError($send_code['message'], null);
        }
        return sendApiSuccess($send_code['message'], $send_code['data'], null);
    }

    /**
     * recoverPassword
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function recoverPassword(Request $request, AuthService $authService)
    {
        $recover_password = $authService->recoverPassword($request);
        if ($recover_password['status'] == false) {
            return sendApiError($recover_password['message'], null);
        }
        return sendApiSuccess($recover_password['message'], $recover_password['data'], null);
    }

    /**
     * emailVerify
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function emailVerify(Request $request, AuthService $authService)
    {

        $email_verify = $authService->codeVerification($request);
        if ($email_verify['status'] == false) {
            return sendApiError($email_verify['message'], null);
        }
        return sendApiSuccess($email_verify['message'], $email_verify['data'], null);
    }

    /**
     * emailVerify
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function verifyCode(Request $request, AuthService $authService)
    {

        $email_verify = $authService->codeVerification($request);
        if ($email_verify['status'] == false) {
            return sendApiError($email_verify['message'], null);
        }
        return sendApiSuccess($email_verify['message'], $email_verify['data'], null);
    }

    /**
     * phoneVerify
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function phoneVerify(Request $request, AuthService $authService)
    {
        $phone_verify = $authService->phoneVerify($request);
        if ($phone_verify['status'] == false) {
            return sendApiError($phone_verify['message'], null);
        }
        return sendApiSuccess($phone_verify['message'], $phone_verify['data'], null);
    }

    /**
     * signOut
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function signOut(Request $request, AuthService $authService)
    {

        $sign_out = $authService->signOut($request);
        if ($sign_out['status'] == false) {
            return sendApiError($sign_out['message'], null);
        }
        return sendApiSuccess($sign_out['message'], $sign_out['data'], null);
    }

    /**
     * getProfile
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function getProfile(AuthService $authService)
    {
        $get_profile = $authService->getProfile();
        if ($get_profile['status'] == false) {
            return sendApiError($get_profile['message'], null);
        }
        return sendApiSuccess($get_profile['message'], $get_profile['data'], null);
    }

    /**
     * updateProfile
     *
     * @param  mixed $request
     * @param  mixed $authService
     * @return void
     */
    public function updateProfile(Request $request, AuthService $authService)
    {

        $update_profile = $authService->updateProfile($request);
        if ($update_profile['status'] == false) {
            return sendApiError($update_profile['message'], null);
        }
        return sendApiSuccess($update_profile['message'], $update_profile['data'], null);
    }

    public function loginAsGustUser(AuthService $authService)
    {
        return  $authService->loginAsGustUser();
    }

    public function updateGustUserData(Request $request, AuthService $authService)
    {
        return  $authService->updateGustUserData($request);
    }

    public function setUserLanguage(Request $request, AuthService $authService)
    {
        return  $authService->setUserLanguage($request);
    }

    public function deleteUserAccount(Request $request, AuthService $authService)
    {
        return  $authService->deleteUserAccount($request);
    }


    public function deleteGuestUser()
    {
       $user = User::where('email', 'like', '%' . '@example'. '%')->where('is_guest', 1)->doesntHave('questionnaireStatesSummary')
       ->doesntHave('usersBookedConsultation')->doesntHave('userBySubscriptions')->get();
       return $user;
    }
}
