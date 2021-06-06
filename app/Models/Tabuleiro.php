<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\jogo;
use App\Models\Casa;
use App\Models\Navio;

class Tabuleiro extends Model
{
    use HasFactory;

    public $fillable = [
        'qtd_linhas',
        'qtd_colunas',
        'qtd_jogadas',
        'jogo_id', 
    ];

    public function jogo()
    {
        return $this->belongsTo(Jogo::class, 'jogo_id');
    }

    public function casas()
    {
        return $this->hasMany(Casa::class, 'tabuleiro_id', 'id');
    }

    public function navios()
    {
        return $this->hasMany(Navio::class, 'tabuleiro_id');
    }

    public static function criarTabuleiros(Jogo $jogo) {
        $tabuleiros = collect();

        $tabuleiroJogador = new Tabuleiro();
        $tabuleiroJogador->qtd_linhas = 10;
        $tabuleiroJogador->qtd_colunas = 10;
        $tabuleiroJogador->qtd_jogadas = 0;
        $tabuleiroJogador->jogo_id = $jogo->id;
        $tabuleiroJogador->save();
        Casa::criarCasas($tabuleiroJogador);
        Navio::criarNavios($tabuleiroJogador);
        $tabuleiros->push($tabuleiroJogador);

        $tabuleiroCOM = new Tabuleiro();
        $tabuleiroCOM->qtd_linhas = 10;
        $tabuleiroCOM->qtd_colunas = 10;
        $tabuleiroCOM->qtd_jogadas = 0;
        $tabuleiroCOM->jogo_id = $jogo->id;
        $tabuleiroCOM->save();
        $tabuleiros->push($tabuleiroCOM);
        Casa::criarCasas($tabuleiroCOM);
        Navio::criarNavios($tabuleiroCOM);
    
        return $tabuleiros;        
    } 
}
