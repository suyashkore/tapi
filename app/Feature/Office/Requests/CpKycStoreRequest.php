<?php

namespace App\Feature\Office\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Class CpKycStoreRequest
 *
 * Handles validation for storing a new CP KYC.
 *
 * @package App\Feature\Office\Requests
 */
class CpKycStoreRequest extends FormRequest
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
        return [
            'tenant_id' => 'nullable|exists:tenants,id',
            'company_tag' => 'nullable|string|max:16|exists:companies,code',
            'legal_name' => 'required|string|max:128',
            'owner1_name' => 'required|string|max:48',
            'photo1_url' => 'nullable|string|max:255',
            'owner1_aadhaar' => 'nullable|string|max:16',
            'owner1_aadhaar_url' => 'nullable|string|max:255',
            'owner1_pan' => 'nullable|string|max:16',
            'owner1_pan_url' => 'nullable|string|max:255',
            'owner1_email' => 'nullable|string|email|max:64',
            'owner1_mobile' => 'nullable|string|max:16',
            'owner2_name' => 'nullable|string|max:48',
            'photo2_url' => 'nullable|string|max:255',
            'owner2_aadhaar' => 'nullable|string|max:16',
            'owner2_aadhaar_url' => 'nullable|string|max:255',
            'owner2_pan' => 'nullable|string|max:16',
            'owner2_pan_url' => 'nullable|string|max:255',
            'owner2_email' => 'nullable|string|email|max:64',
            'owner2_mobile' => 'nullable|string|max:16',
            'country' => 'nullable|string|max:64',
            'state' => 'nullable|string|max:64',
            'district' => 'nullable|string|max:64',
            'taluka' => 'nullable|string|max:64',
            'city' => 'nullable|string|max:64',
            'pincode' => 'required|string|max:16',
            'latitude' => 'nullable|string|max:16',
            'longitude' => 'nullable|string|max:16',
            'address' => 'required|string|max:255',
            'address_reg' => 'nullable|string|max:512',
            'addr_doc_url' => 'nullable|string|max:255',
            'gst_num' => 'nullable|string|max:16',
            'gst_cert_url' => 'nullable|string|max:255',
            'cin_num' => 'nullable|string|max:24',
            'company_reg_cert_url' => 'nullable|string|max:255',
            'pan_num' => 'nullable|string|max:16',
            'pan_card_url' => 'nullable|string|max:255',
            'tan_num' => 'nullable|string|max:16',
            'tan_card_url' => 'nullable|string|max:255',
            'msme_num' => 'nullable|string|max:24',
            'msme_reg_cert_url' => 'nullable|string|max:255',
            'aadhaar_num' => 'nullable|string|max:16',
            'aadhaar_card_url' => 'nullable|string|max:255',
            'bank1_name' => 'nullable|string|max:32',
            'bank1_accnt_holder' => 'nullable|string|max:32',
            'bank1_account_type' => 'nullable|string|max:24',
            'bank1_account_num' => 'nullable|string|max:24',
            'bank1_ifsc_code' => 'nullable|string|max:16',
            'bank1_doc_url' => 'nullable|string|max:255',
            'bank2_name' => 'nullable|string|max:32',
            'bank2_accnt_holder' => 'nullable|string|max:32',
            'bank2_account_type' => 'nullable|string|max:24',
            'bank2_account_num' => 'nullable|string|max:24',
            'bank2_ifsc_code' => 'nullable|string|max:16',
            'bank2_doc_url' => 'nullable|string|max:255',
            'date_of_reg' => 'nullable|date',
            'doc1_name' => 'nullable|string|max:48',
            'doc1_url' => 'nullable|string|max:255',
            'doc1_date' => 'nullable|date',
            'doc2_name' => 'nullable|string|max:48',
            'doc2_url' => 'nullable|string|max:255',
            'doc2_date' => 'nullable|date',
            'doc3_name' => 'nullable|string|max:48',
            'doc3_url' => 'nullable|string|max:255',
            'doc3_date' => 'nullable|date',
            'doc4_name' => 'nullable|string|max:48',
            'doc4_url' => 'nullable|string|max:255',
            'doc4_date' => 'nullable|date',
            'key_personnel1_name' => 'nullable|string|max:48',
            'key_personnel1_job_title' => 'nullable|string|max:48',
            'key_personnel1_mobile' => 'nullable|string|max:16',
            'key_personnel1_email' => 'nullable|string|email|max:64',
            'key_personnel2_name' => 'nullable|string|max:48',
            'key_personnel2_job_title' => 'nullable|string|max:48',
            'key_personnel2_mobile' => 'nullable|string|max:16',
            'key_personnel2_email' => 'nullable|string|email|max:64',
            'key_personnel3_name' => 'nullable|string|max:48',
            'key_personnel3_job_title' => 'nullable|string|max:48',
            'key_personnel3_mobile' => 'nullable|string|max:16',
            'key_personnel3_email' => 'nullable|string|email|max:64',
            'key_personnel4_name' => 'nullable|string|max:48',
            'key_personnel4_job_title' => 'nullable|string|max:48',
            'key_personnel4_mobile' => 'nullable|string|max:16',
            'key_personnel4_email' => 'nullable|string|email|max:64',
            'kyc_date' => 'nullable|date',
            'kyc_completed' => 'boolean',
            'active' => 'boolean',
            'status' => [
                'required',
                'string',
                Rule::in(['CREATED', 'APPROVED', 'REJECTED', 'PENDING_UPDATE', 'PENDING_APPROVAL']),
            ],
            'note' => 'nullable|string|max:255'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::error('Validation failed for new CP KYC request data in CpKycStoreRequest', $validator->errors()->toArray());
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
