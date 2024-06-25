<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivilegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privileges', function (Blueprint $table) {
            $table->smallIncrements('id'); // Primary key: unsigned small integer, auto-increment

            // Privilege details
            $table->string('name', 48)->unique(); // Privilege name, unique
            $table->string('description', 255)->nullable(); // Description

            // Timestamps
            $table->timestamps(); // Created at and updated at

            // Indexes
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
        Schema::table('privileges', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::dropIfExists('privileges');
    }
}
