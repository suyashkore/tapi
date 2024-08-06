<?php

namespace App\Feature\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class ResetPasswordWithOtpRequest
 *
 * Handles validation for resetting a user's password using an OTP.
 *
 * @package App\Feature\User\Requests
 */
class ResetPasswordWithOtpRequest extends FormRequest
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
        Log::debug('Validating reset password request data in ResetPasswordWithOtpRequest');

        return [
            'tenant_id' => 'required|exists:users,tenant_id',
            'login_id' => 'required|exists:users,login_id',
            'otp' => 'required|string|min:6|max:6', // Assuming OTP is a 6-digit code
            'new_password' => 'required|string|min:8|confirmed',
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
