<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class TipoLmsDato extends Model
{
    public function LmsDato(){
        return $this->hasMany('App\Models\Catalogo\LmsDato');
    }
}
