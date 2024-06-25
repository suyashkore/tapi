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

class TestTenant2Seeder extends Seeder
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

        // Retrieve the privilege created in TestTenant1Seeder
        $privilege = Privilege::where('name', 'TENANT_ALL')->first();

        if (!$privilege) {
            $this->command->error("Privilege 'TENANT_ALL' not found. Make sure to run the TestTenant1Seeder first.");
            return;
        }

        // Begin transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // Create the second tenant
            $tenant = Tenant::create([
                'name' => 'Avinash Cargo Pvt Ltd',
                'country' => 'India',
                'state' => 'Maharashtra',
                'city' => 'Pune',
                'pincode' => '411028',
                'address' => 'Shikrapur, Pune',
                'latitude' => '18.6196', // Assuming latitude
                'longitude' => '74.0922', // Assuming longitude
                'logo_url' => 'http://localhost:8000/storage/acpllogo.png',
                'description' => "It's an well established group of companies in Shikrapur focused on 3PL & 4PL services.",
                'active' => true,
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
                'mobile' => '9876543212',
                'email' => 'admin@acpl.com',
                'password_hash' => Hash::make('007'), // Hashing the password
                'name' => 'ACPL Admin',
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
            $this->command->error('Error occurred while seeding TestTenant2: ' . $e->getMessage());
        }
    }
}
