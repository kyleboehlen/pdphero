<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersHideHomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_hide_homes', function (Blueprint $table) {
            // PK is composite, see constraints
            
            // More Columns
            $table->bigInteger('user_id')->unsigned(); // References ID on users
            $table->tinyInteger('home_id')->unsigned(); // References ID on homes

            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('home_id')->references('id')->on('homes');
            $table->primary(['user_id', 'home_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_hide_homes');
    }
}
