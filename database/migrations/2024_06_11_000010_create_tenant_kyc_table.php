<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantKycTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_kyc', function (Blueprint $table) {
            // Primary key
            $table->smallIncrements('id');

            // Foreign key from tenants table
            $table->unsignedSmallInteger('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // KYC details
            $table->string('legal_name', 128);
            $table->string('owner1_name', 48);
            $table->string('photo1_url', 255)->nullable();
            $table->string('owner1_aadhaar', 16)->nullable();
            $table->string('owner1_aadhaar_url', 255)->nullable();
            $table->string('owner1_pan', 16)->nullable();
            $table->string('owner1_pan_url', 255)->nullable();
            $table->string('owner1_email', 64)->nullable();
            $table->string('owner1_mobile', 16)->nullable();
            $table->string('owner2_name', 48)->nullable();
            $table->string('photo2_url', 255)->nullable();
            $table->string('owner2_aadhaar', 16)->nullable();
            $table->string('owner2_aadhaar_url', 255)->nullable();
            $table->string('owner2_pan', 16)->nullable();
            $table->string('owner2_pan_url', 255)->nullable();
            $table->string('owner2_email', 64)->nullable();
            $table->string('owner2_mobile', 16)->nullable();
            $table->string('country', 64)->nullable();
            $table->string('state', 64)->nullable();
            $table->string('district', 64)->nullable();
            $table->string('taluka', 64)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('pincode', 16);
            $table->string('latitude', 16)->nullable();
            $table->string('longitude', 16)->nullable();
            $table->string('address', 255);
            $table->string('address_reg', 512)->nullable();
            $table->string('addr_doc_url', 255)->nullable();
            $table->string('gst_num', 16)->nullable();
            $table->string('gst_cert_url', 255)->nullable();
            $table->string('cin_num', 24)->nullable();
            $table->string('company_reg_cert_url', 255)->nullable();
            $table->string('pan_num', 16)->nullable();
            $table->string('pan_card_url', 255)->nullable();
            $table->string('tan_num', 16)->nullable();
            $table->string('tan_card_url', 255)->nullable();
            $table->string('msme_num', 24)->nullable();
            $table->string('msme_reg_cert_url', 255)->nullable();
            $table->string('aadhaar_num', 16)->nullable();
            $table->string('aadhaar_card_url', 255)->nullable();
            $table->string('bank1_name', 32)->nullable();
            $table->string('bank1_accnt_holder', 32)->nullable();
            $table->string('bank1_account_type', 24)->nullable();
            $table->string('bank1_account_num', 24)->nullable();
            $table->string('bank1_ifsc_code', 16)->nullable();
            $table->string('bank1_doc_url', 255)->nullable();
            $table->string('bank2_name', 32)->nullable();
            $table->string('bank2_accnt_holder', 32)->nullable();
            $table->string('bank2_account_type', 24)->nullable();
            $table->string('bank2_account_num', 24)->nullable();
            $table->string('bank2_ifsc_code', 16)->nullable();
            $table->string('bank2_doc_url', 255)->nullable();
            $table->string('default_bank', 8)->nullable();
            $table->dateTime('date_of_reg')->nullable();
            $table->string('doc1_name', 48)->nullable();
            $table->string('doc1_url', 255)->nullable();
            $table->dateTime('doc1_date')->nullable();
            $table->string('doc2_name', 48)->nullable();
            $table->string('doc2_url', 255)->nullable();
            $table->dateTime('doc2_date')->nullable();
            $table->string('doc3_name', 48)->nullable();
            $table->string('doc3_url', 255)->nullable();
            $table->dateTime('doc3_date')->nullable();
            $table->string('doc4_name', 48)->nullable();
            $table->string('doc4_url', 255)->nullable();
            $table->dateTime('doc4_date')->nullable();
            $table->string('key_personnel1_name', 48)->nullable();
            $table->string('key_personnel1_job_title', 48)->nullable();
            $table->string('key_personnel1_mobile', 16)->nullable();
            $table->string('key_personnel1_email', 64)->nullable();
            $table->string('key_personnel2_name', 48)->nullable();
            $table->string('key_personnel2_job_title', 48)->nullable();
            $table->string('key_personnel2_mobile', 16)->nullable();
            $table->string('key_personnel2_email', 64)->nullable();
            $table->string('key_personnel3_name', 48)->nullable();
            $table->string('key_personnel3_job_title', 48)->nullable();
            $table->string('key_personnel3_mobile', 16)->nullable();
            $table->string('key_personnel3_email', 64)->nullable();
            $table->string('key_personnel4_name', 48)->nullable();
            $table->string('key_personnel4_job_title', 48)->nullable();
            $table->string('key_personnel4_mobile', 16)->nullable();
            $table->string('key_personnel4_email', 64)->nullable();
            $table->dateTime('kyc_date')->nullable();
            $table->boolean('kyc_completed')->default(false);
            $table->boolean('active')->default(true);
            $table->string('status', 24);
            $table->string('note', 255)->nullable();

            // Foreign keys for created_by and updated_by
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenant_kyc', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::dropIfExists('tenant_kyc');
    }
}
