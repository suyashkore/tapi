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
            // Define columns
            $table->smallIncrements('id'); // Autoincrementing and unsigned small integer (0 to 65535)
            $table->unsignedSmallInteger('tenant_id'); // Reference to 'tenants' table
            $table->string('gst_num', 16)->nullable(); // GST number
            $table->string('cin_num', 24)->nullable(); // CIN number
            $table->string('pan_num', 16)->nullable(); // PAN number
            $table->string('bank_name', 32); // Bank name
            $table->string('bank_account_num', 24); // Bank account number
            $table->string('bank_ifsc_code', 16); // Bank IFSC code
            $table->string('owner_aadhaar', 16)->nullable(); // Owner Aadhaar number
            $table->string('owner_pan', 16)->nullable(); // Owner PAN number
            $table->string('owner_photo_url', 255)->nullable(); // URL to owner's photo
            $table->string('owner_email', 64)->nullable(); // Owner email
            $table->string('owner_mobile', 16)->nullable(); // Owner mobile number
            $table->string('finance_head_email', 64)->nullable(); // Finance head email
            $table->string('finance_head_mobile', 16)->nullable(); // Finance head mobile number
            $table->boolean('kyc_completed')->default(false); // KYC completion status
            $table->unsignedMediumInteger('created_by')->nullable(); // Reference to 'users' table for created_by
            $table->unsignedMediumInteger('updated_by')->nullable(); // Reference to 'users' table for updated_by
            $table->timestamps(); // 'created_at' and 'updated_at' columns

            // Define foreign key constraints
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Add indexes for faster queries
            $table->index('tenant_id');
            $table->index('created_by');
            $table->index('updated_by');
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
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop indexes
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
        });

        // Drop the table
        Schema::dropIfExists('tenant_kyc');
    }
}
