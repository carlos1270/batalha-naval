<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabuleiro extends Model
{
    use HasFactory;

    public function jogo()
    {
        return $this->belongsTo(Jogo::class);
    }

    public function casas()
    {
        return $this->hasMany(Casa::class);
    }

    public function navios()
    {
        return $this->hasMany(Navio::class);
    }
}
