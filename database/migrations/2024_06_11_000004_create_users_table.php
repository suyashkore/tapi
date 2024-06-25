<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary key
            $table->mediumIncrements('id');

            // Foreign key from tenants table
            $table->unsignedSmallInteger('tenant_id')->nullable();

            // User details
            $table->string('name', 48);
            $table->string('login_id', 24);
            $table->string('mobile', 16);
            $table->string('email', 64)->nullable();
            $table->string('email2', 64)->nullable();
            $table->string('google_id', 32)->nullable();
            $table->string('password_hash');
            $table->string('profile_pic_url', 255)->nullable();
            $table->string('user_type', 16); // Changed to 'user_type'
            $table->unsignedSmallInteger('role_id')->nullable();
            $table->string('sso_id', 32)->nullable();
            $table->string('sso_ref', 32)->nullable();
            $table->string('job_title', 32)->nullable();
            $table->string('department', 32)->nullable();
            $table->string('aadhaar', 16)->nullable();
            $table->string('pan', 16)->nullable();
            $table->string('epf_uan', 16)->nullable();
            $table->string('epf_num', 16)->nullable();
            $table->string('esic', 32)->nullable();

            // Additional details
            $table->dateTime('last_login')->nullable();
            $table->dateTime('last_password_reset')->nullable();
            $table->unsignedTinyInteger('failed_login_attempts')->default(0);
            $table->boolean('active')->default(true);
            $table->string('remarks', 255)->nullable();

            // Foreign keys for created_by and updated_by
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();

            // Timestamps
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Composite unique constraints for tenant-specific uniqueness
            $table->unique(['tenant_id', 'login_id'], 'tenant_login_unique');
            $table->unique(['tenant_id', 'mobile'], 'tenant_mobile_unique');
            $table->unique(['tenant_id', 'email'], 'tenant_email_unique');
            $table->unique(['tenant_id', 'google_id'], 'tenant_google_unique');

            // Indexes
            $table->index(['tenant_id', 'role_id'], 'tenant_role_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['role_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop composite unique constraints
            $table->dropUnique('tenant_login_unique');
            $table->dropUnique('tenant_mobile_unique');
            $table->dropUnique('tenant_email_unique');
            $table->dropUnique('tenant_google_unique');

            // Drop indexes
            $table->dropIndex('tenant_role_index');
        });

        Schema::dropIfExists('users');
    }
}
