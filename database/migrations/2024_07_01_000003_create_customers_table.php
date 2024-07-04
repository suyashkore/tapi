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
            // Primary key
            $table->increments('id');

            // Foreign key from tenants table
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            // Foreign key from companies table
            $table->string('company_tag', 16)->nullable();
            $table->foreign('company_tag')->references('code')->on('companies')->onDelete('set null');

            // Foreign key from self table
            $table->string('parent_id', 16)->nullable();
            $table->foreign('parent_id')->references('id')->on('customers')->onDelete('set null');

            // Customer details
            $table->string('code', 16);
            $table->string('name', 128);
            $table->string('name_reg', 255)->nullable();
            $table->json('payment_types');
            $table->string('industry_type', 128)->nullable();
            $table->string('c_type', 16);
            $table->string('c_subtype', 24)->nullable();
            $table->string('pan_num', 16)->nullable();
            $table->string('gst_num', 16)->nullable();
            $table->string('country', 64)->nullable();
            $table->string('state', 64)->nullable();
            $table->string('district', 64)->nullable();
            $table->string('taluka', 64)->nullable();
            $table->string('city', 64);
            $table->string('pincode', 16);
            $table->string('latitude', 16)->nullable();
            $table->string('longitude', 16)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('address_reg', 512)->nullable();
            $table->string('mobile', 16)->nullable();
            $table->string('tel_num', 16)->nullable();
            $table->string('email', 64)->nullable();
            $table->string('billing_contact_person', 48)->nullable();
            $table->string('billing_mobile', 16);
            $table->string('billing_email', 64);
            $table->string('billing_address', 255);
            $table->string('billing_address_reg', 512)->nullable();
            $table->string('primary_servicing_office', 16);
            $table->json('other_servicing_offices')->nullable();
            $table->dateTime('erp_entry_date')->nullable();
            $table->boolean('active')->default(true);

            // Foreign keys for created_by and updated_by
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamps();

            // Unique constraints
            $table->unique(['tenant_id', 'code']);
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
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop unique constraints
            $table->dropUnique(['tenant_id', 'code']);
        });

        // Drop the table
        Schema::dropIfExists('customers');
    }
}
