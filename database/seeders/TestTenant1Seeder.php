<?php

namespace Database\Seeders;

use App\Feature\Tenant\Models\Tenant;
use App\Feature\User\Models\Privilege;
use App\Feature\User\Models\Role;
use App\Feature\User\Models\RolePrivilege;
use App\Feature\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestTenant1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Retrieve the system user to set as created_by and updated_by
        $systemUser = User::where('tenant_id', null)->where('login_id', 'sys')->first();

        if (!$systemUser) {
            $this->command->error("System user with login_id 'sys' not found. Make sure to run the SystemSeeder first.");
            return;
        }

        // Begin transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // Create the first tenant
            $tenant = Tenant::create([
                'name' => 'VTC Services Ltd',
                'country' => 'India',
                'state' => 'Maharashtra',
                'city' => 'Pune',
                'pincode' => '411028',
                'address' => 'Fursungi, Pune',
                'latitude' => '18.4956',
                'longitude' => '73.9242',
                'logo_url' => 'http://localhost:8000/storage/vtc-logo.png',
                'description' => "It's an well established group of companies in Fursungi focused on 3PL & 4PL services.",
                'active' => true,
                'created_by' => $systemUser->id,
                'updated_by' => $systemUser->id,
            ]);

            // Create a privilege for tenants
            $privilege = Privilege::create([
                'name' => 'TENANT_ALL',
                'description' => 'All features meant to be used by a tenant without any restriction for user of type TENANT',
                'created_by' => $systemUser->id,
                'updated_by' => $systemUser->id,
            ]);

            // Create a role for the tenant
            $role = Role::create([
                'tenant_id' => $tenant->id,
                'name' => 'TENANT_ADMIN',
                'description' => 'All inclusive role for user of TENANT',
                'created_by' => $systemUser->id,
                'updated_by' => $systemUser->id,
            ]);

            // Map role and privilege
            RolePrivilege::create([
                'role_id' => $role->id,
                'privilege_id' => $privilege->id,
                'created_by' => $systemUser->id,
                'updated_by' => $systemUser->id,
            ]);

            // Create a user for the tenant
            $user = User::create([
                'tenant_id' => $tenant->id,
                'login_id' => 'admin',
                'mobile' => '9876543211',
                'email' => 'admin@vtc.com',
                'password_hash' => Hash::make('007'), // Hashing the password
                'name' => 'VTC Admin',
                'profile_pic_url' => 'http://localhost:8000/storage/user.jpg',
                'user_type' => 'TENANT',
                'role_id' => $role->id,
                'active' => true,
                'remarks' => 'default tenant admin user seeded for new tenant.',
                'created_by' => $systemUser->id,
                'updated_by' => $systemUser->id,
            ]);

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction in case of any error
            DB::rollback();
            $this->command->error('Error occurred while seeding TestTenant1: ' . $e->getMessage());
        }
    }
}
