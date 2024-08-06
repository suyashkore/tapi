<?php

namespace App\Feature\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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
        if (!$this->input('tenant_id')) {
            // Extract user context from request attributes
            $userContext = $this->attributes->get('userContext');

            // Merge tenant_id from userContext into the request data if it's not provided
            if ($userContext && !$this->input('tenant_id')) {
                $this->merge([
                    'tenant_id' => $userContext->tenantId,
                ]);
            }
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
            'login_id' => ['required', 'exists:users,login_id'],
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
        Log::error('Validation failed for admin reset password request data in AdminResetPasswordRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
