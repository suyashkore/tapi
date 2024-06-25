<?php

namespace App\Feature\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrivilegeStoreRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:48|unique:privileges,name',
            'description' => 'nullable|string|max:255',
        ];
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
