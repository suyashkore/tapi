<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            // Primary key: unsigned integer, auto-increment
            $table->unsignedInteger('id')->autoIncrement();

            // Foreign key from tenants table, nullable
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            // Foreign key from companies table, nullable
            $table->unsignedSmallInteger('company_tag')->nullable();
            $table->foreign('company_tag')->references('id')->on('companies')->onDelete('set null');

            // Foreign key from self table (customers), nullable
            $table->unsignedInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('customers')->onDelete('set null');

            // Mandatory fields
            $table->string('code', 16); // Unique for a given tenant_id
            $table->string('name', 128);
            $table->json('payment_types');

            // Nullable fields
            $table->string('name_reg', 255)->nullable(); // Name in regional language
            $table->string('industry_type', 128)->nullable(); // Industry type
            $table->string('c_type', 16); // Customer type
            $table->string('c_subtype', 24)->nullable(); // Customer subtype
            $table->string('pan_num', 16)->nullable(); // PAN number
            $table->string('gst_num', 16)->nullable(); // GST number
            $table->string('country', 64)->nullable();
            $table->string('state', 64)->nullable();
            $table->string('district', 64)->nullable();
            $table->string('taluka', 64)->nullable();
            $table->string('city', 64);
            $table->string('pincode', 16);
            $table->string('latitude', 16)->nullable();
            $table->string('longitude', 16)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('address_reg', 512)->nullable(); // Address in regional language
            $table->string('mobile', 16)->nullable();
            $table->string('tel_num', 16)->nullable();
            $table->string('email', 64)->nullable();

            // Billing information
            $table->string('billing_contact_person', 48)->nullable();
            $table->string('billing_mobile', 16);
            $table->string('billing_email', 64);
            $table->string('billing_address', 255);
            $table->string('billing_address_reg', 512)->nullable(); // Billing address in regional language

            // Foreign key from offices table (primary servicing office), mandatory
            $table->unsignedMediumInteger('primary_servicing_office_id');
            $table->foreign('primary_servicing_office_id')->references('id')->on('offices')->onDelete('cascade');

            // Nullable fields
            $table->json('other_servicing_offices')->nullable(); // Array of comma-separated office IDs
            $table->dateTime('erp_entry_date')->nullable(); // ERP creation date
            $table->boolean('active')->default(true); // Active status

            // Foreign key references for created_by and updated_by fields, nullable
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps for created_at and updated_at fields, auto-populated by the database
            $table->timestamps();

            // Indexes for faster queries
            $table->unique(['tenant_id', 'code'], 'tenant_code_unique'); // Composite unique index
            $table->index('company_tag');
            $table->index('parent_id');
            $table->index('primary_servicing_office_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['company_tag']);
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['primary_servicing_office_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop indexes
            $table->dropIndex('tenant_code_unique');
            $table->dropIndex(['company_tag']);
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['primary_servicing_office_id']);
        });

        // Drop the table
        Schema::dropIfExists('customers');
    }
}
