<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Navio extends Model
{
    use HasFactory;


    public function tabuleiro()
    {
        return $this->belongsTo(Tabuleiro::class);
    }

    public function casas()
    {
        return $this->hasMany(Casa::class);
    }
}

