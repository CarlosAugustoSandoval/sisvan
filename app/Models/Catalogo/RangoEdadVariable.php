<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class RangoEdadVariable extends Model
{
    public function DetalleConsulta() {
        return $this->hasMany('App\Models\Catalogo\DetalleConsulta');
    }
}
