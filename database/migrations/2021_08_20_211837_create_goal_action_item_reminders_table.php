<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalActionItemRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goal_action_item_reminders', function (Blueprint $table) {
            $table->id(); // PK

            // Laravel columns
            $table->timestamps();

            // Other columns
            $table->uuid('uuid')->unique();
            $table->bigInteger('action_item_id')->unsigned();
            $table->timestamp('remind_at');

            // Constraints
            $table->foreign('action_item_id')->references('id')->on('goal_action_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goal_action_item_reminders');
    }
}
