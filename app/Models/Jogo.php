<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jogo extends Model
{
    use HasFactory;


    public function tabuleiros()
    {
        return $this->hasMany(Tabuleiro::class);
    }
}
