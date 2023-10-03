<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequestValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'sometimes|string|email',
            'phone_no' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10 |max:18',
            'first_name' => 'required|string',
        ];
    }
}
