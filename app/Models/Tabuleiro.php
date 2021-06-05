<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(Casa::class, 'tabuleiro_id');
    }

    public function navios()
    {
        return $this->hasMany(Navio::class, 'tabuleiro_id');
    }
}
