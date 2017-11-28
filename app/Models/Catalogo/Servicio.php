<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    public function Upgd(){
        return $this->belongsToMany('App\Upgd')->withTimestamps();
    }
}
