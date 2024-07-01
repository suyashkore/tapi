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
            $table->string('id', 16)->primary();
            $table->unsignedSmallInteger('tenant_id');
            $table->string('parent_id', 16)->nullable();
            $table->string('name', 128);
            $table->string('name_reg', 255)->nullable();
            $table->json('payment_types');
            $table->string('industry_type', 128)->nullable();
            $table->string('customer_type', 24)->nullable();
            $table->string('pan', 16)->nullable();
            $table->string('gst_num', 16)->nullable();
            $table->string('cin_num', 24)->nullable();
            $table->string('country', 64)->nullable();
            $table->string('state', 64)->nullable();
            $table->string('district', 64)->nullable();
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
            $table->json('other_servicing_offices')->nullable();
            $table->string('primary_servicing_office', 16);
            $table->dateTime('erp_entry_date')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('primary_servicing_office')->references('id')->on('offices')->onDelete('cascade');
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
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['primary_servicing_office']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropIndex(['tenant_id']);
        });

        Schema::dropIfExists('customers');
    }
}
