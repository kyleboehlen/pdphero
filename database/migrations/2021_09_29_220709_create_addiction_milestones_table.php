<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddictionMilestonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addiction_milestones', function (Blueprint $table) {
            $table->increments('id'); // PK

            // Laravel columns
            $table->timestamps();

            // Other columns
            $table->bigInteger('addiction_id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->tinyInteger('amount');
            $table->tinyInteger('date_format_id')->unsigned();
            $table->text('reward')->nullable();
            $table->boolean('reached')->default(0);

            // Constraints
            $table->foreign('addiction_id')->references('id')->on('addictions');
            $table->foreign('date_format_id')->references('id')->on('addiction_date_formats');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addiction_milestones');
    }
}
