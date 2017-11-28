<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpgdsController extends Controller
{
    public function panel(){
        return view('upgds.panel');
    }
}
