<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToDoRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_do_reminders', function (Blueprint $table) {
            $table->id(); // PK

            // Laravel columns
            $table->timestamps();

            // Other columns
            $table->uuid('uuid')->unique();
            $table->bigInteger('to_do_id')->unsigned();
            $table->timestamp('remind_at');

            // Constraints
            $table->foreign('to_do_id')->references('id')->on('to_dos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('to_do_reminders');
    }
}
