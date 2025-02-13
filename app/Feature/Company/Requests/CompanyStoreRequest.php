<?php

namespace App\Feature\Company\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

/**
 * Class CompanyStoreRequest
 *
 * Handles validation for storing a new company.
 *
 * @package App\Feature\Company\Requests
 */
class CompanyStoreRequest extends FormRequest
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
        return [
            'tenant_id' => 'nullable|exists:tenants,id',
            'code' => [
                'required',
                'string',
                'max:16',
                Rule::unique('companies')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                }),
            ],
            'name' => 'required|string|max:64',
            'name_reg' => 'nullable|string|max:128',
            'address' => 'required|string|max:255',
            'address_reg' => 'nullable|string|max:512',
            'phone1' => 'nullable|numeric|digits:10',
            'phone2' => 'nullable|numeric|digits:10',
            'email1' => 'nullable|string|max:64',
            'email2' => 'nullable|string|max:64',
            'website' => 'nullable|string|max:128',
            'gst_num' => 'nullable|string|max:16',
            'cin_num' => 'nullable|string|max:24',
            'msme_num' => 'nullable|string|max:24',
            'pan_num' => 'nullable|string|max:16',
            'tan_num' => 'nullable|string|max:16',
            'logo_url' => 'nullable|string|max:255',
            'active' => 'boolean',
            'seq_num' => [
                'required',
                'integer',
                Rule::unique('companies')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                }),
            ],
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id'
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
        Log::error('Validation failed for new company request data in CompanyStoreRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
