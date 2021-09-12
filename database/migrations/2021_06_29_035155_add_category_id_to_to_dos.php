<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToToDos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('to_dos', function (Blueprint $table) {
            // Column
            $table->bigInteger('category_id')->unsigned()->nullable();

            // Constraint
            $table->foreign('category_id')->references('id')->on('to_do_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('to_dos', function (Blueprint $table) {
            $table->dropForeign('to_dos_category_id_foreign');
            $table->dropColumn('category_id');
        });
    }
}
