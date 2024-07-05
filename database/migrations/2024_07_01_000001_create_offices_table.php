<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->mediumIncrements('id'); // Primary key: unsigned medium integer, auto-increment

            // Foreign key from tenants table, nullable
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            // Foreign key from companies table, nullable
            $table->string('company_tag', 16)->nullable();
            $table->foreign('company_tag')->references('code')->on('companies')->onDelete('set null');

            // Office details
            $table->string('code', 16); // Mandatory unique code for a given tenant
            $table->string('name', 64); // Office name
            $table->string('name_reg', 128)->nullable(); // Name in regional language
            $table->string('gst_num', 16)->nullable(); // GST number
            $table->string('cin_num', 24)->nullable(); // CIN number
            $table->boolean('owned')->default(true); // Owned status
            $table->string('o_type', 24); // Office type
            $table->unsignedMediumInteger('cp_kyc_id')->nullable(); // Foreign key from cp_kyc table
            $table->foreign('cp_kyc_id')->references('id')->on('cp_kyc')->onDelete('set null');
            $table->string('country', 64)->nullable(); // Country
            $table->string('state', 64)->nullable(); // State
            $table->string('district', 64)->nullable(); // District
            $table->string('taluka', 64)->nullable(); // Taluka
            $table->string('city', 64)->nullable(); // City
            $table->string('pincode', 16); // Pincode
            $table->string('latitude', 16); // Latitude
            $table->string('longitude', 16); // Longitude
            $table->string('address', 255); // Address
            $table->string('address_reg', 512)->nullable(); // Address in regional language
            $table->boolean('active')->default(true); // Active status
            $table->string('description', 255)->nullable(); // Description
            $table->unsignedMediumInteger('parent_id')->nullable(); // Foreign key from self table offices
            $table->foreign('parent_id')->references('id')->on('offices')->onDelete('set null');

            // Foreign keys for created_by and updated_by
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->unique(['tenant_id', 'code'], 'tenant_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offices', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['company_tag']);
            $table->dropForeign(['cp_kyc_id']);
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop unique constraints
            $table->dropUnique('tenant_code_unique');
        });

        Schema::dropIfExists('offices');
    }
}
