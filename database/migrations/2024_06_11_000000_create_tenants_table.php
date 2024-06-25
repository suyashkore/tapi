<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->smallIncrements('id'); // Primary key: unsigned small integer, auto-increment

            // Tenant details
            $table->string('name', 128); // Tenant name
            $table->string('country', 64)->nullable(); // Country
            $table->string('state', 64)->nullable(); // State
            $table->string('city', 64)->nullable(); // City
            $table->string('pincode', 16); // Pincode
            $table->string('address', 255)->nullable(); // Address
            $table->string('latitude', 16); // Latitude
            $table->string('longitude', 16); // Longitude
            $table->string('logo_url', 255)->nullable(); // Logo URL
            $table->string('description', 255)->nullable(); // Description
            $table->boolean('active')->default(true); // Active status

            // Timestamps
            $table->timestamps(); // Created at and updated at

            // Indexes
            $table->index('name');
            $table->index('country');
            $table->index('state');
            $table->index('city');
            $table->index('pincode');

            // Foreign keys for created_by and updated_by will be added in a later migration
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['country']);
            $table->dropIndex(['state']);
            $table->dropIndex(['city']);
            $table->dropIndex(['pincode']);
        });

        Schema::dropIfExists('tenants');
    }
}
