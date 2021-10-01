<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddictionRelapsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addiction_relapses', function (Blueprint $table) {
            $table->id(); // PK

            // Laravel columns
            $table->timestamps();
            $table->softDeletes();

            // Other columns
            $table->bigInteger('addiction_id')->unsigned();
            $table->tinyInteger('type_id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->text('notes')->nullable();

            // Constraints
            $table->foreign('addiction_id')->references('id')->on('addictions');
            $table->foreign('type_id')->references('id')->on('addiction_relapse_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addiction_relapses');
    }
}
