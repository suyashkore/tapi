<?php

namespace App\Feature\Contract\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Class CustContractExcessWeightRateStoreRequest
 *
 * Handles validation for storing a new excess weight rate.
 *
 * @package App\Feature\Contract\Requests
 */
class CustContractExcessWeightRateStoreRequest extends FormRequest
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
        return [
            'tenant_id' => 'required|exists:tenants,id',
            'cust_contract_id' => 'required|exists:cust_contracts,id',
            'ctr_num' => 'required|string|max:24',
            'lower_limit' => 'required|numeric|min:0',
            'upper_limit' => 'required|numeric|min:0',
            'rate' => 'required|numeric|min:0',
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
        Log::error('Validation failed for new excess weight rate request data in CustContractExcessWeightRateStoreRequest', $validator->errors()->toArray());
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
