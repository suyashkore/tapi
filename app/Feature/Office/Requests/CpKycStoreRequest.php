<?php

namespace App\Feature\Office\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

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
        return true;
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
        Log::debug('Validating new CP KYC request data in CpKycStoreRequest');

        return [
            'tenant_id' => 'required|exists:tenants,id',
            'office_id' => 'nullable|string|exists:offices,id',
            'legal_name' => 'required|string|max:64',
            'gst_num' => 'nullable|string|max:16',
            'cin_num' => 'nullable|string|max:24',
            'pan_num' => 'required|string|max:16',
            'bank_name' => 'required|string|max:32',
            'bank_account_num' => 'required|string|max:24',
            'bank_ifsc_code' => 'required|string|max:16',
            'owner_aadhaar' => 'nullable|string|max:16',
            'owner_pan' => 'nullable|string|max:16',
            'owner_photo_url' => 'nullable|string|max:255',
            'owner_email' => 'nullable|string|max:64',
            'owner_mobile' => 'nullable|string|max:16',
            'finance_head_email' => 'nullable|string|max:64',
            'finance_head_mobile' => 'nullable|string|max:16',
            'kyc_completed' => 'boolean',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
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
        Log::error('Validation failed for new CP KYC request data in CpKycStoreRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
