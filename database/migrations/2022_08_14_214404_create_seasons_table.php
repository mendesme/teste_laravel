<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('numero');
            // $table->unsignedBigInteger('series_id');                         // unsignedBigInteger para id's
            // $table->foreign('series_id')->references('id')->on('series');    // foreign key
            $table->foreignId('series_id')->constrained()->onDelete('cascade');  // equivalente as duas linhas acima
            // $table->foreignIdFor(Serie::class, 'series_id')->constrained();   // equivalente
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seasons');
    }
}
