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
            $table->text('details')->nullable();
            $table->boolean('completed')->default(0);

            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('bucketlist_categories');
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
