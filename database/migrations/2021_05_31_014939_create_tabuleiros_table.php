<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTabuleirosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabuleiros', function (Blueprint $table) {
            $table->Increments('id');
            $table->Integer('qtd_linhas');
            $table->Integer('qtd_colunas');
            $table->Integer('qtd_jogadas');
            $table->Integer('jogo_id');
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
        Schema::dropIfExists('tabuleiros');
    }
}
