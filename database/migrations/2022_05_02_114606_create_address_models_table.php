<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address_models', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->char('name', 255);
            $table->char('address', 255);
            $table->char('city', 255);
            $table->char('zip', 255);
            $table->char('country', 255);
            $table->foreignId('reservation_token')->constrained('reservation_token_models')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('address_models');
    }
}
