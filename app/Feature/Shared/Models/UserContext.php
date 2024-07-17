<?php

namespace App\Feature\Shared\Models;

class UserContext
{
    public $userId;
    public $tenantId;
    public $loginId;
    public $mobile;
    public $email;
    public $userType;
    public $roleName;
    public $privileges;

    // Constructor
    public function __construct($payload)
    {
        $this->userId = $payload->get('user_id');
        $this->tenantId = $payload->get('tenant_id');
        $this->loginId = $payload->get('login_id');
        $this->mobile = $payload->get('mobile');
        $this->email = $payload->get('email');
        $this->userType = $payload->get('user_type');
        $this->roleName = $payload->get('role_name');
        $this->privileges = $payload->get('privileges');
    }
}
