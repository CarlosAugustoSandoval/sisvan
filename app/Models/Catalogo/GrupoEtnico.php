<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class GrupoEtnico extends Model
{
    public function SubgrupoEtnico(){
        return $this->hasMany('App\Models\Catalogo\SubgrupoEtnico');
    }
}
