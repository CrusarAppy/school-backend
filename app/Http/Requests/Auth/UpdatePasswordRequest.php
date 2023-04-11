<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponser;

class UpdatePasswordRequest extends FormRequest
{
    use ApiResponser;

    protected $stopOnFirstFailure = true;

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
            'old_password' => 'required|string',
            'password' => 'required|string|min:8'
        ];
    }

    public function messages()
    {
        return[
            'password.required' => "Password is required",
            'old_password.required' => "Password is required",
            'paswword.min' => "Password must be atleast 8 characters"
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
