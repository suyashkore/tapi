<?php

// <project_root>/app/Feature/User/Requests/AuthByLoginIdRequest.php

namespace App\Feature\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthByLoginIdRequest extends FormRequest
{
    public function rules()
    {
        return [
            'tenant_id' => 'sometimes',
            'login_id' => 'required',
            'password' => 'required',
        ];
    }
}
