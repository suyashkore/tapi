<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePrivilegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_privileges', function (Blueprint $table) {
            // Composite primary key: role_id and privilege_id
            $table->unsignedSmallInteger('role_id');
            $table->unsignedSmallInteger('privilege_id');

            // Timestamps
            $table->timestamps();

            // Foreign key from roles table
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            // Foreign key from privileges table
            $table->foreign('privilege_id')->references('id')->on('privileges')->onDelete('cascade');

            // Composite primary key definition
            $table->primary(['role_id', 'privilege_id']);

            // Indexes
            $table->index('role_id');
            $table->index('privilege_id');

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
        Schema::table('role_privileges', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['privilege_id']);
            $table->dropIndex(['role_id']);
            $table->dropIndex(['privilege_id']);
        });
        Schema::dropIfExists('role_privileges');
    }
}
