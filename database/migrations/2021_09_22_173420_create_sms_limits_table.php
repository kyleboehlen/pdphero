<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSMSLimitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_limits', function (Blueprint $table) {
            $table->id(); // PK
            $table->timestamps(); // Laravel columns

            // Other columns
            $table->bigInteger('user_id')->unsigned();
            $table->tinyInteger('month')->unsigned();
            $table->smallInteger('year')->unsigned();
            $table->smallInteger('sent')->unsigned()->default($value = 0);
            $table->boolean('notify_trial')->default($value = 0);
            $table->boolean('notify_basic')->default($value = 0);
            $table->boolean('notify_black_label')->default($value = 0);
            
            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_limits');
    }
}
