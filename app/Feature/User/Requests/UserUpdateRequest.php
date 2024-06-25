<?php

namespace App\Feature\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UserUpdateRequest
 *
 * Handles validation for updating an existing user.
 *
 * @package App\Feature\User\Requests
 */
class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Authorization logic can be added here if needed
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Extract user context from request attributes
        $userContext = $this->attributes->get('userContext');

        // Merge tenant_id from userContext into the request data
        if ($userContext) {
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
        $userId = $this->route('id'); // Get the user ID from the route parameter

        return [
            'tenant_id' => 'nullable|exists:tenants,id',
            'name' => 'sometimes|string|max:48',
            'login_id' => [
                'sometimes',
                'string',
                'max:24',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($userId)
            ],
            'mobile' => [
                'sometimes',
                'string',
                'max:16',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($userId)
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:64',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($userId)
            ],
            'email2' => 'nullable|string|email|max:64',
            'google_id' => [
                'nullable',
                'string',
                'max:32',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($userId)
            ],
            'password' => 'sometimes|required|string|min:8',
            'profile_pic_url' => 'nullable|string|max:255',
            'user_type' => 'sometimes|string|in:SYSTEM,TENANT|max:16',
            'role_id' => 'nullable|exists:roles,id',
            'sso_id' => [
                'nullable',
                'string',
                'max:32',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($userId)
            ],
            'sso_ref' => [
                'nullable',
                'string',
                'max:32',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($userId)
            ],
            'job_title' => 'nullable|string|max:32',
            'department' => 'nullable|string|max:32',
            'aadhaar' => [
                'nullable',
                'string',
                'max:16',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($userId)
            ],
            'pan' => [
                'nullable',
                'string',
                'max:16',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($userId)
            ],
            'epf_uan' => [
                'nullable',
                'string',
                'max:16',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($userId)
            ],
            'epf_num' => [
                'nullable',
                'string',
                'max:16',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($userId)
            ],
            'esic' => [
                'nullable',
                'string',
                'max:32',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($userId)
            ],
            'last_login' => 'nullable|date',
            'last_password_reset' => 'nullable|date',
            'failed_login_attempts' => 'nullable|integer|min:0',
            'active' => 'boolean',
            'remarks' => 'nullable|string|max:255'
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
            'tenant_id.exists' => 'The selected tenant does not exist.',
            'role_id.exists' => 'The selected role does not exist.',
            'sso_id.unique' => 'The SSO ID has already been taken for this tenant.',
            'sso_ref.unique' => 'The SSO reference has already been taken for this tenant.',
            'aadhaar.unique' => 'The Aadhaar number has already been taken for this tenant.',
            'pan.unique' => 'The PAN number has already been taken for this tenant.',
            'epf_uan.unique' => 'The EPF UAN has already been taken for this tenant.',
            'epf_num.unique' => 'The EPF number has already been taken for this tenant.',
            'esic.unique' => 'The ESIC number has already been taken for this tenant.',
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 48 characters.',
            'login_id.required' => 'The login ID field is required.',
            'login_id.string' => 'The login ID must be a string.',
            'login_id.max' => 'The login ID may not be greater than 24 characters.',
            'mobile.required' => 'The mobile field is required.',
            'mobile.string' => 'The mobile must be a string.',
            'mobile.max' => 'The mobile may not be greater than 16 characters.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 64 characters.',
            'email2.email' => 'The secondary email must be a valid email address.',
            'email2.max' => 'The secondary email may not be greater than 64 characters.',
            'google_id.string' => 'The Google ID must be a string.',
            'google_id.max' => 'The Google ID may not be greater than 32 characters.',
            'password_hash.required' => 'The password hash field is required.',
            'password_hash.string' => 'The password hash must be a string.',
            'profile_pic_url.url' => 'The profile picture URL must be a valid URL.',
            'profile_pic_url.max' => 'The profile picture URL may not be greater than 255 characters.',
            'user_type.required' => 'The user type field is required.',
            'user_type.string' => 'The user type must be a string.',
            'user_type.in' => 'The user type must be either SYSTEM or TENANT.',
            'sso_id.string' => 'The SSO ID must be a string.',
            'sso_id.max' => 'The SSO ID may not be greater than 32 characters.',
            'sso_ref.string' => 'The SSO reference must be a string.',
            'sso_ref.max' => 'The SSO reference may not be greater than 32 characters.',
            'job_title.string' => 'The job title must be a string.',
            'job_title.max' => 'The job title may not be greater than 32 characters.',
            'department.string' => 'The department must be a string.',
            'department.max' => 'The department may not be greater than 32 characters.',
            'aadhaar.string' => 'The Aadhaar number must be a string.',
            'aadhaar.max' => 'The Aadhaar number may not be greater than 16 characters.',
            'pan.string' => 'The PAN number must be a string.',
            'pan.max' => 'The PAN number may not be greater than 16 characters.',
            'epf_uan.string' => 'The EPF UAN must be a string.',
            'epf_uan.max' => 'The EPF UAN may not be greater than 16 characters.',
            'epf_num.string' => 'The EPF number must be a string.',
            'epf_num.max' => 'The EPF number may not be greater than 16 characters.',
            'esic.string' => 'The ESIC number must be a string.',
            'esic.max' => 'The ESIC number may not be greater than 32 characters.',
            'last_login.date' => 'The last login must be a valid date.',
            'last_password_reset.date' => 'The last password reset must be a valid date.',
            'failed_login_attempts.integer' => 'The failed login attempts must be an integer.',
            'active.boolean' => 'The active field must be true or false.',
            'remarks.string' => 'The remarks must be a string.',
            'remarks.max' => 'The remarks may not be greater than 255 characters.',
        ];
    }
}
