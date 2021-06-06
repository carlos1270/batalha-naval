<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tabuleiro;

class Casa extends Model
{
    use HasFactory;

    public $fillable = [
        'linha',
        'coluna',
        'preenchido',
        'acertado',
        'posicao_do_navio',
        'tabuleiro_id',
        'navio_id',
    ];
    
    public function tabuleiro()
    {
        return $this->belongsTo(Tabuleiro::class, 'tabuleiro_id');
    }

    public function navio()
    {
        return $this->belongsTo(Navio::class, 'navio_id');
    }

    public static function criarCasas(Tabuleiro $tabuleiro) {
        for ($i = 1; $i <= $tabuleiro->qtd_linhas; $i++) {
            for ($j = 1; $j <= $tabuleiro->qtd_colunas; $j++) {
                $casa = new Casa();
                $casa->linha = $i;
                $casa->coluna = $j;
                $casa->preenchido = FALSE;
                $casa->acertado = FALSE;
                $casa->tabuleiro_id = $tabuleiro->id;
                $casa->save();
            }
        }
    }
}
