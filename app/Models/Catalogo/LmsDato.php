<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class LmsDato extends Model
{
    public function TipoLmsDato(){
        return $this->belongsTo('App\Models\Catalogo\TipoLmsDato');
    }
}
