<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
