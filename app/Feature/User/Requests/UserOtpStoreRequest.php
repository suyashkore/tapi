<?php

namespace App\Feature\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UserOtpStoreRequest
 *
 * Handles validation for storing a new userOtp.
 *
 * @package App\Feature\User\Requests
 */
class UserOtpStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Add your authorization logic here if needed
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
            'user_id' => 'required|exists:users,id',
            'otp_hash' => 'required|string|max:255|unique:user_otps,otp_hash,NULL,user_id,user_id,' . $this->user_id,
            'expires_at' => 'nullable|date',
            'failed_otp_login_attempts' => 'nullable|integer|min:0',
            'otp_login_blocked_till' => 'nullable|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required' => 'The user ID field is required.',
            'user_id.exists' => 'The selected user ID is invalid.',
            'otp_hash.required' => 'The OTP hash field is required.',
            'otp_hash.string' => 'The OTP hash must be a string.',
            'otp_hash.max' => 'The OTP hash may not be greater than 255 characters.',
            'otp_hash.unique' => 'The OTP hash has already been taken for this user.',
            'expires_at.date' => 'The expires at must be a valid date.',
            'failed_otp_login_attempts.integer' => 'The failed OTP login attempts must be an integer.',
            'failed_otp_login_attempts.min' => 'The failed OTP login attempts must be at least 0.',
            'otp_login_blocked_till.date' => 'The OTP login blocked till must be a valid date.',
        ];
    }
}
