<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffirmationsReadLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affirmations_read_logs', function (Blueprint $table) {
            // PK
            $table->id();

            // More columns
            $table->bigInteger('user_id')->unsigned();
            $table->timestamp('read_at');

            // Constraints
            $table->foreign('user_id')->references('id')->on('users');

            // Indexes
            $table->index('user_id');
            $table->index('read_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affirmations_read_logs');
    }
}
