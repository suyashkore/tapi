<?php

namespace App\Feature\Customer\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Class CustomerUpdateRequest
 *
 * Handles validation for updating an existing customer.
 *
 * @package App\Feature\Customer\Requests
 */
class CustomerUpdateRequest extends FormRequest
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
        $customerId = $this->route('id'); // Get the customer ID from the route parameter

        return [
            'tenant_id' => 'nullable|exists:tenants,id',
            'company_tag' => 'nullable|exists:companies,id',
            'parent_id' => 'nullable|exists:customers,id',
            'code' => [
                'sometimes',
                'string',
                'max:16',
                Rule::unique('customers')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($customerId)
            ],
            'name' => 'sometimes|string|max:128',
            'name_reg' => 'nullable|string|max:255',
            'payment_types' => 'sometimes|required|json',
            'industry_type' => 'nullable|string|max:128',
            'c_type' => 'sometimes|required|string|in:CONTRACTUAL,RETAIL|max:16',
            'c_subtype' => 'nullable|string|in:CONSIGNOR,CONSIGNEE|max:24',
            'pan_num' => 'nullable|string|max:16',
            'gst_num' => 'nullable|string|max:16',
            'country' => 'nullable|string|max:64',
            'state' => 'nullable|string|max:64',
            'district' => 'nullable|string|max:64',
            'taluka' => 'nullable|string|max:64',
            'city' => 'sometimes|required|string|max:64',
            'pincode' => 'sometimes|required|string|max:16',
            'latitude' => 'nullable|string|max:16',
            'longitude' => 'nullable|string|max:16',
            'address' => 'nullable|string|max:255',
            'address_reg' => 'nullable|string|max:512',
            'mobile' => 'nullable|string|max:16',
            'tel_num' => 'nullable|string|max:16',
            'email' => 'nullable|string|email|max:64',
            'billing_contact_person' => 'nullable|string|max:48',
            'billing_mobile' => 'sometimes|required|string|max:16',
            'billing_email' => 'sometimes|required|string|email|max:64',
            'billing_address' => 'sometimes|required|string|max:255',
            'billing_address_reg' => 'nullable|string|max:512',
            'primary_servicing_office_id' => 'sometimes|required|exists:offices,id',
            'other_servicing_offices' => 'nullable|json',
            'erp_entry_date' => 'nullable|date',
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
        Log::error('Validation failed for update customer request data in CustomerUpdateRequest', $validator->errors()->toArray());
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
