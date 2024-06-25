<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPrivilegesTableAddCreatedByAndUpdatedBy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('privileges', function (Blueprint $table) {
            // Add created_by and updated_by fields
            $table->unsignedMediumInteger('created_by')->nullable()->after('description');
            $table->unsignedMediumInteger('updated_by')->nullable()->after('created_by');

            // Add foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Add indexes for faster queries if deemed necessary
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
        Schema::table('privileges', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop the columns
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');

            // Drop indexes
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
        });
    }
}
