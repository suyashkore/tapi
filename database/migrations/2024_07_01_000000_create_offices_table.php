<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->string('id', 16)->primary();
            $table->unsignedSmallInteger('tenant_id');
            $table->string('code', 16)->nullable();
            $table->string('name', 64);
            $table->string('name_reg', 128)->nullable();
            $table->string('gst_num', 16)->nullable();
            $table->string('cin_num', 24)->nullable();
            $table->boolean('owned')->default(true);
            $table->string('o_type', 24);
            $table->string('country', 64)->nullable();
            $table->string('state', 64)->nullable();
            $table->string('district', 64)->nullable();
            $table->string('taluka', 64)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('pincode', 16);
            $table->string('latitude', 16);
            $table->string('longitude', 16);
            $table->string('address', 255);
            $table->string('address_reg', 512)->nullable();
            $table->boolean('active')->default(true);
            $table->string('description', 255)->nullable();
            $table->string('parent_id', 16)->nullable();
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('offices')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropIndex(['tenant_id']);
        });

        Schema::dropIfExists('offices');
    }
}
