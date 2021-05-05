<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalActionItemsToDoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goal_action_items_to_do', function (Blueprint $table) {
            // PK
            $table->bigInteger('action_item_id')->unsigned();
            $table->bigInteger('to_do_id')->unsigned();

            // Constraints
            $table->primary(['action_item_id', 'to_do_id']);
            $table->foreign('action_item_id')->references('id')->on('goal_action_items');
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
        Schema::dropIfExists('goal_action_items_to_do');
    }
}
