<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id(); // PK

            // Laravel tables
            $table->timestamps();
            $table->softDeletes();

            // More columns
            $table->bigInteger('user_id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->tinyInteger('type_id')->unsigned();
            $table->tinyInteger('status_id')->unsigned();
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->boolean('achieved')->default(0); // Whether or not the user has marked it as achieved, not if progress is at 100%
            $table->boolean('use_custom_img')->default(0); // Otherwise use default based on status
            $table->tinyInteger('progress')->default(0); // To store cached calculated goal progress %
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            /**
             * Goal Type Specific Attributes
             */

            // Determines the default for if the ToDo list should show action items for the goal
            $table->boolean('default_show_todo')->nullable();

            // Determines the default for how many days before an action items due date it shows up on the ToDo list
            $table->tinyInteger('default_todo_days_before')->unsigned()->nullable();

            // The amount of times something needs to be accomplished for a manual goal or
            // The number of action items that need to be scheduled and accomplished each ad-hoc period
            $table->smallInteger('custom_times')->unsigned()->nullable();

            // The progress for a manual goal
            $table->smallInteger('manual_completed')->unsigned()->nullable();

            // The length of the period of time in which {custom_times} number of action items needs to be scheduled and accomplished
            $table->tinyInteger('ad_hoc_period_id')->unsigned()->nullable();

            // The strength level that a habit must hit in order to achieve the goal
            $table->tinyInteger('habit_strength')->unsigned()->nullable();

            // The habit id of the habit the goal references
            $table->bigInteger('habit_id')->unsigned()->nullable();

            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('type_id')->references('id')->on('goal_types');
            $table->foreign('status_id')->references('id')->on('goal_statuses');
            $table->foreign('category_id')->references('id')->on('goal_categories');
            $table->foreign('ad_hoc_period_id')->references('id')->on('goal_ad_hoc_periods');
            $table->foreign('parent_id')->references('id')->on('goals');
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
        Schema::dropIfExists('goals');
    }
}
