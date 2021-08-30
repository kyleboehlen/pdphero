<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHabitRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('habit_reminders', function (Blueprint $table) {
            $table->id(); // PK

            // Laravel columns
            $table->timestamps();

            // Other columns
            $table->uuid('uuid')->unique();
            $table->bigInteger('habit_id')->unsigned();
            $table->time('remind_at');

            // Constraints
            $table->foreign('habit_id')->references('id')->on('habits');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('habit_reminders');
    }
}
