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
