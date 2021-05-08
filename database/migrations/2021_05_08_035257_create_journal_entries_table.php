<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJournalEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            // PK
            $table->id();

            // Laravel tables
            $table->timestamps();
            $table->softDeletes();

            // Other columns
            $table->bigInteger('user_id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->tinyInteger('mood_id')->unsigned()->default(0); // Default
            $table->string('title');
            $table->date('day');
            $table->text('body')->nullable();

            // Constraints
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('journal_categories');
            $table->foreign('mood_id')->references('id')->on('journal_moods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('journal_entries');
    }
}
