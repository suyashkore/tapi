<?php

namespace App\Feature\Contract\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Class CustContractUpdateRequest
 *
 * Handles validation for updating an existing customer contract.
 *
 * @package App\Feature\Contract\Requests
 */
class CustContractUpdateRequest extends FormRequest
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
        $contractId = $this->route('id'); // Get the contract ID from the route parameter

        return [
            'tenant_id' => 'nullable|exists:tenants,id',
            'company_tag' => 'nullable|exists:companies,id',
            'ctr_num' => [
                'sometimes',
                'string',
                'max:24',
                Rule::unique('cust_contracts')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($contractId)
            ],
            'customer_group_id' => 'nullable|exists:customers,id',
            'customer_id' => 'required|exists:customers,id',
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date_format:Y-m-d H:i:s',
            'payment_type' => 'required|json',
            'load_type' => 'required|json',
            'distance_type' => 'required|json',
            'rate_type' => 'required|json',
            'pickup_delivery_mode' => 'required|json',
            'excess_wt_chargeable' => 'boolean',
            'oda_del_chargeable' => 'boolean',
            'credit_period' => 'integer|min:1|max:365',
            'docu_charges_per_invoice' => 'required|numeric|min:0',
            'loading_charges_per_pkg' => 'required|numeric|min:0',
            'fuel_surcharge' => 'required|numeric|min:0',
            'oda_min_del_charges' => 'required|numeric|min:0',
            'reverse_pick_up_charges' => 'required|numeric|min:0',
            'insurance_charges' => 'required|numeric|min:0',
            'minimum_chargeable_wt' => 'required|numeric|min:0',
            'active' => 'boolean',
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
        Log::error('Validation failed for update customer contract request data in CustContractUpdateRequest', $validator->errors()->toArray());
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
