<?php

namespace App\Feature\Fleet\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Class VehicleStoreRequest
 *
 * Handles validation for storing a new vehicle.
 *
 * @package App\Feature\Fleet\Requests
 */
class VehicleStoreRequest extends FormRequest
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
            'base_office_id' => 'required|exists:offices,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'rc_num' => [
                'required',
                'string',
                'max:16',
                Rule::unique('vehicles')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })
            ],
            'vehicle_num' => 'nullable|string|max:16',
            'vehicle_ownership' => 'required|string|in:OWN,ATTACHED,MARKET|max:16',
            'make' => 'required|string|max:32',
            'model' => 'nullable|string|max:32',
            'gvw' => 'nullable|numeric',
            'capacity' => 'nullable|numeric',
            'gvw_capacity_unit' => 'nullable|string|in:KG,TON|max:16',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'lwh_unit' => 'nullable|string|in:METER,FEET|max:16',
            'specification' => 'nullable|string|max:64',
            'sub_specification' => 'nullable|string|max:64',
            'fuel_type' => 'nullable|string|max:32',
            'rto_reg_expiry' => 'nullable|date',
            'rc_url' => 'nullable|url|max:255',
            'insurance_policy_num' => 'nullable|string|max:32',
            'insurance_expiry' => 'nullable|date',
            'insurance_doc_url' => 'nullable|url|max:255',
            'fitness_cert_num' => 'nullable|string|max:32',
            'fitness_cert_expiry' => 'nullable|date',
            'fitness_cert_url' => 'nullable|url|max:255',
            'vehicle_contact_mobile1' => 'nullable|string|max:16',
            'vehicle_contact_mobile2' => 'nullable|string|max:16',
            'active' => 'boolean',
            'status' => 'required|string|in:CREATED,APPROVED,REJECTED,PENDING_UPDATE,PENDING_APPROVAL|max:24',
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
        Log::error('Validation failed for new vehicle request data in VehicleStoreRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
