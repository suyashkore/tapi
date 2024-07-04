<?php

namespace App\Feature\Station\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class StationCoverageUpdateRequest
 *
 * Handles validation for updating an existing station coverage.
 *
 * @package App\Feature\Station\Requests
 */
class StationCoverageUpdateRequest extends FormRequest
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
        $stationCoverageId = $this->route('id'); // Get the station coverage ID from the route parameter

        return [
            'tenant_id' => 'nullable|exists:tenants,id',
            'name' => 'sometimes|required|string|max:64',
            'name_reg' => 'nullable|string|max:128',
            'post_name' => 'nullable|string|max:64',
            'post_name_reg' => 'nullable|string|max:64',
            'pincode' => 'sometimes|required|string|max:16',
            'taluka' => 'nullable|string|max:32',
            'taluka_reg' => 'nullable|string|max:128',
            'district' => 'sometimes|required|string|max:32',
            'district_reg' => 'nullable|string|max:128',
            'state' => 'sometimes|required|string|max:24',
            'state_reg' => 'nullable|string|max:128',
            'country' => 'sometimes|required|string|max:24',
            'latitude' => 'sometimes|required|string|max:16',
            'longitude' => 'sometimes|required|string|max:16',
            'servicing_office_id' => 'sometimes|required|exists:offices,id',
            'service_office_tat' => 'nullable|integer|min:0',
            'servicing_office_dist' => 'nullable|integer|min:0',
            'name_gmap' => 'nullable|string|max:64',
            'zone' => 'nullable|string|max:16',
            'route_num' => 'nullable|string|max:16',
            'route_sequence' => 'nullable|integer|min:0',
            'oda' => 'boolean',
            'nr_state_highway' => 'nullable|string|max:16',
            'nr_national_highway' => 'nullable|string|max:16',
            'active' => 'boolean',
            'status' => 'sometimes|required|string|in:CREATED,APPROVED,REJECTED,PENDING_UPDATE,PENDING_APPROVAL|max:24',
            'note' => 'nullable|string|max:255',
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
        Log::error('Validation failed for station coverage update request', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
