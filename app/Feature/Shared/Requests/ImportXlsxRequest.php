<?php

namespace App\Feature\Shared\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class ImportXlsxRequest
 *
 * Request validation for importing data from an Excel file.
 *
 * @package App\Feature\Shared\Requests
 */
class ImportXlsxRequest extends FormRequest
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
        Log::debug('Validating import xlsx request data in ImportXlsxRequest');

        return [
            'file' => 'required|file|mimes:xls,xlsx|max:5120', // 5MB max file size
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
        Log::error('Validation failed for import xlsx request data in ImportXlsxRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
