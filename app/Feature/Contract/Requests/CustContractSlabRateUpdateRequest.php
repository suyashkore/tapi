<?php

namespace App\Feature\Contract\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Class CustContractSlabRateUpdateRequest
 *
 * Handles validation for updating an existing contract slab rate.
 *
 * @package App\Feature\Contract\Requests
 */
class CustContractSlabRateUpdateRequest extends FormRequest
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
        if ($userContext && isset($userContext->tenantId)) {
            $this->merge([
                'tenant_id' => $userContext->tenantId,
            ]);
        } else {
            // Log an error or throw an exception if tenant_id cannot be determined
            Log::error('Tenant ID missing or invalid in userContext', ['userContext' => $userContext]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $custContractSlabRateId = $this->route('id'); // Get the contract slab rate ID from the route parameter

        return [
            'tenant_id' => 'nullable|exists:tenants,id',
            'cust_contract_id' => 'required|exists:cust_contracts,id',
            'ctr_num' => 'required|string|max:24',
            'zone' => 'nullable|string|max:16',
            'from_place_id' => 'nullable|exists:station_coverage,id',
            'from_place' => 'required|string|max:64',
            'to_place_id' => 'nullable|exists:station_coverage,id',
            'to_place' => 'required|string|max:64',
            'tat' => 'nullable|integer|min:0|max:255',
            'slab_distance_type' => 'required|json',
            'slab_contract_type' => 'required|string|in:PER_KG,PER_PKG|max:16',
            'slab1' => 'required|numeric|min:0',
            'slab2' => 'required|numeric|min:0',
            'slab3' => 'required|numeric|min:0',
            'slab4' => 'required|numeric|min:0',
            'slab5' => 'required|numeric|min:0',
            'slab6' => 'required|numeric|min:0',
            'slab7' => 'required|numeric|min:0',
            'slab8' => 'required|numeric|min:0',
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
        Log::error('Validation failed for update contract slab rate request data in CustContractSlabRateUpdateRequest', $validator->errors()->toArray());
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
