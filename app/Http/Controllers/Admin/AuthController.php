<?php

namespace App\Http\Controllers\Admin;


use App\Models\Admin;
use Illuminate\Http\Request;
use App\CommanFunctions\Constants;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Admin\BaseAdminController;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function home() {

        if(Auth::guard('admin')->check()){
			return redirect()->route('admin.dashboard');
        }

		return view('admin.login');
	}

	public function myProfile() {

        $id	= Auth::guard('admin')->user()->id;
        $user = Admin::find($id);
        return view('admin.users.user_profile', compact('user'));
    }

    public function save_profile(Request $request) {

        $id = Auth::guard('admin')->user()->id;

        $validator = Validator::make($request->all(), [
            'name'	=> 'required|string',
            'email'	=> 'required|email|unique:admins,email,'.$id
        ]);

        if ($validator->fails()) {
            return \redirect()->back()->with('invalid', $validator->getMessageBag()->getMessages());
        }

        $user = Admin::find($id);

        $user->name 	= $request->name;
        $user->email 	= $request->email;

        if($user->save()) {

            if ($request->hasFile('upload')) {

                if ($user->profile_image && Storage::exists($user->profile_image)) {
                    Storage::delete($user->profile_image);
                }

                $fileName		= Storage::put(Constants::DEFAULT_IMAGE_PROFILE, $request->file('upload'));
                $user->profile_image 	= $fileName;
                $user->update();
            }

            return \redirect()->back()->with('success', 'Profile edit successfully.');
        } else {

            return \redirect()->back()->with('error', 'Profile cannot be edit.');
        }
    }

    public function change_password(Request $request) {

        $validator = Validator::make($request->all(), [
            'new_password'		=> 'required|between:6,255|required_with:confirm_password|same:confirm_password',
            'confirm_password'	=> 'required'
        ]);

        if ($validator->fails()) {
            return \redirect()->back()->with('invalid', $validator->getMessageBag()->getMessages());
        }

        $user = Admin::find(Auth::guard('admin')->user()->id);

        $user->password = bcrypt($request->new_password);

        if($user->save()) {
            $this->logout($request,true);
            Session::flash('success', 'Password changed, Please login with new password.');
            return Redirect::to(route('login'));
        } else {
            return \redirect()->back()->with('danger', 'Password cannot be changed.');
        }
    }

	public function login(Request $request) {

		if(Auth::guard('admin')->check())
			return redirect()->route('admin.dashboard');

		$this->validate($request, [
			'email'		=> 'required|email',
			'password'	=> 'required',
		]);

		$auth = Auth::guard('admin');

		if ($auth->attempt(['email'		=> $request->email,	'password'	=> $request->password])) {

            $Admin 	= Auth::guard('admin')->user();
            return redirect()->route('admin.dashboard');

		} else {

			Session::flash('error', 'Invalid email or password.');
			return Redirect::to(route('login'));
		}
	}

	public function logout(Request $request, $message = false) {

		Auth::guard('admin')->logout();
		if ($message == true){
            Session::flash('success', 'Password changed, Please login with new password.');
            return Redirect::to(route('admin.login'));
        }
		return redirect('/');
	}

    public function signIn(Request $request)
    {
        $messages = [
            'email.exists:user,email' => 'email doesnot exist!',
        ];
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|string|email|exists:admins,email',
                'password' => 'required',
            ],
            [
                'email.exists' => 'No account with this e-mail was found.
                Please create a new account.',
            ]
        );

        if ($validator->fails()) {
            return sendError($validator->messages()->first(), null);
        }
        // $auth = Auth::guard('api_admin');
        // dd(\auth('api_admin')->attempt());
        if (Auth::guard('api_admin')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            $user = Admin::where('email', $request->email)->first();
            // dd($user);
            // if ($user->email_verified_at == null) {
            //     return sendError('Please verify your email',null);
            // }
            $user = Auth::guard('api_admin')->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $data['access_token'] = $tokenResult->accessToken;
            $data['token_type'] = 'Bearer';
            $data['expires_at'] = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
            $data['user'] = $user;
            return sendSuccess('Login successfully.', $data);
        }

        return sendError('Email or password is incorrect.', null);
    }

}
