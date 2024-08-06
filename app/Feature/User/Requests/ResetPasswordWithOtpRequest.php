<?php

namespace App\Feature\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $tenantId = $this->input('tenant_id');
            $loginId = $this->input('login_id');

            // Check if the combination of tenant_id and login_id exists
            $exists = DB::table('users')
                ->where('tenant_id', $tenantId)
                ->where('login_id', $loginId)
                ->exists();

            if (!$exists) {
                $validator->errors()->add('login_id', 'The specified tenant_id and login_id combination does not exist.');
            }
        });
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
