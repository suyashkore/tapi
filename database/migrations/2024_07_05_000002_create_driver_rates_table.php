<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_rates', function (Blueprint $table) {
            $table->mediumIncrements('id'); // Primary key: unsigned medium integer, auto-increment
            $table->unsignedSmallInteger('tenant_id')->nullable(); // Foreign key from tenants table
            $table->unsignedMediumInteger('contracting_office_id'); // Foreign key from offices table
            $table->unsignedMediumInteger('vendor_id')->nullable(); // Foreign key from vendors table
            $table->string('vendor_name', 128); // Driver's name
            $table->string('default_rate_type', 24); // Default rate type
            $table->float('daily_rate')->nullable()->default(0); // Daily rate
            $table->float('hourly_rate')->nullable()->default(0); // Hourly rate
            $table->float('overtime_hourly_rate')->nullable()->default(0); // Overtime hourly rate
            $table->float('daily_allowance')->nullable()->default(0); // Daily allowance
            $table->float('per_km_rate')->nullable()->default(0); // Per kilometer rate
            $table->float('per_extra_km_rate')->nullable()->default(0); // Per extra kilometer rate
            $table->float('night_halt_rate')->nullable()->default(0); // Night halt rate
            $table->float('per_trip_rate')->nullable()->default(0); // Per trip rate
            $table->float('trip_allowance')->nullable()->default(0); // Trip allowance
            $table->float('incentive_per_trip')->nullable()->default(0); // Incentive per trip
            $table->float('monthly_sal')->nullable()->default(0); // Monthly salary
            $table->float('monthly_incentive')->nullable()->default(0); // Monthly incentive
            $table->float('per_trip_penalty_percent')->nullable()->default(0); // Per trip penalty percent
            $table->float('per_trip_penalty_fixed_amount')->nullable()->default(0); // Per trip penalty fixed amount
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
        Schema::dropIfExists('driver_rates');
    }
}
