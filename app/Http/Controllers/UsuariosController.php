<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    public function panel(){
        return view('usuarios.panel');
    }
}
