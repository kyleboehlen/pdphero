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
            $table->tinyIncrements('id'); // PK

            // Laravel columns
            $table->timestamps();

            // Other columns
            $table->bigInteger('addiction_id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->tinyInteger('amount');
            $table->enum('date_format', array_keys(config('addictions.date_formats')));
            $table->text('reward')->nullable();
            $table->boolean('reached')->default(0);

            // Constraints
            $table->foreign('addiction_id')->references('id')->on('addictions');
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
