<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class ServicioUpgd extends Model
{
    public function Consulta() {
        return $this->hasMany('App\Models\Catalogo\Consulta');
    }

    public function Servicio(){
        return $this->belongsTo('App\Models\Catalogo\Servicio');
    }


    protected $table = 'servicio_upgd';
}
