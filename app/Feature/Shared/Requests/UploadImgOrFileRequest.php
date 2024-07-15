<?php

namespace App\Feature\Shared\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class UploadImgOrFileRequest
 *
 * Request validation for uploading an image or other file.
 *
 * @package App\Feature\Shared\Requests
 */
class UploadImgOrFileRequest extends FormRequest
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
        Log::debug('Validating file upload request data in UploadImgOrFileRequest');

        return [
            'file' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,gif,heic,webp,bmp,pdf,zip,doc,docx,xls,xlsx,ppt,pptx,txt,rtf',
                function ($attribute, $value, $fail) {
                    $maxSizeInBytes = 20 * 1024 * 1024; // 20MB for non-image files
                    $imageExtensions = ['jpeg', 'jpg', 'png', 'gif', 'heic', 'webp', 'bmp'];
                    if (in_array($value->getClientOriginalExtension(), $imageExtensions)) {
                        $maxSizeInBytes = 10 * 1024 * 1024; // 10MB for image files
                    }
                    if ($value->getSize() > $maxSizeInBytes) {
                        Log::info('UploadImgOrFileRequest file size: '.$value->getSize());
                        return $fail("The $attribute may not be greater than " . ($maxSizeInBytes / (1024 * 1024)) . " MB.");
                    }
                }
            ],
            'urlfield_name' => 'required|string|max:32',
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
        Log::error('Validation failed for file upload request data in UploadImgOrFileRequest', $validator->errors()->toArray());
        throw new ValidationException($validator);
    }
}
