<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponser;

class LoginRequest extends FormRequest
{
    use ApiResponser;

    protected $stopOnFirstFailure = true;
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
            'phone_number' => 'required_without:email|exists:users,phone_number',
            'email' => 'required_without:phone_number|exists:users,email',
            'password'=>'required'
        ];
    }

    public function messages()
    {
        return[
            'phone_number.exists' => 'Phone number is not registered.',
            'phone_number.required_without' => 'Phone number or email is required.',
            'email.exists' => 'User is not registered.',
            'email.required_without' => 'Phone number or email is required.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->errorResponse($validator->errors()->first(),422)
        );
        parent::failedValidation($validator);
    }
}
