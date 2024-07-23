<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeoHierarchyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geo_hierarchy', function (Blueprint $table) {
            // Primary key
            $table->mediumIncrements('id');

            // Geo hierarchy fields
            $table->string('country', 64);
            $table->string('state', 64);
            $table->string('district', 64);
            $table->string('taluka', 64);
            $table->string('po_name', 64);
            $table->string('pincode', 16);
            $table->string('po_lat', 16);
            $table->string('po_long', 16);
            $table->string('place', 64)->nullable();
            $table->string('place_lat', 16)->nullable();
            $table->string('place_long', 16)->nullable();

            // Foreign key constraints
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['country', 'state', 'district', 'taluka', 'po_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geo_hierarchy');
    }
}
