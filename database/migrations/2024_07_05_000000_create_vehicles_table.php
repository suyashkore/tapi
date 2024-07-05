<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            // Primary key
            $table->unsignedMediumInteger('id')->autoIncrement();

            // Foreign key from tenants table
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            // Foreign key from companies table
            $table->unsignedSmallInteger('company_tag')->nullable();
            $table->foreign('company_tag')->references('id')->on('companies')->onDelete('set null');

            // Foreign key from offices table
            $table->unsignedMediumInteger('base_office_id');
            $table->foreign('base_office_id')->references('id')->on('offices')->onDelete('cascade');

            // Foreign key from vendors table
            $table->unsignedMediumInteger('vendor_id')->nullable();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');

            // Vehicle details
            $table->string('rc_num', 16); // Mandatory RTO registration number
            $table->string('vehicle_num', 16)->nullable(); // Vehicle number
            $table->string('vehicle_ownership', 16); // Ownership type
            $table->string('make', 32); // Vehicle make
            $table->string('model', 32)->nullable(); // Vehicle model
            $table->float('gvw')->nullable(); // Gross vehicle weight
            $table->float('capacity')->nullable(); // Capacity
            $table->string('gvw_capacity_unit', 16)->nullable(); // GVW capacity unit
            $table->float('length')->nullable(); // Length
            $table->float('width')->nullable(); // Width
            $table->float('height')->nullable(); // Height
            $table->string('lwh_unit', 16)->nullable(); // Length, width, height unit
            $table->string('specification', 64)->nullable(); // Specification
            $table->string('sub_specification', 64)->nullable(); // Sub-specification
            $table->string('fuel_type', 32)->nullable(); // Fuel type
            $table->dateTime('rto_reg_expiry')->nullable(); // RTO registration expiry date
            $table->string('rc_url', 255)->nullable(); // URL for Registration Card PDF
            $table->string('insurance_policy_num', 32)->nullable(); // Insurance policy number
            $table->dateTime('insurance_expiry')->nullable(); // Insurance expiry date
            $table->string('insurance_doc_url', 255)->nullable(); // URL for Insurance Policy PDF
            $table->string('fitness_cert_num', 32)->nullable(); // Fitness certificate number
            $table->dateTime('fitness_cert_expiry')->nullable(); // Fitness certificate expiry date
            $table->string('fitness_cert_url', 255)->nullable(); // URL for Fitness Certificate PDF
            $table->string('vehicle_contact_mobile1', 16)->nullable(); // Primary contact mobile
            $table->string('vehicle_contact_mobile2', 16)->nullable(); // Secondary contact mobile

            // Status and activity
            $table->boolean('active')->default(true); // Active status
            $table->string('status', 24); // Status
            $table->string('note', 255)->nullable(); // Additional note

            // Foreign key from users table
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamps(); // created_at and updated_at columns

            // Composite unique constraints for tenant-specific uniqueness
            $table->unique(['tenant_id', 'rc_num'], 'tenant_rc_num_unique');

            // Indexes for faster queries
            $table->index('tenant_id');
            $table->index('base_office_id');
            $table->index('vendor_id');
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['company_tag']);
            $table->dropForeign(['base_office_id']);
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop composite unique constraints
            $table->dropUnique('tenant_rc_num_unique');

            // Drop indexes
            $table->dropIndex('tenant_id');
            $table->dropIndex('base_office_id');
            $table->dropIndex('vendor_id');
            $table->dropIndex('active');
        });

        // Drop the table
        Schema::dropIfExists('vehicles');
    }
}
