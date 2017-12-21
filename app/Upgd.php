<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Upgd extends Model
{
    public function User(){
        return $this->hasMany('App\User');
    }

    public function Servicio(){
        return $this->belongsToMany('App\Models\Catalogo\Servicio')->withPivot('id')->withTimestamps();
    }

    public function TipoInstitucion(){
        return $this->belongsTo('App\Models\Catalogo\TipoInstitucion');
    }
}
