<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCpKycTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cp_kyc', function (Blueprint $table) {
            $table->mediumIncrements('id'); // Primary key: unsigned medium integer, auto-increment

            // Foreign key from tenants table, nullable
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            // Foreign key from companies table, nullable
            $table->string('company_tag', 16)->nullable();
            $table->foreign('company_tag')->references('code')->on('companies')->onDelete('set null');

            // KYC details
            $table->string('legal_name', 128); // Legal name
            $table->string('owner1_name', 48); // Owner1 name
            $table->string('photo1_url', 255)->nullable(); // Photo1 URL
            $table->string('owner1_aadhaar', 16)->nullable(); // Owner1 Aadhaar
            $table->string('owner1_aadhaar_url', 255)->nullable(); // Owner1 Aadhaar URL
            $table->string('owner1_pan', 16)->nullable(); // Owner1 PAN
            $table->string('owner1_pan_url', 255)->nullable(); // Owner1 PAN URL
            $table->string('owner1_email', 64)->nullable(); // Owner1 email
            $table->string('owner1_mobile', 16)->nullable(); // Owner1 mobile
            $table->string('owner2_name', 48)->nullable(); // Owner2 name
            $table->string('photo2_url', 255)->nullable(); // Photo2 URL
            $table->string('owner2_aadhaar', 16)->nullable(); // Owner2 Aadhaar
            $table->string('owner2_aadhaar_url', 255)->nullable(); // Owner2 Aadhaar URL
            $table->string('owner2_pan', 16)->nullable(); // Owner2 PAN
            $table->string('owner2_pan_url', 255)->nullable(); // Owner2 PAN URL
            $table->string('owner2_email', 64)->nullable(); // Owner2 email
            $table->string('owner2_mobile', 16)->nullable(); // Owner2 mobile
            $table->string('country', 64)->nullable(); // Country
            $table->string('state', 64)->nullable(); // State
            $table->string('district', 64)->nullable(); // District
            $table->string('taluka', 64)->nullable(); // Taluka
            $table->string('city', 64)->nullable(); // City
            $table->string('pincode', 16); // Pincode
            $table->string('latitude', 16)->nullable(); // Latitude
            $table->string('longitude', 16)->nullable(); // Longitude
            $table->string('address', 255); // Address
            $table->string('address_reg', 512)->nullable(); // Address in regional language
            $table->string('addr_doc_url', 255)->nullable(); // Address document URL
            $table->string('gst_num', 16)->nullable(); // GST number
            $table->string('gst_cert_url', 255)->nullable(); // GST certificate URL
            $table->string('cin_num', 24)->nullable(); // CIN number
            $table->string('company_reg_cert_url', 255)->nullable(); // Company registration certificate URL
            $table->string('pan_num', 16)->nullable(); // PAN number
            $table->string('pan_card_url', 255)->nullable(); // PAN card URL
            $table->string('tan_num', 16)->nullable(); // TAN number
            $table->string('tan_card_url', 255)->nullable(); // TAN card URL
            $table->string('msme_num', 24)->nullable(); // MSME number
            $table->string('msme_reg_cert_url', 255)->nullable(); // MSME registration certificate URL
            $table->string('aadhaar_num', 16)->nullable(); // Aadhaar number
            $table->string('aadhaar_card_url', 255)->nullable(); // Aadhaar card URL
            $table->string('bank1_name', 32)->nullable(); // Bank1 name
            $table->string('bank1_accnt_holder', 32)->nullable(); // Bank1 account holder
            $table->string('bank1_account_type', 24)->nullable(); // Bank1 account type
            $table->string('bank1_account_num', 24)->nullable(); // Bank1 account number
            $table->string('bank1_ifsc_code', 16)->nullable(); // Bank1 IFSC code
            $table->string('bank1_doc_url', 255)->nullable(); // Bank1 document URL
            $table->string('bank2_name', 32)->nullable(); // Bank2 name
            $table->string('bank2_accnt_holder', 32)->nullable(); // Bank2 account holder
            $table->string('bank2_account_type', 24)->nullable(); // Bank2 account type
            $table->string('bank2_account_num', 24)->nullable(); // Bank2 account number
            $table->string('bank2_ifsc_code', 16)->nullable(); // Bank2 IFSC code
            $table->string('bank2_doc_url', 255)->nullable(); // Bank2 document URL
            $table->dateTime('date_of_reg')->nullable(); // Date of registration
            $table->string('doc1_name', 48)->nullable(); // Document1 name
            $table->string('doc1_url', 255)->nullable(); // Document1 URL
            $table->dateTime('doc1_date')->nullable(); // Document1 date
            $table->string('doc2_name', 48)->nullable(); // Document2 name
            $table->string('doc2_url', 255)->nullable(); // Document2 URL
            $table->dateTime('doc2_date')->nullable(); // Document2 date
            $table->string('doc3_name', 48)->nullable(); // Document3 name
            $table->string('doc3_url', 255)->nullable(); // Document3 URL
            $table->dateTime('doc3_date')->nullable(); // Document3 date
            $table->string('doc4_name', 48)->nullable(); // Document4 name
            $table->string('doc4_url', 255)->nullable(); // Document4 URL
            $table->dateTime('doc4_date')->nullable(); // Document4 date
            $table->string('key_personnel1_name', 48)->nullable(); // Key personnel1 name
            $table->string('key_personnel1_job_title', 48)->nullable(); // Key personnel1 job title
            $table->string('key_personnel1_mobile', 16)->nullable(); // Key personnel1 mobile
            $table->string('key_personnel1_email', 64)->nullable(); // Key personnel1 email
            $table->string('key_personnel2_name', 48)->nullable(); // Key personnel2 name
            $table->string('key_personnel2_job_title', 48)->nullable(); // Key personnel2 job title
            $table->string('key_personnel2_mobile', 16)->nullable(); // Key personnel2 mobile
            $table->string('key_personnel2_email', 64)->nullable(); // Key personnel2 email
            $table->string('key_personnel3_name', 48)->nullable(); // Key personnel3 name
            $table->string('key_personnel3_job_title', 48)->nullable(); // Key personnel3 job title
            $table->string('key_personnel3_mobile', 16)->nullable(); // Key personnel3 mobile
            $table->string('key_personnel3_email', 64)->nullable(); // Key personnel3 email
            $table->string('key_personnel4_name', 48)->nullable(); // Key personnel4 name
            $table->string('key_personnel4_job_title', 48)->nullable(); // Key personnel4 job title
            $table->string('key_personnel4_mobile', 16)->nullable(); // Key personnel4 mobile
            $table->string('key_personnel4_email', 64)->nullable(); // Key personnel4 email
            $table->dateTime('kyc_date')->nullable(); // KYC date
            $table->boolean('kyc_completed')->default(false); // KYC completion status
            $table->boolean('active')->default(true); // Active status
            $table->string('status', 24); // Status
            $table->string('note', 255)->nullable(); // Note

            // Foreign keys for created_by and updated_by
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('tenant_id');
            $table->index('company_tag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cp_kyc', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['company_tag']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop indexes
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['company_tag']);
        });

        // Drop the table
        Schema::dropIfExists('cp_kyc');
    }
}
