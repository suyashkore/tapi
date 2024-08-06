<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCustContractExcessWeightRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cust_contract_excess_weight_rates', function (Blueprint $table) {
            // Primary key: unsigned big integer, auto-increment
            $table->unsignedBigInteger('id')->autoIncrement();

            // Foreign key from tenants table, mandatory
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Foreign key from cust_contracts table, mandatory
            $table->unsignedBigInteger('cust_contract_id');
            $table->foreign('cust_contract_id')->references('id')->on('cust_contracts')->onDelete('cascade');

            // Contract number, will be populated from cust_contracts
            $table->string('ctr_num', 24);

            // Mandatory fields for excess weight rate
            $table->float('lower_limit');
            $table->float('upper_limit');
            $table->float('rate');

            // Foreign key references for created_by and updated_by fields, nullable
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps for created_at and updated_at fields, auto-populated by the database
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
        Schema::table('cust_contract_excess_weight_rates', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['cust_contract_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        // Drop the table
        Schema::dropIfExists('cust_contract_excess_weight_rates');
    }
}
