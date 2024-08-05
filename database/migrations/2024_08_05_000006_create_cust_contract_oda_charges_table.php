<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCustContractOdaChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cust_contract_oda_charges', function (Blueprint $table) {
            // Primary key: unsigned big integer, auto-increment
            $table->unsignedBigInteger('id')->autoIncrement();

            // Foreign key from tenants table, mandatory
            $table->unsignedSmallInteger('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Foreign key from cust_contracts table, mandatory
            $table->unsignedBigInteger('cust_contract_id');
            $table->foreign('cust_contract_id')->references('id')->on('cust_contracts')->onDelete('cascade');

            // Mandatory fields
            $table->string('ctr_num', 24); // Populated from cust_contracts
            $table->string('from_place', 64);
            $table->string('to_place', 64);
            $table->float('rate');

            // Foreign key from station_coverage table, nullable
            $table->unsignedBigInteger('from_place_id')->nullable();
            $table->foreign('from_place_id')->references('id')->on('station_coverage')->onDelete('set null');

            $table->unsignedBigInteger('to_place_id')->nullable();
            $table->foreign('to_place_id')->references('id')->on('station_coverage')->onDelete('set null');

            // Foreign key references for created_by and updated_by fields, nullable
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps for created_at and updated_at fields, auto-populated by the database
            $table->timestamps();

            // Indexes for faster queries
            $table->index('tenant_id');
            $table->index('cust_contract_id');
            $table->index('from_place_id');
            $table->unique(['cust_contract_id', 'from_place_id', 'to_place'], 'unique_contract_place');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cust_contract_oda_charges', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['cust_contract_id']);
            $table->dropForeign(['from_place_id']);
            $table->dropForeign(['to_place_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop indexes
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['cust_contract_id']);
            $table->dropIndex(['from_place_id']);
            $table->dropIndex('unique_contract_place');
        });

        // Drop the table
        Schema::dropIfExists('cust_contract_oda_charges');
    }
}
