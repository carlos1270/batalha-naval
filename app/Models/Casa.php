<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Casa extends Model
{
    use HasFactory;


    public function tabuleiro()
    {
        return $this->belongsTo(Tabuleiro::class);
    }

    public function navio()
    {
        return $this->belongsTo(Navio::class);
    }
}
