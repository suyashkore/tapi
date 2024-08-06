<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_otps', function (Blueprint $table) {
            // Primary key: unsigned medium integer, not auto-incrementing, foreign key from users table
            $table->unsignedMediumInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Foreign key from tenants table, nullable
            $table->unsignedSmallInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');

            // Mandatory fields
            $table->string('login_id', 24); // Value fetched from users table
            $table->string('otp_hash', 255); // Hashed OTP value

            // Nullable fields
            $table->dateTime('expires_at')->nullable();
            $table->unsignedTinyInteger('failed_otp_login_attempts')->default(0);
            $table->dateTime('otp_login_blocked_till')->nullable();

            // Foreign key references for created_by and updated_by fields, nullable
            $table->unsignedMediumInteger('created_by')->nullable();
            $table->unsignedMediumInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Timestamps for created_at and updated_at fields, auto-populated by the database
            $table->timestamps();

            // Indexes for faster queries
            $table->unique('user_id');
            $table->index('tenant_id');
            $table->index('login_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_otps', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['user_id']);
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop indexes
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['login_id']);
        });

        // Drop the table
        Schema::dropIfExists('user_otps');
    }
}
