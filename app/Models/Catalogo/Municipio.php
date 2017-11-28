<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    public function Departamento(){
        return $this->belongsTo('App\Models\Catalogo\Departamento');
    }

    public function Barrio(){
        return $this->hasMany('App\Models\Catalogo\Barrio');
    }
}
