<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    public function Persona() {
        return $this->belongsTo('App\Persona');
    }

    public function ServicioUpgd() {
        return $this->belongsTo('App\Models\Catalogo\ServicioUpgd');
    }

    public function DetalleConsulta() {
        return $this->hasMany('App\Models\Catalogo\DetalleConsulta');
    }

    public function DiagnosticoNutricional() {
        return $this->hasMany('App\Models\Catalogo\DiagnosticoNutricional');
    }
}
