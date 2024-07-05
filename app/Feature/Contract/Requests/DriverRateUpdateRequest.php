<?php

namespace App\Feature\Contract\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class DriverRateUpdateRequest
 *
 * @package App\Feature\Contract\Requests
 */
class DriverRateUpdateRequest extends FormRequest
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
            'default_rate_type' => 'required|string|in:HOURLY,BY_KM,DAILY,TRIP_WISE,MONTHLY,ALL|max:24',
            'daily_rate' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'overtime_hourly_rate' => 'nullable|numeric|min:0',
            'daily_allowance' => 'nullable|numeric|min:0',
            'per_km_rate' => 'nullable|numeric|min:0',
            'per_extra_km_rate' => 'nullable|numeric|min:0',
            'night_halt_rate' => 'nullable|numeric|min:0',
            'per_trip_rate' => 'nullable|numeric|min:0',
            'trip_allowance' => 'nullable|numeric|min:0',
            'incentive_per_trip' => 'nullable|numeric|min:0',
            'monthly_sal' => 'nullable|numeric|min:0',
            'monthly_incentive' => 'nullable|numeric|min:0',
            'per_trip_penalty_percent' => 'nullable|numeric|min:0',
            'per_trip_penalty_fixed_amount' => 'nullable|numeric|min:0',
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
        Log::error('Validation failed for DriverRate update request.', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
