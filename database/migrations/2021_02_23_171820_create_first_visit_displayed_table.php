<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirstVisitDisplayedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('first_visit_displayed', function (Blueprint $table) {
            // Composite PK, see constraints

            // More Columns
            $table->bigInteger('user_id')->unsigned(); // References ID on users
            $table->smallInteger('message_id')->unsigned(); // References ID on homes
            $table->timestamp('displayed_at');

            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('message_id')->references('id')->on('first_visit_messages');
            $table->primary(['user_id', 'message_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('first_visit_displayed');
    }
}
