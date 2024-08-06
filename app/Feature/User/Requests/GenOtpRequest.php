<?php

namespace App\Feature\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class GenOtpRequest
 *
 * Handles validation for Generating OTP.
 *
 * @package App\Feature\User\Requests
 */
class GenOtpRequest extends FormRequest
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
        Log::debug('Validating generate otp request data in GenOtpRequest');

        return [
            'tenant_id' => 'required|exists:tenants,id',
            'login_id' => 'required|exists:users,id'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        Log::error('Validation failed for reset password request data in ResetPasswordWithOtpRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
