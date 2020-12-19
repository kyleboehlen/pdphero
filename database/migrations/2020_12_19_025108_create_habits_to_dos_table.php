<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHabitsToDosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('habits_to_dos', function (Blueprint $table) {
            // Compound PK
            $table->bigInteger('habit_id')->unsigned();
            $table->bigInteger('to_do_id')->unsigned();

            // Constraints
            $table->foreign('habit_id')->references('id')->on('habits');
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
        Schema::dropIfExists('habits_to_dos');
    }
}
