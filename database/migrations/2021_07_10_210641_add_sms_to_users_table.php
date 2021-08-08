<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSmsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sms_number');
            $table->timestamp('sms_verified_at')->nullable();
            $table->string('sms_verify_code');
            $table->timestamp('sms_code_created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('sms_number');
            $table->dropColumn('sms_verified_at');
            $table->dropColumn('sms_verify_code');
            $table->dropColumn('sms_code_created_at');
        });
    }
}
