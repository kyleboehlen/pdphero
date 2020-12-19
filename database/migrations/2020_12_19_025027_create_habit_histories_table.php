<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHabitHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('habit_histories', function (Blueprint $table) {
            // Pk
            $table->id();

            // Laravel Columns
            $table->timestamps();
            
            // Other columns
            $table->tinyInteger('type_id')->unsigned();
            $table->date('day');
            $table->tinyInteger('times')->default(1);
            $table->text('notes')->nullable();

            // Constraints
            $table->foreign('type_id')->references('id')->on('habit_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('habit_histories');
    }
}
