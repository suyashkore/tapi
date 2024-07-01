<?php

namespace App\Feature\Customer\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class CustomerStoreRequest
 *
 * Handles validation for storing a new customer.
 *
 * @package App\Feature\Customer\Requests
 */
class CustomerStoreRequest extends FormRequest
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
        Log::debug('Validating new customer request data in CustomerStoreRequest');

        return [
            'tenant_id' => 'required|exists:tenants,id',
            'parent_id' => 'nullable|string|exists:customers,id',
            'name' => 'required|string|max:128',
            'name_reg' => 'nullable|string|max:255',
            'payment_types' => 'required|json',
            'industry_type' => 'nullable|string|max:128',
            'customer_type' => 'nullable|string|max:24',
            'pan' => 'nullable|string|max:16',
            'gst_num' => 'nullable|string|max:16',
            'cin_num' => 'nullable|string|max:24',
            'country' => 'nullable|string|max:64',
            'state' => 'nullable|string|max:64',
            'district' => 'nullable|string|max:64',
            'city' => 'required|string|max:64',
            'pincode' => 'required|string|max:16',
            'latitude' => 'nullable|string|max:16',
            'longitude' => 'nullable|string|max:16',
            'address' => 'nullable|string|max:255',
            'address_reg' => 'nullable|string|max:512',
            'mobile' => 'nullable|string|max:16',
            'tel_num' => 'nullable|string|max:16',
            'email' => 'nullable|string|max:64',
            'billing_contact_person' => 'nullable|string|max:48',
            'billing_mobile' => 'required|string|max:16',
            'billing_email' => 'required|string|max:64',
            'billing_address' => 'required|string|max:255',
            'billing_address_reg' => 'nullable|string|max:512',
            'other_servicing_offices' => 'nullable|json',
            'primary_servicing_office' => 'required|string|exists:offices,id',
            'erp_entry_date' => 'nullable|date',
            'active' => 'boolean',
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
        Log::error('Validation failed for new customer request data in CustomerStoreRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
