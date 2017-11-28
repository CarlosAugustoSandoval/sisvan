<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class DetalleConsulta extends Model
{
    public function Consulta() {
        return $this->belongsTo('App\Models\Catalogo\Consulta');
    }

    public function RangoEdadVariable() {
        return $this->belongsTo('App\Models\Catalogo\RangoEdadVariable');
    }
}
