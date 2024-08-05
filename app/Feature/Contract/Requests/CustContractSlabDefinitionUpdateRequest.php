<?php

namespace App\Feature\Contract\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Class CustContractSlabDefinitionUpdateRequest
 *
 * Handles validation for updating an existing CustContractSlabDefinition.
 *
 * @package App\Feature\Contract\Requests
 */
class CustContractSlabDefinitionUpdateRequest extends FormRequest
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
        $custContractSlabDefinitionId = $this->route('id'); // Get the slab definition ID from the route parameter

        return [
            'tenant_id' => 'required|exists:tenants,id',
            'cust_contract_id' => 'required|exists:cust_contracts,id',
            'ctr_num' => 'required|string|max:24',
            'slab_distance_type' => 'required|json',
            'slab_contract_type' => 'required|string|max:16',
            'slab_rate_type' => 'required|string|in:RATED,FLAT|max:8',
            'slab_number' => [
                'required',
                'string',
                'in:1,2,3,4,5,6,7,8',
                'max:8',
                Rule::unique('cust_contract_slab_definitions')->where(function ($query) {
                    return $query->where('cust_contract_id', $this->cust_contract_id);
                })->ignore($custContractSlabDefinitionId)
            ],
            'slab_lower_limit' => 'required|integer',
            'slab_upper_limit' => 'required|integer',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
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
        Log::error('Validation failed for update CustContractSlabDefinition request data in CustContractSlabDefinitionUpdateRequest', $validator->errors()->toArray());
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
