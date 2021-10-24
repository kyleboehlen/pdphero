<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddictionDateFormatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addiction_date_formats', function (Blueprint $table) {
            $table->tinyIncrements('id'); // PK

            // Other columns
            $table->string('name');
            $table->smallInteger('max')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addiction_date_formats');
    }
}
