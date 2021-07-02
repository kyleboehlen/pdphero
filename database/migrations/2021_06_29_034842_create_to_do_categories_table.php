<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToDoCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_do_categories', function (Blueprint $table) {
            $table->id(); // PK

            // Laravel tables
            $table->softDeletes();

            // Other columns
            $table->bigInteger('user_id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->string('name');

            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('to_do_categories');
    }
}
