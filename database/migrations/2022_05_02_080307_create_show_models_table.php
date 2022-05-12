<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('show_models', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start', $precision = 0);
            $table->dateTime('end', $precision = 0);
            $table->foreignId('concert')->constrained('concert_models')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('show_models');
    }
}
