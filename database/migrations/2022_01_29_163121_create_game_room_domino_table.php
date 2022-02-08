<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameRoomDominoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_room_domino', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_room_id')->index();
            $table->foreign('game_room_id')->references('id')->on('game_rooms')->onDelete('cascade');
            $table->unsignedBigInteger('domino_id');
            $table->foreign('domino_id')->references('id')->on('dominoes');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('index_position')->nullable();
            $table->boolean('flip')->default(false);
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
        Schema::dropIfExists('game_room_domino');
    }
}
