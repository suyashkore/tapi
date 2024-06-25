<?php

namespace App\Feature\Tenant\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TenantKycUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Authorization logic can be added here if needed
        // For now, we'll just return true to allow all requests
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
            'tenant_id' => 'sometimes|exists:tenants,id',
            'gst_num' => 'sometimes|string|max:16',
            'cin_num' => 'sometimes|string|max:24',
            'pan_num' => 'sometimes|string|max:16',
            'bank_name' => 'sometimes|string|max:32',
            'bank_account_num' => 'sometimes|string|max:24',
            'bank_ifsc_code' => 'sometimes|string|max:16',
            'owner_aadhaar' => 'sometimes|string|max:16',
            'owner_pan' => 'sometimes|string|max:16',
            'owner_photo_url' => 'sometimes|string|url|max:255',
            'owner_email' => 'sometimes|email|max:64',
            'owner_mobile' => 'sometimes|string|max:16',
            'finance_head_email' => 'sometimes|email|max:64',
            'finance_head_mobile' => 'sometimes|string|max:16',
            'kyc_completed' => 'sometimes|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'gst_num.max' => 'The GST number must not exceed 16 characters.',
            'cin_num.max' => 'The CIN number must not exceed 24 characters.',
            'pan_num.max' => 'The PAN number must not exceed 16 characters.',
            'bank_name.required' => 'The bank name is required.',
            'bank_name.max' => 'The bank name must not exceed 32 characters.',
            'bank_account_num.required' => 'The bank account number is required.',
            'bank_account_num.max' => 'The bank account number must not exceed 24 characters.',
            'bank_ifsc_code.required' => 'The bank IFSC code is required.',
            'bank_ifsc_code.max' => 'The bank IFSC code must not exceed 16 characters.',
            'owner_aadhaar.max' => 'The owner Aadhaar number must not exceed 16 characters.',
            'owner_pan.max' => 'The owner PAN number must not exceed 16 characters.',
            'owner_photo_url.url' => 'The owner photo URL must be a valid URL.',
            'owner_photo_url.max' => 'The owner photo URL must not exceed 255 characters.',
            'owner_email.email' => 'The owner email must be a valid email address.',
            'owner_email.max' => 'The owner email must not exceed 64 characters.',
            'owner_mobile.max' => 'The owner mobile number must not exceed 16 characters.',
            'finance_head_email.email' => 'The finance head email must be a valid email address.',
            'finance_head_email.max' => 'The finance head email must not exceed 64 characters.',
            'finance_head_mobile.max' => 'The finance head mobile number must not exceed 16 characters.',
            'kyc_completed.boolean' => 'The KYC completed field must be true or false.',
        ];
    }
}
