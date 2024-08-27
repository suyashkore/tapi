<?php

namespace App\Feature\Tenant\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class TenantUpdateRequest
 *
 * Request validation for updating an existing tenant.
 *
 * @package App\Feature\Tenant\Requests
 */
class TenantUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Add logic if necessary to determine if the user is authorized
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        Log::debug('Validating existing tenant update request data in TenantUpdateRequest');

        return [
            'name' => 'sometimes|string|max:128',
            'country' => 'sometimes|nullable|string|max:64',
            'state' => 'sometimes|nullable|string|max:64',
            'city' => 'sometimes|nullable|string|max:64',
            'pincode' => 'sometimes|string|max:16',
            'address' => 'sometimes|nullable|string|max:255',
            'latitude' => 'sometimes|string|max:16',
            'longitude' => 'sometimes|string|max:16',
            'description' => 'sometimes|nullable|string|max:255',
            'active' => 'sometimes|boolean',
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
        Log::error('Validation failed for existing tenant update request data in TenantUpdateRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
