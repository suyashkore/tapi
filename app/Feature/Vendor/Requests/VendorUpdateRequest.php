<?php

namespace App\Feature\Vendor\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

class VendorUpdateRequest extends FormRequest
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
        $userContext = $this->attributes->get('userContext');

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
        $vendorId = $this->route('id');

        return [
            'tenant_id' => 'nullable|exists:tenants,id',
            'company_tag' => 'nullable|exists:companies,id',
            'code' => [
                'sometimes',
                'string',
                'max:24',
                Rule::unique('vendors')->where(function ($query) {
                    return $query->where('tenant_id', $this->tenant_id);
                })->ignore($vendorId)
            ],
            'name' => 'sometimes|required|string|max:128',
            'name_reg' => 'nullable|string|max:255',
            'legal_name' => 'nullable|string|max:128',
            'legal_name_reg' => 'nullable|string|max:255',
            'v_type' => 'sometimes|required|string|in:FUEL_VENDOR,FLEET_VENDOR,LOADER,DRIVER,OTHERS|max:24',
            'mobile' => 'sometimes|required|string|max:16',
            'email' => 'nullable|string|email|max:64',
            'contracting_office_id' => 'sometimes|required|exists:offices,id',
            'erp_code' => 'nullable|string|max:24',
            'active' => 'boolean',
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
        Log::error('Validation failed for vendor update request data in VendorUpdateRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
