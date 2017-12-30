<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class DiagnosticoNutricional extends Model
{
    public function Consulta() {
        return $this->belongsTo('App\Models\Catalogo\Consulta');
    }
}
