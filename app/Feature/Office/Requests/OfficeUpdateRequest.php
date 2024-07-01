<?php

namespace App\Feature\Office\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

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
        Log::debug('Validating existing office update request data in OfficeUpdateRequest');

        return [
            'tenant_id' => 'sometimes|exists:tenants,id',
            'code' => 'nullable|string|max:16',
            'name' => 'sometimes|string|max:64',
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
            'pincode' => 'sometimes|string|max:16',
            'latitude' => 'sometimes|string|max:16',
            'longitude' => 'sometimes|string|max:16',
            'address' => 'sometimes|string|max:255',
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
        Log::error('Validation failed for existing office update request data in OfficeUpdateRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
