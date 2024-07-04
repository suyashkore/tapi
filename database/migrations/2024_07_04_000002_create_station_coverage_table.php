<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStationCoverageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('station_coverage', function (Blueprint $table) {
            // Primary key
            $table->unsignedBigInteger('id')->autoIncrement();

            // Foreign key from tenants table
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            // Station coverage details
            $table->string('name', 64); // Mandatory station name
            $table->string('name_reg', 128)->nullable(); // Name in regional language
            $table->string('post_name', 64)->nullable(); // Post name
            $table->string('post_name_reg', 64)->nullable(); // Post name in regional language
            $table->string('pincode', 16); // Mandatory pincode
            $table->string('taluka', 32)->nullable(); // Taluka
            $table->string('taluka_reg', 128)->nullable(); // Taluka in regional language
            $table->string('district', 32); // Mandatory district
            $table->string('district_reg', 128)->nullable(); // District in regional language
            $table->string('state', 24); // Mandatory state
            $table->string('state_reg', 128)->nullable(); // State in regional language
            $table->string('country', 24); // Mandatory country
            $table->string('latitude', 16); // Mandatory latitude
            $table->string('longitude', 16); // Mandatory longitude

            // Foreign key from offices table
            $table->unsignedMediumInteger('servicing_office_id');
            $table->foreign('servicing_office_id')->references('id')->on('offices')->onDelete('cascade');

            // Additional details
            $table->unsignedTinyInteger('service_office_tat')->nullable(); // Number of days
            $table->unsignedMediumInteger('servicing_office_dist')->nullable(); // Distance in kms
            $table->string('name_gmap', 64)->nullable(); // Name as in google maps
            $table->string('zone', 16)->nullable(); // Zone
            $table->string('route_num', 16)->nullable(); // Route number
            $table->unsignedSmallInteger('route_sequence')->nullable(); // Route sequence
            $table->boolean('oda')->default(false); // Out of delivery area flag

            // Nearby highways
            $table->string('nr_state_highway', 16)->nullable(); // Nearby state highway
            $table->string('nr_national_highway', 16)->nullable(); // Nearby national highway

            // Status and activity
            $table->boolean('active')->default(true); // Active status
            $table->string('status', 24); // Mandatory status
            $table->string('note', 255)->nullable(); // Additional note

            // Foreign key from users table
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamps(); // created_at and updated_at columns

            // Indexes for faster queries
            $table->index('tenant_id');
            $table->index('pincode');
            $table->index('district');
            $table->index('state');
            $table->index('country');
            $table->index('servicing_office_id');
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('station_coverage', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['servicing_office_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop indexes
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['pincode']);
            $table->dropIndex(['district']);
            $table->dropIndex(['state']);
            $table->dropIndex(['country']);
            $table->dropIndex(['servicing_office_id']);
            $table->dropIndex(['active']);
        });

        // Drop the table
        Schema::dropIfExists('station_coverage');
    }
}
