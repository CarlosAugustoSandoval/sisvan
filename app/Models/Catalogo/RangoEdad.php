<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class RangoEdad extends Model
{
    public function Variable(){
        return $this->belongsToMany('App\Models\Catalogo\Variable')->withPivot('id')->withTimestamps();
    }
}
