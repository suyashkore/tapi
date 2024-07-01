<?php

namespace App\Feature\Office\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

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
        Log::debug('Validating new office request data in OfficeStoreRequest');

        return [
            'tenant_id' => 'required|exists:tenants,id',
            'code' => 'nullable|string|max:16',
            'name' => 'required|string|max:64',
            'name_reg' => 'nullable|string|max:128',
            'gst_num' => 'nullable|string|max:16',
            'cin_num' => 'nullable|string|max:24',
            'owned' => 'boolean',
            'o_type' => 'nullable|string|max:24',
            'country' => 'nullable|string|max:64',
            'state' => 'nullable|string|max:64',
            'district' => 'nullable|string|max:64',
            'taluka' => 'nullable|string|max:64',
            'city' => 'nullable|string|max:64',
            'pincode' => 'required|string|max:16',
            'latitude' => 'required|string|max:16',
            'longitude' => 'required|string|max:16',
            'address' => 'required|string|max:255',
            'address_reg' => 'nullable|string|max:512',
            'active' => 'boolean',
            'description' => 'nullable|string|max:255',
            'parent_id' => 'nullable|string|exists:offices,id',
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
        Log::error('Validation failed for new office request data in OfficeStoreRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
