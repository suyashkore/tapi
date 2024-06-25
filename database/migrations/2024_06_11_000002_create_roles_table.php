<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->smallIncrements('id'); // Primary key: unsigned small integer, auto-increment

            // Foreign key from tenants table, nullable
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            // Role details
            $table->string('name', 24); // Role name
            $table->string('description', 255)->nullable(); // Description

            // Timestamps
            $table->timestamps(); // Created at and updated at

            // Indexes
            $table->index('tenant_id');
            $table->index('name');

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
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['name']);
        });
        Schema::dropIfExists('roles');
    }
}
