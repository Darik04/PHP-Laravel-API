<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_models', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->char('code', 255)->unique();
            $table->char('name', 255);
            $table->integer('row')->default(1);
            $table->integer('seat')->default(1);
            $table->foreignId('show')->constrained('show_models')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_models');
    }
}
