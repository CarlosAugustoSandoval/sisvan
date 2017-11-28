<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Model;

class SubgrupoEtnico extends Model
{
    public function GrupoEtnico() {
        return $this->belongsTo('App\Models\Catalogo\GrupoEtnico');
    }
}
