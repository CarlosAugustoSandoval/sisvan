<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class Barrio extends Model
{
    public function Municipio(){
        return $this->belongsTo('App\Models\Catalogo\Municipio');
    }
}
