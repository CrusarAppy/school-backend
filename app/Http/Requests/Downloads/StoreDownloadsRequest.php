<?php

namespace App\Http\Requests\Downloads;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponser;

class StoreDownloadsRequest extends FormRequest
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
            'english.title' => 'required',
            'nepali.title' => 'required',
            'file' => 'required|mimes:png,jpg,jpeg,gif,pdf,doc,docx'
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
