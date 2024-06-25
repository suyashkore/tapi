<?php

namespace App\Feature\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Adjust authorization logic as needed
    }

    protected function prepareForValidation()
    {
        // Extract user context from request attributes
        $userContext = $this->attributes->get('userContext');

        // Merge tenant_id from userContext into the request data if it's not null
        if ($userContext && $userContext->tenantId !== null) {
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
        // Get the role ID from the route
        $roleId = $this->route('id');

        return [
            'tenant_id' => 'nullable|exists:tenants,id',
            'name' => [
                'sometimes',
                'string',
                'max:24',
                Rule::unique('roles')->where(function ($query) use ($roleId) {
                    return $query->where('tenant_id', $this->tenant_id)
                                 ->where('id', '!=', $roleId);
                })
            ],
            'description' => 'nullable|string|max:255',
            'privileges' => 'sometimes|array',
            'privileges.*' => 'exists:privileges,id'
        ];
        /**
         * The name validation rule ensures that the role name is unique within the
         * same tenant_id context excluding the current role being updated. If
         * tenant_id is null, the query will check for uniqueness among all roles
         * with tenant_id as null.
         */
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'tenant_id.exists' => 'The selected tenant does not exist.',
            'name.unique' => 'The role name has already been taken for this tenant.',
            'privileges.required' => 'At least one privilege must be assigned to the role.',
            'privileges.*.exists' => 'The selected privilege does not exist.',
        ];
    }
}
