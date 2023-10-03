<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Address;
use App\Models\Petitioner;

class UserService
{

    protected $stripe;

    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    /**
     * @param mixed $request
     *
     * @return [type]
     */
    public function createUser($request)
    {
        $user = User::where('id', $request->user_id)->orWhere('email', $request->email)->first();
        if ($user) {

            $name = $user->first_name . ' ' . $user->last_name ?? null;
            $user->update($this->requestData($request));
            $user = $user->refresh();
           $user->stripe_id ? $user->stripe_id : $user->createAsStripeCustomer([
                "name" => $name,
            ]);


            if ($request->has('petitioner_email') || $request->has('petitioner_email')) {
                $this->addPetitionerData($user, $request);
            }
            if ($request->has('street_address') || $request->has('country')) {
                $this->addAddress($user, $request);
            }
            $user->save();
            // $user['access_token'] = $this->getToken($request, $user);
            return $user;
        } else {
            $user = User::Create($this->requestData($request));
            $name = $user->first_name . ' ' . $user->last_name ?? null;
            $user->createAsStripeCustomer([
                "name" => $name,
            ]);
            $user->save();
            if ($request->has('petitioner_email') || $request->has('petitioner_firstname')) {
                $this->addPetitionerData($user, $request);
            }
            if ($request->has('street_address') || $request->has('country')) {
                $this->addAddress($user, $request);
            }
            return $user;
        }
    }

    public function requestData($request)
    {

        return   [
            "email" => $request->email,
            "first_name" => $request->first_name ?? null,
            "last_name" => $request->last_name ?? null,
            'password' => bcrypt($request->password) ?? bcrypt('pass123'),
            'phone_no' => $request->phone_no??null,
            'is_guest' => isset($request->password) ? 0 : 1
        ];
    }

    public function getToken($request, $user)
    {
        if ($request->filled($request->password)) {
            $tokenResult = $user->createToken('Personal Access Token');
            return   $tokenResult->accessToken;
            $user['token_type'] = 'Bearer';
        }
    }


    public function addPetitionerData($user, $request)
    {

        return  Petitioner::updateOrCreate(
            [
                'user_id' =>  $user->id,
            ],
            [
                'user_id' =>  $user->id,
                'first_name' => $request->petitioner_firstname,
                'last_name' => $request->petitioner_lastname,
                'email' => $request->petitioner_email,
                'is_us_citizen' => $request->petitioner_email ? 1 : 0,
            ]
        );
    }

    public function addAddress($user, $request)
    {
        return  Address::updateOrCreate(
            [
                'user_id' =>  $user->id,
            ],
            [
                'user_id' =>  $user->id,
                'street_address' => $request->street_address,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'zip_code' => $request->zip_code,
                'country_name_code' => $request->country_name_code ?? null,
            ]
        );
    }
}
