<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_settings', function (Blueprint $table) {
            // PK is composite, see constraints
            // Laravel columns
            $table->timestamps();
            
            // More Columns
            $table->bigInteger('user_id')->unsigned(); // References ID on users
            $table->smallInteger('setting_id')->unsigned(); // References ID on settings
            $table->string('value');

            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('setting_id')->references('id')->on('settings');
            $table->primary(['user_id', 'setting_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_settings');
    }
}
