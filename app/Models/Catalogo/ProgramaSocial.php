<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class ProgramaSocial extends Model
{
    public function Persona(){
        return $this->belongsToMany('App\Persona')->withTimestamps();
    }
}
