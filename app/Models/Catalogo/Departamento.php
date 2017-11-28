<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    public function Municipio(){
        return $this->hasMany('App\Models\Catalogo\Municipio');
    }
}
