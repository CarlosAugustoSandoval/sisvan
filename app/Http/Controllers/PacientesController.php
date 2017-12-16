<?php

namespace App\Http\Controllers;

use App\Http\helpers\EnumsTrait;
use App\Models\Catalogo\Barrio;
use App\Models\Catalogo\Consulta;
use App\Models\Catalogo\DetalleConsulta;
use App\Models\Catalogo\Ep;
use App\Models\Catalogo\GrupoEtnico;
use App\Models\Catalogo\GrupoPoblacional;
use App\Models\Catalogo\LmsDato;
use App\Models\Catalogo\ProgramaSocial;
use App\Models\Catalogo\Regimen;
use App\Models\Catalogo\TipoAreaResidencial;
use App\Models\Catalogo\TipoIdentificacion;
use App\Persona;
use Faker\Provider\id_ID\Person;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class PacientesController extends Controller
{
    use EnumsTrait;
    public function panel(){
        return view('pacientes.panel');
    }

    public function obtenerPacientes(Request $request){
        try{
            $request =  json_decode($request->getContent());
            $pacientes = Persona::Buscar($request->datos->busqueda)->with(['TipoIdentificacion','ProgramaSocial','RangoEdad.Variable','Consulta.DetalleConsulta'])->paginate(50);
            return response()->json($pacientes);
        }catch (\Exception $exception){
            return response()->json($exception);
        }
    }

    public function complementosPacientes(){
        try{
            $usuario = Auth::user()->with(['Upgd.Servicio'])->get();
//            var_dump($usuario[0]['upgd']->servicio);
            return response()->json([
                'tiposIdentificacion' => TipoIdentificacion::where('estado','=','Activo')->get(),
                'regimenes' => Regimen::where('estado','=','Activo')->get(),
                'epss' => Ep::where('estado','=','Activa')->get(),
                'tiposAreaResidencial' => TipoAreaResidencial::get(),
                'barrios' => Barrio::where('municipio_id','=',$usuario[0]['upgd']->municipio_id)->get(),
                'tiposAreaResidencial' => TipoAreaResidencial::get(),
                'gruposEtnico' => GrupoEtnico::with(['SubgrupoEtnico'])->get(),
                'gruposPoblacional' => GrupoPoblacional::get(),
                'programasSocial' => ProgramaSocial::where('estado','=','Activo')->get(),
                'generos' => $this->getEnumValues('personas','genero'),
                'beneficiarios' => $this->getEnumValues('personas','beneficiario'),
                'serviciosUpgd' => $usuario[0]['upgd']->servicio,
            ]);
        }catch (\Exception $exception){
            return response()->json($exception);
        }
    }

    public function calcularRangoEdad($fecha){
        $arrayDate = explode("-", $fecha);
        $date = Carbon::createFromDate((int)$arrayDate[0], (int)$arrayDate[1], (int)$arrayDate[2], 'America/Bogota');
        $ahora = Carbon::now('America/Bogota');
        $años = ($date->diffInDays($ahora))/365;
        if($años>0 and $años<=5){
            return 1;
        }
        if($años>5 and $años<=18){
            return 2;
        }
        if($años>18 and $años<=64){
            return 3;
        }
    }

    public function calcularEdad($fecha){
        $arrayDate = explode("-", $fecha);
        $date = Carbon::createFromDate((int)$arrayDate[0], (int)$arrayDate[1], (int)$arrayDate[2], 'America/Bogota');
        $ahora = Carbon::now('America/Bogota');
        $años = ($date->diffInDays($ahora))/365;
        $semanas = ($date->diffInWeeks($ahora));
        $edad = new class{};
        $edad->anios=$años;
        $edad->meses=$años*12;
        $edad->semanas = $semanas;
        return $edad;
    }

    public function clasificacionNutricional(Request $request)
    {
        try {
            $variables = new class{};
            $variables->peso='';
            $variables->talla='';
            $variables->pc='';
            $variables->hg='';
            $variables->imc='';
            $variables->edadMeses=$request['edad']['meses'];
            $variables->edadSemanas=$request['edad']['semanas'];
            $variables->genero=$request['edad']['genero'];

            $datos = new class{
              var $zs = '';
              var $dv = '';
              var $cn = '';
              var $clase = '';
            };

            $datosLms = new class{
                var $genero = '';
                var $tipo_r = '';
                var $r = '';
                var $id_tipo_lms_datos = '';
                var $x = '';
            };

            $clasificacion = new class{
                var $imc = '';
                var $hg = '';
                var $pesotalla = '';
                var $tallaedad = '';
                var $pcedad = '';
                var $pesoedad = '';
                var $imcedad = '';
            };

            $clasificacion->hg = new $datos;
            $clasificacion->pesotalla = new $datos;
            $clasificacion->tallaedad = new $datos;
            $clasificacion->pcedad = new $datos;
            $clasificacion->pesoedad = new $datos;
            $clasificacion->imcedad = new $datos;

            switch ($request['edad']['rango_edad_id']){
                case 1:{
                    foreach ($request['consulta']['detalle_consulta'] as $key=>$detalle ) {
                        if(is_numeric($detalle['valor'])){
                            switch($detalle['rango_edad_variable_id']){
                                case 1:{
                                    $variables->peso = $detalle['valor'];
                                    break;
                                }
                                case 2:{
                                    $variables->talla = $detalle['valor'];
                                    break;
                                }
                                case 3:{
                                    $variables->pc = $detalle['valor'];
                                    break;
                                }
                                case 4:{
                                    $variables->hg = $detalle['valor'];
                                    break;
                                }
                            }
                        }
                    }


                    if(is_numeric($variables->peso) && is_numeric($variables->talla)){
                        //IMC
                        $variables->imc = $variables->peso / pow (($variables->talla / 100),2);
                        $clasificacion->imc = $variables->imc;
                    }

                    //PESO PARA LA EDAD
                    if(is_numeric($variables->peso)){
                        $lmsDatos = new $datosLms;
                        $lmsDatos->id_tipo_lms_datos = 4;
                        if(is_numeric($variables->edadSemanas) && ($variables->edadSemanas<13)){
                            $lmsDatos->tipo_r = 'Semana';
                        }else{
                            $lmsDatos->tipo_r = 'Mes';
                        }
                        $lmsDatos->genero = $variables->genero;
                        $lmsDatos->r = $variables->edadSemanas;
                        $lmsDatos->x = $variables->peso;
                        $clasificacion->pesoedad = $this->calcularZ($lmsDatos);
                    }

                    //HEMOGLOBINA
                    if(is_numeric($variables->hg)){
                        if($variables->edadMeses<60 && $variables->hg>10.9){
                            $clasificacion->hg->zs='';
                            $clasificacion->hg->dv='';
                            $clasificacion->hg->cn='SIN ANEMIA';
                            $clasificacion->hg->clase='bg-success';
                        }
                        if($variables->edadMeses<60 && ($variables->hg<11 && $variables->hg>9.9)){
                            $clasificacion->hg->zs='';
                            $clasificacion->hg->dv='';
                            $clasificacion->hg->cn='ANEMIA LEVE';
                            $clasificacion->hg->clase='bg-warning';
                        }

                        if($variables->edadMeses<60 && ($variables->hg<10 && $variables->hg>6.9)){
                            $clasificacion->hg->zs='';
                            $clasificacion->hg->dv='';
                            $clasificacion->hg->cn='ANEMIA MODERADA';
                            $clasificacion->hg->clase='bg-warning';
                        }
                        if($variables->edadMeses<60 && $variables->hg<7){
                            $clasificacion->hg->zs='';
                            $clasificacion->hg->dv='';
                            $clasificacion->hg->cn='ANEMIA SEVERA';
                            $clasificacion->hg->clase='bg-danger';
                        }
                    }
                    break;
                }
            }

            return response()->json([
                'estado' => 'ok',
                'clasificacion' =>  $clasificacion
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'estado' => 'fail',
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function calcularZ($lmsDatos){
        $datos = new class{
            var $zs = '';
            var $dv = '';
            var $cn = '';
            var $clase = '';
        };
        $fz = LmsDato::where('genero','=',$lmsDatos->genero,'and')->where('tipo_r','=',$lmsDatos->tipo_r,'and')->where('r','=',$lmsDatos->r)->first();
        if ($fz){
            $datos->zs = (pow(((double)$lmsDatos->x / (double)$fz->m),(double)$fz->l)-1)/((double)$fz->l * (double)$fz->s);

            return $datos;
        }
    }

    public function procesaEdadPacientes(Request $request)
    {
        DB::beginTransaction();
        try {
            $persona = Persona::find($request->id);
            $persona->rango_edad_id = $this->calcularRangoEdad($request->fecha_nacimiento);
            $persona->save();
            DB::commit();
            $edad = $this->calcularEdad($request->fecha_nacimiento);
            $edad->rango_edad_id = $persona->rango_edad_id;
            $edad->genero = $persona->genero;
            return response()->json([
                'estado' => 'ok',
                'paciente' =>  Persona::where('id','=',$persona->id)->with(['RangoEdad.Variable'])->first(),
                'edad'=> $edad
            ]);
        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'estado' => 'fail',
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function guardarPacientes(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request->id != '') {
                $validador = Validator::make($request->all(), [
                    'tipo_identificacion_id' => 'required',
                    'identificacion' => 'required | unique:personas',
                    'identificacion' => [
                        'required ',
                        Rule::unique('personas')->ignore($request->id)
                    ],
                    'nombre1' => 'required',
                    'apellido1' => 'required',
                    'fecha_nacimiento' => 'required',
                    'genero' => 'required',
                    'tipo_area_residencial_id' => 'required',
                    'barrio_id' => 'required',
                    'regimen_id' => 'required',
                    'ep_id' => 'required',
                    'grupo_etnico_id' => 'required',
                    'grupo_poblacional_id' => 'required',
                    'beneficiario' => 'required',
                    'programa_social' => 'required_if:beneficiario,"Sí"',
                ]);
                if ($validador->fails()) {
                    DB::rollback();
                    return response()->json([
                        'estado' => 'validador',
                        'errors' => $validador->errors()
                    ]);
                }

                $persona = Persona::find($request->id);
                $persona->tipo_identificacion_id = $request->tipo_identificacion_id;
                $persona->identificacion = $request->identificacion;
                $persona->nombre1 = $request->nombre1;
                $persona->nombre2 = $request->nombre2;
                $persona->apellido1 = $request->apellido1;
                $persona->apellido2 = $request->apellido2;
                $persona->nombre_completo = $request->nombre1.' '.$request->nombre2.' '.$request->apellido1.' '.$request->apellido2;
                $persona->fecha_nacimiento = $request->fecha_nacimiento;
                $persona->genero = $request->genero;
                $persona->tipo_area_residencial_id = $request->tipo_area_residencial_id;
                $persona->barrio_id = $request->barrio_id;
                $persona->telefono = $request->telefono;
                $persona->regimen_id = $request->regimen_id;
                $persona->ep_id = $request->ep_id;
                $persona->grupo_etnico_id = $request->grupo_etnico_id;
                $persona->subgrupo_etnico_id = $request->subgrupo_etnico_id;
                $persona->grupo_poblacional_id = $request->grupo_poblacional_id;
                $persona->beneficiario = $request->beneficiario;
                $persona->rango_edad_id = $this->calcularRangoEdad($request->fecha_nacimiento);
                $persona->ProgramaSocial()->detach();
                $persona->ProgramaSocial()->attach($request->programa_social);
                $persona->save();
                DB::commit();
                return response()->json([
                    'estado' => 'ok',
                    'tipo' => 'update',
                    'message' => 'Paciente actualizado correctamente',
                    'paciente' => Persona::where('id','=',$persona->id)->with(['TipoIdentificacion','ProgramaSocial','RangoEdad.Variable','Consulta.DetalleConsulta'])->first()
                ]);
            }
            else {
                //Create
                $validador = Validator::make($request->all(), [
                    'tipo_identificacion_id' => 'required',
                    'identificacion' => 'required | unique:personas',
                    'nombre1' => 'required',
                    'apellido1' => 'required',
                    'fecha_nacimiento' => 'required',
                    'genero' => 'required',
                    'tipo_area_residencial_id' => 'required',
                    'barrio_id' => 'required',
                    'regimen_id' => 'required',
                    'ep_id' => 'required',
                    'grupo_etnico_id' => 'required',
                    'grupo_poblacional_id' => 'required',
                    'beneficiario' => 'required',
                    'programa_social' => 'required_if:beneficiario,"Sí"',
                ]);
                if ($validador->fails()) {
                    DB::rollback();
                    return response()->json([
                        'estado' => 'validador',
                        'errors' => $validador->errors()
                    ]);
                }

                $persona = new Persona();
                $persona->tipo_identificacion_id = $request->tipo_identificacion_id;
                $persona->identificacion = $request->identificacion;
                $persona->nombre1 = $request->nombre1;
                $persona->nombre2 = $request->nombre2;
                $persona->apellido1 = $request->apellido1;
                $persona->apellido2 = $request->apellido2;
                $persona->nombre_completo = $request->nombre1.' '.$request->nombre2.' '.$request->apellido1.' '.$request->apellido2;
                $persona->fecha_nacimiento = $request->fecha_nacimiento;
                $persona->genero = $request->genero;
                $persona->tipo_area_residencial_id = $request->tipo_area_residencial_id;
                $persona->barrio_id = $request->barrio_id;
                $persona->telefono = $request->telefono;
                $persona->regimen_id = $request->regimen_id;
                $persona->ep_id = $request->ep_id;
                $persona->grupo_etnico_id = $request->grupo_etnico_id;
                $persona->subgrupo_etnico_id = $request->subgrupo_etnico_id;
                $persona->grupo_poblacional_id = $request->grupo_poblacional_id;
                $persona->beneficiario = $request->beneficiario;
                $persona->rango_edad_id = $this->calcularRangoEdad($request->fecha_nacimiento);
                $persona->estado = 'Activo';
                $persona->ProgramaSocial()->attach($request->programa_social);
                $persona->save();
                DB::commit();
                return response()->json([
                    'estado' => 'ok',
                    'tipo' => 'save',
                    'message' => 'Paciente creado correctamente',
                    'paciente' => Persona::where('id','=',$persona->id)->with(['TipoIdentificacion','ProgramaSocial','RangoEdad.Variable','Consulta.DetalleConsulta'])->first()
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'estado' => 'fail',
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function guardarConsultas(Request $request)
    {
        DB::beginTransaction();
        try {
            if ($request['paciente']['id'] != '') {
                $persona = Persona::find($request['paciente']['id']);
                $persona->tipo_identificacion_id = $request['paciente']['tipo_identificacion_id'];
                $persona->identificacion = $request['paciente']['identificacion'];
                $persona->nombre1 = $request['paciente']['nombre1'];
                $persona->nombre2 = $request['paciente']['nombre2'];
                $persona->apellido1 = $request['paciente']['apellido1'];
                $persona->apellido2 = $request['paciente']['apellido2'];
                $persona->nombre_completo = $persona->nombre1.' '.$persona->nombre2.' '.$persona->apellido1.' '.$persona->apellido2;
                $persona->fecha_nacimiento = $request['paciente']['fecha_nacimiento'];
                $persona->genero = $request['paciente']['genero'];
                $persona->tipo_area_residencial_id = $request['paciente']['tipo_area_residencial_id'];
                $persona->barrio_id = $request['paciente']['barrio_id'];
                $persona->telefono = $request['paciente']['telefono'];
                $persona->regimen_id = $request['paciente']['regimen_id'];
                $persona->ep_id = $request['paciente']['ep_id'];
                $persona->grupo_etnico_id = $request['paciente']['grupo_etnico_id'];
                $persona->subgrupo_etnico_id = $request['paciente']['subgrupo_etnico_id'];
                $persona->grupo_poblacional_id = $request['paciente']['grupo_poblacional_id'];
                $persona->beneficiario = $request['paciente']['beneficiario'];
                $persona->rango_edad_id = $this->calcularRangoEdad($request['paciente']['fecha_nacimiento']);

                $persona->ProgramaSocial()->detach();
                $persona->ProgramaSocial()->attach($request['paciente']['programa_social']);
                $persona->save();

                $consulta = new Consulta();
                $consulta->servicio_upgd_id = $request['consulta']['servicio_upgd_id'];
                $consulta->fecha_consulta = $request['consulta']['fecha_consulta'];
                $consulta->persona_id = $persona->id;
                $consulta->gestante = 0;
                $consulta->user_id = Auth::user()->id;
                $consulta->save();

                foreach ($request['consulta']['detalle_consulta'] as $key=>$detalle ) {
                    $detalleconsulta = new DetalleConsulta();
                    $detalleconsulta->consulta_id = $consulta->id;
                    $detalleconsulta->rango_edad_variable_id = $detalle['rango_edad_variable_id'];
                    $detalleconsulta->valor = $detalle['valor'];
                    $detalleconsulta->save();
                }

                DB::commit();
                return response()->json([
                    'estado' => 'ok',
                    'tipo' => 'update',
                    'message' => 'Consulta registrada satisfactoriamente',
                    'paciente' => Persona::where('id','=',$persona->id)->with(['TipoIdentificacion','ProgramaSocial','RangoEdad.Variable','Consulta.DetalleConsulta'])->first()
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'estado' => 'fail',
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
