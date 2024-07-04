<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedSmallInteger('tenant_id')->nullable(); // Foreign key from tenant
            $table->string('company_tag', 16)->nullable(); // Foreign key from company
            $table->string('code', 24); // Vendor code
            $table->string('name', 128); // Vendor name
            $table->string('name_reg', 255)->nullable(); // Name in regional language
            $table->string('legal_name', 128)->nullable(); // Legal name
            $table->string('legal_name_reg', 255)->nullable(); // Legal name in regional language
            $table->string('v_type', 24); // Vendor type
            $table->string('mobile', 16); // Mobile number
            $table->string('email', 64)->nullable(); // Email
            $table->string('contracting_office', 16); // Foreign key from office
            $table->string('erp_code', 24)->nullable(); // ERP code
            $table->boolean('active')->default(false); // Active status

            // Foreign keys for created_by and updated_by
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();

            // Timestamps
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');
            $table->foreign('contracting_office')->references('code')->on('offices')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Unique constraints
            $table->unique(['tenant_id', 'code'], 'tenant_vendor_code_unique');

            // Indexes
            $table->index('tenant_id');
            $table->index('company_tag');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['contracting_office']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropUnique('tenant_vendor_code_unique');
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['company_tag']);
            $table->dropIndex(['code']);
        });

        Schema::dropIfExists('vendors');
    }
}
