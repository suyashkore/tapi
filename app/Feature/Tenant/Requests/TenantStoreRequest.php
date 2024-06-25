<?php

namespace App\Feature\Tenant\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class TenantStoreRequest
 *
 * Request validation for storing a new tenant.
 *
 * @package App\Feature\Tenant\Requests
 */
class TenantStoreRequest extends FormRequest
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
        Log::debug('Validating new tenant request data in TenantStoreRequest');

        return [
            'name' => 'required|string|max:128',
            'country' => 'nullable|string|max:64',
            'state' => 'nullable|string|max:64',
            'city' => 'nullable|string|max:64',
            'pincode' => 'required|string|max:16',
            'address' => 'nullable|string|max:255',
            'latitude' => 'required|string|max:16',
            'longitude' => 'required|string|max:16',
            'description' => 'nullable|string|max:255',
            'active' => 'boolean', // default value is true in the database
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
        Log::error('Validation failed for new tenant request data in TenantStoreRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
