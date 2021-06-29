<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeatureVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_votes', function (Blueprint $table) {
            $table->id(); // PK

            // Laravel columns
            $table->timestamps();

            // Other columns
            $table->bigInteger('feature_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->tinyInteger('value')->default(0);

            // Constraints
            $table->unique(['feature_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feature_votes');
    }
}
