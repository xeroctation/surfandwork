<?php

namespace App\Http\Requests;

use App\Http\Controllers\LoginController;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserLoginRequest extends FormRequest
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
            'email' => 'required',
            'password'=> 'required'
        ];
    }


    public function messages()
    {
        return [
                'name.required' => __('login.name.required'),
                'name.unique' => __('login.name.unique'),
                'phone_number.required' => __('login.phone_number.required'),
                'phone_number.regex' => __('login.phone_number.regex'),
                'phone_number.unique' => __('Этот номер зарегистрирован'),
                'email.required' => __('login.email.required'),
                'email.email' => __('login.email.email'),
                'email.unique' => __('login.email.unique'),
                'password.required' => __('login.password.required'),
                'password.min' => __('login.password.min'),
                'password.confirmed' => __('login.password.confirmed'),
            ];
    }
}
