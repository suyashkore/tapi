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
            $table->id();
            $table->unsignedSmallInteger('tenant_id');
            $table->string('office_id', 16)->nullable();
            $table->string('legal_name', 64);
            $table->string('gst_num', 16)->nullable();
            $table->string('cin_num', 24)->nullable();
            $table->string('pan_num', 16);
            $table->string('bank_name', 32);
            $table->string('bank_account_num', 24);
            $table->string('bank_ifsc_code', 16);
            $table->string('owner_aadhaar', 16)->nullable();
            $table->string('owner_pan', 16)->nullable();
            $table->string('owner_photo_url', 255)->nullable();
            $table->string('owner_email', 64)->nullable();
            $table->string('owner_mobile', 16)->nullable();
            $table->string('finance_head_email', 64)->nullable();
            $table->string('finance_head_mobile', 16)->nullable();
            $table->boolean('kyc_completed')->default(false);
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('office_id')->references('id')->on('offices')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['tenant_id']);
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
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['office_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropIndex(['tenant_id']);
        });

        Schema::dropIfExists('cp_kyc');
    }
}
