<?php

namespace App\Feature\Office\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

/**
 * Class OfficeStoreRequest
 *
 * Handles validation for storing a new office.
 *
 * @package App\Feature\Office\Requests
 */
class OfficeStoreRequest extends FormRequest
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
            'company_tag' => 'nullable|exists:companies,id',
            'code' => [
                'required',
                'string',
                'max:16',
                Rule::unique('offices')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })
            ],
            'name' => 'required|string|max:64',
            'name_reg' => 'nullable|string|max:128',
            'gst_num' => 'nullable|string|max:16',
            'cin_num' => 'nullable|string|max:24',
            'owned' => 'boolean',
            'o_type' => 'required|string|in:HQ,REG_HQ,BACK_OFFICE,BRANCH,HUB,WAREHOUSE|max:24',
            'cp_kyc_id' => 'nullable|exists:cp_kyc,id',
            'country' => 'nullable|string|max:64',
            'state' => 'nullable|string|max:64',
            'district' => 'nullable|string|max:64',
            'taluka' => 'nullable|string|max:64',
            'city' => 'nullable|string|max:64',
            'pincode' => 'required|integer|digits:6',
            'latitude' => 'required|numeric|min:-90|max:90',
            'longitude' => 'required|numeric|min:-180|max:180',
            'address' => 'required|string|max:255',
            'address_reg' => 'nullable|string|max:512',
            'active' => 'boolean',
            'description' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:offices,id',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id'
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
        Log::error('Validation failed for new office request data in OfficeStoreRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
