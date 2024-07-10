<?php

namespace App\Feature\Shared\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class UploadImageRequest
 *
 * Request validation for uploading an image.
 *
 * @package App\Feature\Shared\Requests
 */
class UploadImageRequest extends FormRequest
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
        Log::debug('Validating image upload request data in UploadImageRequest');

        return [
            'img' => 'required|file|mimes:jpeg,jpg,png,gif,pdf|max:10240', // 10MB max file size
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
        Log::error('Validation failed for image upload request data in UploadImageRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
