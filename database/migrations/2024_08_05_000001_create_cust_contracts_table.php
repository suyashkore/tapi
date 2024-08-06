<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import the DB facade

class CreateCustContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cust_contracts', function (Blueprint $table) {
            // Primary key: unsigned big integer, auto-increment
            $table->unsignedBigInteger('id')->autoIncrement();

            // Foreign key from tenants table
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Foreign key from companies table
            $table->unsignedSmallInteger('company_tag')->nullable();
            $table->foreign('company_tag')->references('id')->on('companies')->onDelete('set null');

            // Unique contract number within a tenant
            $table->string('ctr_num', 24);
            $table->unique(['tenant_id', 'ctr_num']);

            // Foreign key from customers table
            $table->unsignedInteger('customer_group_id')->nullable();
            $table->foreign('customer_group_id')->references('id')->on('customers')->onDelete('set null');

            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            // Contract dates
            $table->dateTime('start_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('end_date')->default(DB::raw('CURRENT_TIMESTAMP'));

            // JSON fields
            $table->json('payment_type');
            $table->json('load_type');
            $table->json('distance_type');
            $table->json('rate_type');
            $table->json('pickup_delivery_mode');

            // Boolean fields with default values
            $table->boolean('excess_wt_chargeable')->default(true);
            $table->boolean('oda_del_chargeable')->default(true);

            // Integer fields with default values
            $table->unsignedTinyInteger('credit_period')->default(15);

            // Float fields with default values
            $table->float('docu_charges_per_invoice')->default(0);
            $table->float('loading_charges_per_pkg')->default(0);
            $table->float('fuel_surcharge')->default(0);
            $table->float('oda_min_del_charges')->default(0);
            $table->float('reverse_pick_up_charges')->default(0);
            $table->float('insurance_charges')->default(0);
            $table->float('minimum_chargeable_wt')->default(0);

            // Active status
            $table->boolean('active')->default(false);

            // Foreign key references for created_by and updated_by fields
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps for created_at and updated_at fields
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
        Schema::table('cust_contracts', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['company_tag']);
            $table->dropForeign(['customer_group_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        // Drop the table
        Schema::dropIfExists('cust_contracts');
    }
}
