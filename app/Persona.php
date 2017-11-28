<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    public function TipoIdentificacion() {
        return $this->belongsTo('App\Models\Catalogo\TipoIdentificacion');
    }

    public function Regimen(){
        return $this->belongsTo('App\Models\Catalogo\Regimen');
    }

    public function Ep(){
        return $this->belongsTo('App\Models\Catalogo\Ep');
    }

    public function TipoAreaResidencia(){
        return $this->belongsTo('App\Models\Catalogo\TipoAreaResidencia');
    }

    public function Barrio(){
        return $this->belongsTo('App\Models\Catalogo\Barrio');
    }

    public function GrupoEtnico(){
        return $this->belongsTo('App\Models\Catalogo\GrupoEtnico');
    }

    public function SubgrupoEtnico(){
        return $this->belongsTo('App\Models\Catalogo\SubgrupoEtnico');
    }

    public function GrupoPoblacional(){
        return $this->belongsTo('App\Models\Catalogo\GrupoPoblacional');
    }

    public function RangoEdad(){
        return $this->belongsTo('App\Models\Catalogo\RangoEdad');
    }

    public function ProgramaSocial(){
        return $this->belongsToMany('App\Models\Catalogo\ProgramaSocial')->withTimestamps();
    }

    public function Consulta() {
        return $this->hasMany('App\Models\Catalogo\Consulta');
    }

    public function scopeBuscar($query, $data) {
        return $query
            ->where('identificacion', 'like', '%' . $data . '%')
            ->orWhere('nombre_completo', 'like', '%' . $data . '%');
    }
}
