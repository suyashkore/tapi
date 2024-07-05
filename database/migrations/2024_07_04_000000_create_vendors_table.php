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
            // Primary key: unsigned medium integer, auto-increment
            $table->unsignedMediumInteger('id')->autoIncrement();

            // Foreign key from tenants table, nullable
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            // Foreign key from companies table, nullable
            $table->unsignedSmallInteger('company_tag')->nullable();
            $table->foreign('company_tag')->references('id')->on('companies')->onDelete('set null');

            // Mandatory fields
            $table->string('code', 24); // Unique for a given tenant_id
            $table->string('name', 128);
            $table->string('v_type', 24); // Vendor type
            $table->string('mobile', 16);

            // Nullable fields
            $table->string('name_reg', 255)->nullable(); // Name in regional language
            $table->string('legal_name', 128)->nullable();
            $table->string('legal_name_reg', 255)->nullable(); // Legal name in regional language
            $table->string('email', 64)->nullable();
            $table->string('erp_code', 24)->nullable();

            // Foreign key from offices table, mandatory
            $table->unsignedMediumInteger('contracting_office_id');
            $table->foreign('contracting_office_id')->references('id')->on('offices')->onDelete('cascade');

            // Active status
            $table->boolean('active')->default(false);

            // Foreign key references for created_by and updated_by fields, nullable
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps for created_at and updated_at fields, auto-populated by the database
            $table->timestamps();

            // Indexes for faster queries
            $table->unique(['tenant_id', 'code'], 'tenant_code_unique'); // Composite unique index
            $table->index('tenant_id');
            $table->index('company_tag');
            $table->index('contracting_office_id');
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
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['company_tag']);
            $table->dropForeign(['contracting_office_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop indexes
            $table->dropUnique('tenant_code_unique');
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['company_tag']);
            $table->dropIndex(['contracting_office_id']);
        });

        // Drop the table
        Schema::dropIfExists('vendors');
    }
}
