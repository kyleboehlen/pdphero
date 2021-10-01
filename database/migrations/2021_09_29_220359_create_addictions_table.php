<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddictionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addictions', function (Blueprint $table) {
            $table->id(); // PK

            // Laravel columns
            $table->timestamps();
            $table->softDeletes();

            // Other Columns
            $table->bigInteger('user_id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->tinyInteger('method_id')->unsigned();
            $table->text('details');
            $table->date('start_date')->nullable();
            $table->smallInteger('moderated_amount')->unsigned()->nullable();
            $table->enum('moderated_date_format', array_keys(config('addictions.date_formats')))->nullable();
            
            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('method_id')->references('id')->on('addiction_methods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addictions');
    }
}
