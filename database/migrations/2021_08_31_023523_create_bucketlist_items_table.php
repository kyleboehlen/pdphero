<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBucketlistItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bucketlist_items', function (Blueprint $table) {
            // PK
            $table->id();

            // Laravel tables
            $table->timestamps();
            $table->softDeletes();

            // More columns
            $table->bigInteger('user_id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->text('notes')->nullable();
            $table->boolean('achieved')->default(0);
            $table->bigInteger('goal_id')->unsigned()->nullable();
            $table->date('deadline')->nullable();

            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('bucketlist_categories');
            $table->foreign('goal_id')->references('id')->on('goals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bucketlist_items');
    }
}
