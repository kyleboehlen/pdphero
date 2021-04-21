<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalActionItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goal_action_items', function (Blueprint $table) {
            $table->id(); // PK

            // Laravel tables
            $table->timestamps();
            $table->softDeletes();

            // Other tables
            $table->bigInteger('goal_id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->text('notes')->nullable();
            $table->date('deadline')->nullable();
            $table->boolean('achieved')->default(0);
            $table->boolean('override_show_todo')->nullable(); // Overrides the default todo behavior of the goal
            $table->boolean('override_todo_days_before')->default(0); // How many days before the duedate should we push to the todo list

            // Constraints
            $table->foreign('goal_id')->references('id')->on('goals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goal_action_items');
    }
}
