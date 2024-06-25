<?php

namespace App\Feature\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrivilegeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Authorization logic can be added here if needed
        // For now, we'll just return true to allow all requests
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Retrieve the privilege ID from the route
        $privilegeId = $this->route('id');

        return [
            'name' => 'sometimes|string|max:48|unique:privileges,name,' . $privilegeId,
            'description' => 'nullable|string|max:255',
        ];
        /**
         * Validation rule for the 'name' attribute in the PrivilegeUpdateRequest.
         * This rule ensures that:
         * - The 'name' field is required.
         * - The 'name' field must be a string.
         * - The 'name' field must not exceed 48 characters.
         * - The 'name' field must be unique in the 'privileges' table.
         *
         * The uniqueness check allows the privilege's current name to remain the same
         * without failing the validation. The $privilegeId variable is used to
         * exclude the current privilege's ID from the uniqueness check. This means
         * that the validation will only fail if there is another privilege with the
         * same name, excluding the current privilege being updated.
         */
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The privilege name is required.',
            'name.string' => 'The privilege name must be a string.',
            'name.max' => 'The privilege name must not exceed 48 characters.',
            'name.unique' => 'The privilege name must be unique.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description must not exceed 255 characters.',
        ];
    }
}
