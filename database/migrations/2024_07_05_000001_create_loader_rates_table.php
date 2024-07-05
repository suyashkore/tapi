<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoaderRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loader_rates', function (Blueprint $table) {
            $table->mediumIncrements('id'); // Primary key: unsigned medium integer, auto-increment
            $table->unsignedSmallInteger('tenant_id')->nullable(); // Foreign key from tenants table
            $table->unsignedMediumInteger('contracting_office_id'); // Foreign key from offices table
            $table->unsignedMediumInteger('vendor_id')->nullable(); // Foreign key from vendors table
            $table->string('vendor_name', 128); // Name of the Loader vendor
            $table->string('default_rate_type', 24); // Rate type
            $table->float('reg_pkg_rate')->default(0); // Regular package rate
            $table->float('crossing_pkg_rate')->default(0); // Crossing package rate
            $table->float('reg_weight_rate')->default(0); // Regular weight rate
            $table->float('crossing_weight_rate')->default(0); // Crossing weight rate
            $table->float('monthly_sal')->default(0); // Monthly salary
            $table->float('daily_allowance')->default(0); // Daily allowance
            $table->float('daily_wage')->default(0); // Daily wage
            $table->unsignedMediumInteger('daily_wage_pkg_capping')->nullable()->default(0); // Daily wage package capping
            $table->unsignedMediumInteger('daily_wage_weight_capping')->nullable()->default(0); // Daily wage weight capping
            $table->float('overtime_hourly_rate')->nullable()->default(0); // Overtime hourly rate
            $table->boolean('active')->default(true); // Active status
            $table->string('status', 24); // Status
            $table->string('note', 255)->nullable(); // Note
            $table->dateTime('start_date'); // Start date
            $table->dateTime('end_date'); // End date
            $table->unsignedMediumInteger('created_by')->nullable(); // Foreign key from users table
            $table->unsignedMediumInteger('updated_by')->nullable(); // Foreign key from users table
            $table->timestamps(); // Created at and updated at

            // Foreign key constraints
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');
            $table->foreign('contracting_office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('tenant_id');
            $table->index('contracting_office_id');
            $table->index('vendor_id');
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
        Schema::dropIfExists('loader_rates');
    }
}
