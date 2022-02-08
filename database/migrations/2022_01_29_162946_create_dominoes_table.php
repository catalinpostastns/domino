<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateDominoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dominoes', function (Blueprint $table) {
            $table->id();
            $table->integer('side1')->default(0);
            $table->integer('side2')->default(0);
            $table->timestamps();
        });

        Artisan::call('db:seed', array('--class' => 'DominoesSeeder'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dominoes');
    }
}
