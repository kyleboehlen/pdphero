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
            $table->boolean('default_show_todo')->default(0); // Whether or not to automatically push to the todo list
            $table->boolean('default_todo_days_before')->default(0); // How many days before the duedate should we push to the todo list
            $table->tinyInteger('ad_hoc_period_id')->unsigned()->nullable();
            $table->tinyInteger('ad_hoc_times')->unsigned()->nullable();
            $table->string('ad_hoc_label')->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('type_id')->references('id')->on('goal_types');
            $table->foreign('status_id')->references('id')->on('goal_statuses');
            $table->foreign('category_id')->references('id')->on('goal_categories');
            $table->foreign('ad_hoc_period_id')->references('id')->on('goal_ad_hoc_periods');
            $table->foreign('parent_id')->references('id')->on('goals');
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
