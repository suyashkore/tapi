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
            // Define columns
            $table->unsignedMediumInteger('user_id'); // Reference to 'users' table
            $table->string('otp_hash', 255); // Hashed OTP value
            $table->dateTime('expires_at')->nullable(); // Expiration time of OTP
            $table->unsignedTinyInteger('failed_otp_login_attempts')->default(0); // Count of failed OTP login attempts
            $table->dateTime('otp_login_blocked_till')->nullable(); // Time till OTP login is blocked
            $table->unsignedMediumInteger('created_by')->nullable(); // Reference to 'users' table for created_by
            $table->unsignedMediumInteger('updated_by')->nullable(); // Reference to 'users' table for updated_by
            $table->timestamps(); // 'created_at' and 'updated_at' columns

            // Define primary key
            $table->primary(['user_id', 'otp_hash']);

            // Define foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Add indexes for faster queries
            $table->index('user_id');
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
        Schema::table('user_otps', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['user_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop indexes
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
        });

        // Drop the table
        Schema::dropIfExists('user_otps');
    }
}
