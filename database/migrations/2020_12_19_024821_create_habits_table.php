<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHabitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('habits', function (Blueprint $table) {
            // Pk
            $table->id();

            // Laravel Columns
            $table->timestamps();
            $table->softDeletes();

            // Other columns
            $table->bigInteger('user_id')->unsigned(); // User the habit belongs to
            $table->tinyInteger('type_id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->tinyInteger('strength')->default(0); // To store cached calculated habit strenth %
            $table->tinyInteger('times_daily')->default(1); // How many times daily habit should be completed
            $table->json('days_of_week')->nullable(); // Stores the days of the week the habit should be performed
            $table->tinyInteger('every_x_days')->nullable(); // Or it this will store how many days in between doing said habit
            $table->boolean('show_todo')->default(0); // Whether or not to automatically create to do items for habit

            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('habits');
    }
}
