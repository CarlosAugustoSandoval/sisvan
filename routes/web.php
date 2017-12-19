<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'HomeController@index')->name("home");

Route::group(['middleware' => 'auth'], function () {
    Route::get('/pacientes/panel', 'PacientesController@panel')->name("pacientes");
    Route::post('/pacientes/obtener-pacientes', 'PacientesController@obtenerPacientes');
    Route::post('/pacientes/guardar-pacientes', 'PacientesController@guardarPacientes');
    Route::get('/pacientes/complementos-pacientes', 'PacientesController@complementosPacientes');
    Route::post('/pacientes/procesaedad-pacientes', 'PacientesController@procesaEdadPacientes');

    Route::post('/pacientes/clasificacion-nutricional', 'PacientesController@clasificacionNutricional');
    Route::post('/pacientes/guardar-consultas', 'PacientesController@guardarConsultas');
    Route::post('/pacientes/excel-consulta', 'PacientesController@reporteConsultasSemana');

    Route::get('/usuarios/panel', 'UsuariosController@panel')->name("usuarios");
    Route::get('/upgds/panel', 'UpgdsController@panel')->name("upgds");
});
