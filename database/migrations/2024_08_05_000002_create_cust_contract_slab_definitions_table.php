<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustContractSlabDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cust_contract_slab_definitions', function (Blueprint $table) {
            // Primary key: unsigned big integer, auto-increment
            $table->unsignedBigInteger('id')->autoIncrement();

            // Foreign key from tenants table, mandatory
            $table->unsignedSmallInteger('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Foreign key from cust_contracts table, mandatory
            $table->unsignedBigInteger('cust_contract_id');
            $table->foreign('cust_contract_id')->references('id')->on('cust_contracts')->onDelete('cascade');

            // Contract number, mandatory, will be populated from cust_contracts
            $table->string('ctr_num', 24);

            // Slab attributes
            $table->json('slab_distance_type');
            $table->string('slab_contract_type', 16);
            $table->string('slab_rate_type', 8);
            $table->string('slab_number', 8);
            $table->integer('slab_lower_limit');
            $table->integer('slab_upper_limit');

            // Foreign key references for created_by and updated_by fields, nullable
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps for created_at and updated_at fields, auto-populated by the database
            $table->timestamps();

            // Indexes for faster queries
            $table->index(['tenant_id', 'cust_contract_id']);
            $table->unique(['cust_contract_id', 'ctr_num', 'slab_number'], 'unique_contract_slab');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cust_contract_slab_definitions', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['cust_contract_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop indexes
            $table->dropIndex(['tenant_id', 'cust_contract_id']);
            $table->dropUnique(['unique_contract_slab']);
        });

        // Drop the table
        Schema::dropIfExists('cust_contract_slab_definitions');
    }
}
