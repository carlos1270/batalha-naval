<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casas', function (Blueprint $table) {
            $table->Increments('id');
            $table->Integer('linha');
            $table->Integer('coluna');
            $table->Boolean('preenchido')->nullable(true);
            $table->Boolean('acertado')->nullable(true);
            $table->Integer('posicao_do_navio')->nullable(true);
            $table->Integer('tabuleiro_id');
            $table->Integer('navio_id')->nullable(true);
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
        Schema::dropIfExists('casas');
    }
}
