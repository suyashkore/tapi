<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Feature\User\Models\Privilege;
use App\Feature\User\Models\Role;
use App\Feature\User\Models\RolePrivilege;
use App\Feature\User\Models\User;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Privilege
        $privilege = Privilege::create([
            'name' => 'SYS_ALL',
            'description' => 'All features in the application without any restriction for user of SYSTEM',
            'created_by' => null,
            'updated_by' => null,
        ]);

        // Create Role
        $role = Role::create([
            'tenant_id' => null,
            'name' => 'SUPER_ADMIN',
            'description' => 'All inclusive role for user of SYSTEM',
            'created_by' => null,
            'updated_by' => null,
        ]);

        // Map Role and Privilege
        $rolePrivilege = RolePrivilege::create([
            'role_id' => $role->id,
            'privilege_id' => $privilege->id,
            'created_by' => null,
            'updated_by' => null,
        ]);

        // Create User
        $user = User::create([
            'tenant_id' => null,
            'login_id' => 'sys',
            'mobile' => '9876543210',
            'email' => 'sys@swatpro.co',
            'password_hash' => Hash::make('007'),
            'name' => 'System Owner',
            'profile_pic_url' => 'http://localhost:8000/storage/user.jpg',
            'user_type' => 'SYSTEM',
            'role_id' => $role->id,
            'active' => true,
            'remarks' => 'default super admin user seeded for initiating the system.',
            'created_by' => null,
            'updated_by' => null,
        ]);

        // Update created_by and updated_by fields with the ID of the newly created user
        $userId = $user->id;

        $privilege->update([
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        $role->update([
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        $rolePrivilege->update([
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        $user->update([
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
    }
}
