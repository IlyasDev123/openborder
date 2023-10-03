<?php

namespace App\Http\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserDevice;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\CommanFunctions\Constants;
use App\Models\Petitioner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;


class AuthService
{

    protected $stripe;

    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * signUp
     *
     * @param  mixed $request
     * @return void
     */

    public function signUp($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|string|email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required ',
            'first_name' => 'required|string',
            'phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10 |max:18',
            "street_address" => "required",
            "country" => "required",
            "city" => "required",
            "zip_code" => "required"
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }
        DB::beginTransaction();
        try {

            $input = $request->except(['token']);
            $input['password'] = bcrypt($request->password);
            $input['is_complete_profile'] = 1;
            $activation_code = mt_rand(1000, 9999);
            // $input['remember_token'] = $activation_code;
            $input['profile_image'] = isset($request->profile_image) ? addFile($request->profile_image, 'users/images/') : null;
            $add_user = User::updateOrCreate([
                "email" => $request->email
            ],$input);
            $name = $add_user->first_name . ' ' . $add_user->last_name ?? null;
            if(!$add_user->stripe_id){
                $add_user->createAsStripeCustomer([
                    "name" => $name,
                ]);
            }
            $add_user->remember_token = $activation_code;
            if ($add_user->save()) {

                $add_user->user_address()->updateOrCreate(['user_id' =>  $add_user->id], [
                    'street_address' => $request->street_address,
                    'country' => $request->country,
                    'state' => $request->state,
                    'city' => $request->city,
                    'zip_code' => $request->zip_code,
                    'country_name_code' => $request->country_name_code ?? "",
                ]);

                if ($request->has('petitioner_email') || $request->has('petitioner_email')) {
                    $this->addPetitionerData($add_user, $request);
                }

                if (Auth::loginUsingId($add_user->id)) {
                    // if ($add_user) {
                    //     $viewData['title'] = 'Your Email verify OTP code is :';
                    //     $viewData['activate_code'] = $activation_code;
                    //     if ($request->has('email')) {
                    //         try {
                    //             Mail::send('emails.verify-email', $viewData, function ($m) use ($request) {
                    //                 $m->from(env("MAIL_FROM_ADDRESS"), env('MAIL_FROM_NAME'));
                    //                 $m->bcc(env('MAIL_BCC_ADDRESS'));
                    //                 $m->to($request->email)
                    //                ->subject(Constants::EMAIL_SUBJECT);
                    //             });
                    //         } catch (\Exception $e) {
                    //             return sendError("OTP Failed to send . But register successfully ", null);
                    //         }
                    //     }
                    // }

                    $tokenResult = $add_user->createToken('Personal Access Token');
                    $data['access_token'] = $tokenResult->accessToken;
                    $data['token_type'] = 'Bearer';
                    $data['user'] = User::where('id', Auth::user()->id)->with('petitionerDetail')->first();
                    DB::commit();

                    return sendSuccess('Signed up successfully..', $data);
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return sendError($th->getMessage(), null);
        }
    }


    /**
     * signIn
     *
     * @param  mixed $request
     * @return void
     */

    public function signIn($request)
    {
        $messages = [
            'email.exists:user,email' => 'email does not exist!',
        ];
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|string|email |exists:users,email',
                'password' => 'required',
            ],
            [
                'email.exists' => 'No account with this e-mail was found.Please create a new account.',
            ]
        );

        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }

        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            $user = User::where('email', $request->email)->first();

            // if ($user->email_verified_at == null) {
            //     return sendError('Please verify your email',null);
            // }
            $tokenResult = $user->createToken('Personal Access Token');
            $data['access_token'] = $tokenResult->accessToken;
            $data['token_type'] = 'Bearer';
            $data['expires_at'] = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
            $data['user'] = $user;
            return sendSuccess('Login successfully.', $data);
        }

        return sendError('Email or password is incorrect.', null);
    }

    /**
     * @param mixed $request
     *
     * @return [type]
     */
    public function forgetPassword($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|string|exists:users,email',
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {

            $activation_code = mt_rand(1000, 9999);
            $user->remember_token = $activation_code;
            $user->save();
            $viewData['title'] = "Your forget password verify OTP Code is :";
            $viewData['activate_code'] = $activation_code;
            if ($request->has('email')) {
                try {
                    Mail::send('emails.otp-code', $viewData, function ($m) use ($request) {
                        $m->from(env("MAIL_FROM_ADDRESS"), env('MAIL_FROM_NAME'));
                        $m->bcc(env('MAIL_BCC_ADDRESS'));
                        $m->to($request->email)->subject(Constants::EMAIL_SUBJECT);
                    });
                    return sendSuccess('Reset mail has been sent on your email', null);
                } catch (\Exception $e) {
                    return sendError('Failed to reset password.', null);
                }
            }
        }
        return sendError('Email does not exist.', null);
    }


    /**
     * sendCode
     *
     * @param  mixed $request
     * @return void
     */

    public function sendCode($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|string|exists:users,email',
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {

            $activation_code = mt_rand(1000, 9999);
            $user->remember_token = $activation_code;
            $user->save();
            $viewData['title'] = "Your forget password verify OTP Code is :";
            $viewData['activate_code'] = $activation_code;
            $zapierData = [
                "email" => $request->email,
                "otpCode" => $activation_code,
                "subject" => "OTP CODE",

            ];
            if ($request->has('email')) {

                // $response = Http::post('https://hooks.zapier.com/hooks/catch/2967384/b04wte6/',[
                //     $zapierData
                // ]);
                $emaildata = array('to' => $request['email'], 'to_name' => $user->name);
                try {

                    Mail::send('emails.otp-code', $viewData, function ($m) use ($request) {
                        $m->from(env("MAIL_FROM_ADDRESS"), env('MAIL_FROM_NAME'));
                        $m->bcc(env('MAIL_BCC_ADDRESS'));
                        $m->to($request->email)->subject(Constants::EMAIL_SUBJECT);
                    });
                    return sendSuccess('OTP has been sent on your email.', null);
                } catch (\Exception $e) {
                    return sendError('OTP not sent on your email.', null);
                }
            }
        }
        return sendError('Email does not exist.', null);
    }


    /**
     * recoverPassword
     *
     * @param  mixed $request
     * @return void
     */

    public function recoverPassword($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required ',
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }
        $user = User::where('email', $request->email)->first();

        if ($user) {

            $user->password = bcrypt($request->password);
            $user->remember_token = null;
            $user->save();

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            $data['access_token'] = $tokenResult->accessToken;
            $data['token_type'] = 'Bearer';
            $data['expires_at'] = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
            $data['user'] = $user;
            return sendSuccess('Password updated And LoggedIn successfully!', $data);
        }
        return sendError('Token expired.', null);
    }


    /**
     * codeVerification
     *
     * @param  mixed $request
     * @return void
     */

    public function codeVerification($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required',
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }
        $user = User::where('email', $request->email)->first();

        if ($user) {

            if ($user->remember_token != $request->input('code')) {

                return sendError('OTP code is incorrect', null);
            }
            $user->remember_token = null;
            $user->email_verified_at = Carbon::now();
            $user->save();

            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            $data['access_token'] = $tokenResult->accessToken;
            $data['token_type'] = 'Bearer';
            $data['expires_at'] = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
            $data['user'] = $user;
            return sendSuccess('Your e-mail was verified.', $data);
        }
        return sendError('Token expired.', null);
    }



    /**
     * signOut
     *
     * @param  mixed $request
     * @return void
     */

    public function signOut($request)
    {
        $user = $request->user();
        if ($user) {
            if ($user->token()->revoke()) {
                return sendSuccess('Logout successfully!', null);
            } else {
                return sendError('Failed to logout', null);
            }
        }
        return sendError('User not found.', null);
    }

    /**
     * getProfile
     *
     * @param  mixed $request
     * @return void
     */

    public function getProfile()
    {

        $id = Auth::id();
        $user = User::with('user_address', 'petitionerDetail')->find($id);
        if ($user) {
            $data['user'] = $user;
            return sendSuccess('Success.', $data);
        }
        return sendError('Please try again later!', null);
    }


    /**
     * updateProfile
     *
     * @param  mixed $request
     * @return void
     */

    public function updateProfile($request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10 |max:18',
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }
        $user = Auth::user();
        if ($user) {
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->phone_no = $request->phone_no;
            $user->is_complete_profile = 1;
            $user->profile_image = isset($request->profile_image) ? addFile($request->profile_image, 'users/images/') : Auth::user()->profile_picture;
            if ($user->save()) {
                $address = $user->user_address()->updateOrCreate(['user_id' => $user->id], [
                    'street_address' => $request->street_address,
                    'country' => $request->country,
                    'state' => $request->state,
                    'city' => $request->city,
                    'zip_code' => $request->zip_code,
                    'country_name_code' => $request->country_name_code ?? "",
                ]);
                if ($request->has('petitioner_email') || $request->has('petitioner_email')) {
                    $this->addPetitionerData($user, $request);
                }
                $user = User::with('petitionerDetail')->find($user->id);
                $data['user'] = $user;
                return sendSuccess('Profile updated successfully.', $data);
            }
        }
        return sendError('Please try again later!', null);
    }



    /**
     * phoneVerify
     *
     * @param  mixed $request
     * @return void
     */
    public function phoneVerify($request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => 'required|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }
        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            $user->phone_verified_at = Carbon::now();
            $user->save();

            return sendSuccess('Phone Number was verified Successfully!', null);
        }
        return sendError('Token expire!', null);
    }

    /**
     * @return [type]
     */
    public function loginAsGustUser()
    {
        try {
            $faker = Faker::create();
            $user = User::firstOrCreate([
                'email' => $faker->unique()->safeEmail(),
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'is_guest' => 1,
            ]);

            $data['user'] = $user;
            $tokenResult = $user->createToken('Personal Access Token');
            $data['access_token'] = $tokenResult->accessToken;
            $data['token_type'] = 'Bearer';

            return sendSuccess('success.', $data);
        } catch (\Throwable $th) {
            return $th->getMessage();
            return sendError('Failed', null);
        }
    }

    /**
     * @param mixed $request
     *
     * @return [type]
     */
    public function updateGustUserData($request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'email' => 'sometimes|string|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required ',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10 |max:18',
                "street_address" => "required",
                "country" => "required",
                "city" => "required",
                "zip_code" => "required"
            ]);
            if ($validator->fails()) {
                return sendError($validator->messages()->first(), null);
            }

            $add_user = User::findOrFail($request->user_id);
            $add_user->email = $request->email ?? null;
            $add_user->first_name = $request->first_name ?? null;
            $add_user->last_name = $request->last_name ?? null;
            $add_user->phone_no = $request->phone_no ?? null;
            $add_user->is_guest = 0;
            $add_user->password = bcrypt($request->password);
            $activation_code = mt_rand(1000, 9999);
            $add_user->remember_token = $activation_code;
            $add_user->profile_image = isset($request->profile_image) ? addFile($request->profile_image, 'users/images/') : null;
            $add_user->createAsStripeCustomer();

            if ($add_user->save()) {

                $add_user->user_address()->updateOrCreate(['user_id' =>  $add_user->id], [
                    'street_address' => $request->street_address,
                    'country' => $request->country,
                    'state' => $request->state,
                    'city' => $request->city,
                    'zip_code' => $request->zip_code,
                    'country_name_code' => $request->country_name_code ?? "",
                ]);

                if ($request->has('petitioner_email') || $request->has('petitioner_email')) {
                    $this->addPetitionerData($add_user, $request);
                }

                if (Auth::loginUsingId($add_user->id)) {
                    // if ($add_user) {
                    //     $viewData['title'] = 'Your Email verify OTP code is :';
                    //     $viewData['activate_code'] = $activation_code;
                    //     if ($request->has('email')) {
                    //         try {
                    //             Mail::send('emails.verify-email', $viewData, function ($m) use ($request) {
                    //                 $m->from(env("MAIL_FROM_ADDRESS"), env('MAIL_FROM_NAME'));
                    //                 $m->bcc(env('MAIL_BCC_ADDRESS'));
                    //                 $m->to($request->email)->subject(Constants::EMAIL_SUBJECT);
                    //             });
                    //         } catch (\Exception $e) {
                    //             return sendError("OTP Failed to send . But register successfully ", null);
                    //         }
                    //     }
                    // }

                    $tokenResult = $add_user->createToken('Personal Access Token');
                    $data['access_token'] = $tokenResult->accessToken;
                    $data['token_type'] = 'Bearer';
                    $data['user'] = User::where('id', Auth::user()->id)->with('petitionerDetail')->first();
                    return sendSuccess('Signup successfully.', $data);
                }
            }
            return sendError('Please try again later!', null);
        } catch (\Throwable $th) {
            return sendError('Failed', $th->getMessage());
        }
    }

    /**
     * @param mixed $request
     *
     * @return [type]
     */
    public function setUserLanguage($request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return sendError($validator->messages()->first(), null);
            }
            $user = auth()->user();
            $user->update([
                "language" => $request->language
            ]);

            return sendSuccess('set language successfully.', $user);
        } catch (\Throwable $th) {
            return sendError('Failed', $th->getMessage());
        }
    }

    /**
     * @param mixed $user
     * @param mixed $request
     *
     * @return [type]
     */
    public function addPetitionerData($user, $request)
    {

        return  Petitioner::updateOrCreate(
            ['user_id' =>  $user->id],
            [
                'user_id' =>  $user->id,
                'first_name' => $request->petitioner_firstname,
                'last_name' => $request->petitioner_lastname,
                'email' => $request->petitioner_email,
                'is_us_citizen' => $request->is_us_citizen ? 1 : 0,
            ]
        );
    }

    /**
     * @param mixed $request
     *
     * @return [type]
     */
    public function deleteUserAccount($request)
    {
        User::find($request->user_id)->delete();
        return sendApiSuccess('Your account was deleted. You can create another one if needed.', null);
    }
}
