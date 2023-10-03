<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('user_address', 'questionnaireStatesSummary','petitionerDetail')->where('is_guest', 0)->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::with('user_address')->find($id);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            // 'first_name' => 'required|string',
            // 'last_name' => 'required|string',
            // 'phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10 |max:18',
            // "street_address" => "required",
            // "country" => "required",
            // "city" => "required",
            // "zip_code" => "required"
        ]);
        if ($validator->fails()) {
            return \redirect()->back()->with('invalid', $validator->getMessageBag()->getMessages());
        }

        $user = User::find($request->user_id);
        $input = $request->except('_token');
        $user->update($input);

        Address::updateOrCreate(
            ['user_id' => $request->user_id],
            $input
        );


        return redirect()->route('user.index')
            ->with('success', 'Record updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = User::find($request->id);
        $user->delete();
        return sendApiSuccess('User Deleted successfully', null);

    }

    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required ',
        ]);
        if ($validator->fails()) {

            return sendApiError($validator->messages(), null);
        }
        $user = User::find($request->user_id);

        if ($user) {

            $user->password = bcrypt($request->password);
            $user->save();
            $password['password'] = $request->password;
            if ($request->has('email')) {
                try {
                    Mail::send('emails.change-password', $password, function ($m) use ($request) {
                        $m->from(env("MAIL_FROM_ADDRESS"), env('MAIL_FROM_NAME'));
                        $m->to($request->email)->subject('Reset Your Password.');
                    });
                    return sendApiSuccess('Password reset successfully', null);
                } catch (\Exception $e) {
                    return sendApiError($e->messages(), null);
                }
            }

            return sendApiSuccess('Password reset successfully', null);
        }
        return sendApiError("Something went wrong", null);
    }
}
