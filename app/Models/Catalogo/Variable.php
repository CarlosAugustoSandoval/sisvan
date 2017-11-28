<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    public function RangoEdad(){
        return $this->belongsToMany('App\Models\Catalogo\RangoEdad')->withTimestamps();
    }
}
