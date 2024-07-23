<?php

namespace App\Feature\Station\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeoHierarchyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // You may add authorization logic here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'country' => 'required|string|max:64',
            'state' => 'required|string|max:64',
            'district' => 'required|string|max:64',
            'taluka' => 'required|string|max:64',
            'po_name' => 'required|string|max:64',
            'pincode' => 'required|string|max:16',
            'po_lat' => 'required|string|max:16',
            'po_long' => 'required|string|max:16',
            'place' => 'nullable|string|max:64',
            'place_lat' => 'nullable|string|max:16',
            'place_long' => 'nullable|string|max:16',
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
            'country.required' => 'The country field is required.',
            'state.required' => 'The state field is required.',
            'district.required' => 'The district field is required.',
            'taluka.required' => 'The taluka field is required.',
            'po_name.required' => 'The post office name is required.',
            'pincode.required' => 'The pincode is required.',
            'po_lat.required' => 'The post office latitude is required.',
            'po_long.required' => 'The post office longitude is required.',
        ];
    }
}
