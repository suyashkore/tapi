<?php

namespace App\Feature\Office\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

/**
 * Class OfficeUpdateRequest
 *
 * Handles validation for updating an existing office.
 *
 * @package App\Feature\Office\Requests
 */
class OfficeUpdateRequest extends FormRequest
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
        $officeId = $this->route('id'); // Get the office ID from the route parameter

        return [
            'tenant_id' => 'nullable|exists:tenants,id',
            'company_tag' => 'nullable|exists:companies,id',
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:16',
                Rule::unique('offices')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($officeId),
            ],
            'name' => 'sometimes|required|string|max:64',
            'name_reg' => 'nullable|string|max:128',
            'gst_num' => 'nullable|string|max:16',
            'cin_num' => 'nullable|string|max:24',
            'owned' => 'boolean',
            'o_type' => 'sometimes|required|string|in:HQ,REG_HQ,BACK_OFFICE,BRANCH,HUB,WAREHOUSE|max:24',
            'cp_kyc_id' => 'nullable|exists:cp_kyc,id',
            'country' => 'nullable|string|max:64',
            'state' => 'nullable|string|max:64',
            'district' => 'nullable|string|max:64',
            'taluka' => 'nullable|string|max:64',
            'city' => 'nullable|string|max:64',
            'pincode' => 'sometimes|required|string|max:16',
            'latitude' => 'sometimes|required|string|max:16',
            'longitude' => 'sometimes|required|string|max:16',
            'address' => 'sometimes|required|string|max:255',
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
        Log::error('Validation failed for office update request data in OfficeUpdateRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
