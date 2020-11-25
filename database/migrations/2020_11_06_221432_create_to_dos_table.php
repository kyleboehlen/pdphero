<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToDosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_dos', function (Blueprint $table) {
            // PK
            $table->id();

            // Laravel tables
            $table->timestamps();
            $table->softDeletes();

            // More columns
            $table->bigInteger('user_id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->tinyInteger('priority_id')->unsigned()->default(0);
            $table->tinyInteger('type_id')->unsigned();
            $table->text('notes')->nullable();
            $table->boolean('completed')->default(0);

            // Constraints
            $table->foreign('priority_id')->references('id')->on('to_do_priorities');
            $table->foreign('type_id')->references('id')->on('to_do_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('to_dos');
    }
}
