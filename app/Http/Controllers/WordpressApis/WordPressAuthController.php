<?php

namespace App\Http\Controllers\WordpressApis;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WordPressAuthController extends Controller
{
    /**
     * signUp
     *
     * @param  mixed $request
     * @return void
     */
    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|string|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required ',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }

        $input = $request->except(['token']);
        $input['password'] = bcrypt($request->password);
        $input['is_complete_profile'] = 1;
        $add_user = User::create($input);
        $add_user->createAsStripeCustomer();

        if ($add_user->save()) {

            if (Auth::loginUsingId($add_user->id)) {

                $tokenResult = $add_user->createToken('Personal Access Token');
                $data['access_token'] = $tokenResult->accessToken;
                $data['token_type'] = 'Bearer';
                $data['user'] = User::where('id', Auth::user()->id)->first();
                return sendSuccess('Signup successfully.', $data);
            }
        }
        return sendError('There is some problem.', null);
    }

    /**
     * createUser
     *
     * @param  mixed $request
     * @return void
     */
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|string|email|unique:users,email',
        ]);
        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }
        $add_user = User::create([
            'email' => $request->email,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'is_guest' => 1,
        ]);
        $add_user->createAsStripeCustomer();
        return sendSuccess('success.', $add_user);
    }
    /**
     * updateOrCreateUser
     *
     * @param  mixed $request
     * @return void
     */
    public function updateUser(Request $request)
    {
        try {
            $add_user = User::where('email', $request->email)->first();
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10 |max:14',
                "street_address" => "required",
                "country" => "required",
                "state" => "required",
                "city" => "required",
                "zip_code" => "required",
                "primary_applicant_email" => "required|email"
            ]);
            if ($validator->fails()) {
                return sendError($validator->messages()->first(), null);
            }

            $add_user->first_name = $request->first_name ?? null;
            $add_user->last_name = $request->last_name ?? null;
            $add_user->phone_no = $request->phone_no ?? null;

            if ($add_user->save()) {

                $add_user->user_address()->updateOrCreate(['user_id' =>  $add_user->id], [
                    'street_address' => $request->street_address,
                    'country' => $request->country,
                    'state' => $request->state,
                    'city' => $request->city,
                    'zip_code' => $request->zip_code,
                ]);
                $add_user->userMeta()->updateOrCreate(['user_id' =>  $add_user->id], [
                    'email' => $request->primary_applicant_email,
                    "other_detail" => $request->other_detail ?? ""
                ]);
            }
            if ($request->petitioner_email || $request->petitioner_first_name) {
                $add_user->petitionerDetail()->updateOrCreate(['user_id' =>  $add_user->id], [
                    'email' => $request->petitioner_email ?? "",
                    "first_name" => $request->petitioner_first_name ?? "",
                    "last_name" => $request->petitioner_last_name ?? "",
                ]);
            }
            return sendSuccess('success.', $add_user);
        } catch (\Throwable $th) {
            return sendError('Failed', $th->getMessage());
        }
    }

    /**
     * userDetail
     *
     * @return void
     */
    public function userDetail(Request $request)
    {
        $add_user = User::where('email', $request->email)->with('userMeta', 'petitionerDetail', 'user_address')->first();
        return sendSuccess('success.', $add_user);
    }
}
