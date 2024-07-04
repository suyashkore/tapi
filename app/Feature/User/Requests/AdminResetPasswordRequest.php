<?php

namespace App\Feature\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class AdminResetPasswordRequest
 *
 * Handles validation for an admin resetting a user's password.
 *
 * @package App\Feature\User\Requests
 */
class AdminResetPasswordRequest extends FormRequest
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Extract user context from request attributes
        $userContext = $this->attributes->get('userContext');

        // Merge tenant_id from userContext into the request data if it's not provided
        if ($userContext && !$this->input('tenant_id')) {
            $this->merge([
                'tenant_id' => $userContext->tenantId,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        Log::debug('Validating admin reset password request data in AdminResetPasswordRequest');

        return [
            'tenant_id' => 'required|exists:tenants,id',
            'user_id' => 'required|exists:users,id',
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
        Log::error('Validation failed for admin reset password request data in AdminResetPasswordRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
