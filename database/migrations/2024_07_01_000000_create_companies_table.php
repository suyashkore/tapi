<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->smallIncrements('id'); // Primary key: unsigned small integer, auto-increment

            // Foreign key from tenants table, nullable
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            // Company details
            $table->string('code', 16); // Mandatory unique code for a given tenant
            $table->string('name', 64); // Company name
            $table->string('name_reg', 128)->nullable(); // Name in regional language
            $table->string('address', 255); // Address
            $table->string('address_reg', 512)->nullable(); // Address in regional language
            $table->string('phone1', 16)->nullable(); // Phone 1
            $table->string('phone2', 16)->nullable(); // Phone 2
            $table->string('email1', 64)->nullable(); // Email 1
            $table->string('email2', 64)->nullable(); // Email 2
            $table->string('website', 128)->nullable(); // Website
            $table->string('gst_num', 16)->nullable(); // GST number
            $table->string('cin_num', 24)->nullable(); // CIN number
            $table->string('msme_num', 24)->nullable(); // MSME number
            $table->string('pan_num', 16)->nullable(); // PAN number
            $table->string('tan_num', 16)->nullable(); // TAN number
            $table->string('logo_url', 255)->nullable(); // Logo URL
            $table->boolean('active')->default(true); // Active status
            $table->unsignedSmallInteger('seq_num'); // Mandatory sequence number

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
        Schema::table('companies', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop unique constraints
            $table->dropUnique('tenant_code_unique');
        });

        Schema::dropIfExists('companies');
    }
}
