<?php

namespace App\Feature\Contract\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class LoaderRateUpdateRequest
 *
 * @package App\Feature\Contract\Requests
 */
class LoaderRateUpdateRequest extends FormRequest
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
        return [
            'tenant_id' => 'nullable|exists:tenants,id',
            'contracting_office_id' => 'required|exists:offices,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'vendor_name' => 'required|string|max:128',
            'default_rate_type' => 'required|string|in:PKG_WT,DAILY,MONTHLY,ALL|max:24',
            'reg_pkg_rate' => 'nullable|numeric|min:0',
            'crossing_pkg_rate' => 'nullable|numeric|min:0',
            'reg_weight_rate' => 'nullable|numeric|min:0',
            'crossing_weight_rate' => 'nullable|numeric|min:0',
            'monthly_sal' => 'nullable|numeric|min:0',
            'daily_allowance' => 'nullable|numeric|min:0',
            'daily_wage' => 'nullable|numeric|min:0',
            'daily_wage_pkg_capping' => 'nullable|integer|min:0',
            'daily_wage_weight_capping' => 'nullable|integer|min:0',
            'overtime_hourly_rate' => 'nullable|numeric|min:0',
            'active' => 'boolean',
            'status' => 'required|string|in:CREATED,APPROVED,REJECTED,PENDING_UPDATE,PENDING_APPROVAL|max:24',
            'note' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
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
        Log::error('Validation failed for LoaderRate update request.', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
