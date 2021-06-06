<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Navio extends Model
{
    use HasFactory;

    public $fillable = [
        'tabuleiro_id',
        'tamanho',
        'afundado',
    ];

    public function tabuleiro()
    {
        return $this->belongsTo(Tabuleiro::class, 'tabuleiro_id');
    }

    public function casas()
    {
        return $this->hasMany(Casa::class, 'navio_id');
    }

    public static function criarNavios(Tabuleiro $tabuleiro) {
        $navios = [6,4,3,3,1];
        foreach ($navios as $tamanho){
            $navio = new Navio();
            $navio->tamanho = $tamanho;
            $navio->afundado = FALSE;
            $navio->tabuleiro_id = $tabuleiro->id;
            $navio->save();
        }
    }
}

