<?php

namespace App\Http\Controllers;

use App\Http\helpers\EnumsTrait;
use App\Models\Catalogo\Barrio;
use App\Models\Catalogo\Consulta;
use App\Models\Catalogo\DetalleConsulta;
use App\Models\Catalogo\DiagnosticoNutricional;
use App\Models\Catalogo\Ep;
use App\Models\Catalogo\GrupoEtnico;
use App\Models\Catalogo\GrupoPoblacional;
use App\Models\Catalogo\LmsDato;
use App\Models\Catalogo\ProgramaSocial;
use App\Models\Catalogo\Regimen;
use App\Models\Catalogo\TipoAreaResidencial;
use App\Models\Catalogo\TipoIdentificacion;
use App\Persona;
use App\Upgd;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;



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

    public function calcularRangoEdad($fecha, $consulta){
        $arrayDate = explode("-", $fecha);
        $arrayDateConsulta = explode("-", $consulta);
        $date = Carbon::createFromDate((int)$arrayDate[0], (int)$arrayDate[1], (int)$arrayDate[2], 'America/Bogota');
        $ahora = Carbon::createFromDate((int)$arrayDateConsulta[0], (int)$arrayDateConsulta[1], (int)$arrayDateConsulta[2], 'America/Bogota');
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

    public function reporteConsultasSemana(Request $request){
        DB::beginTransaction();
        try{
            $request =  json_decode($request->getContent());
            $upgd = Upgd::find(Auth::user()->upgd_id);
            $upgd->ultima_semana_reporte = $request->excel_semana;
            $upgd->save();
            DB::commit();
            return response()->json([
                'estado' => 'ok',
                'message' => 'Reporte registrado',
            ]);
        }catch (\Exception $exception) {
            DB::rollback();
            return response()->json([
                'estado' => 'fail',
                'error' => $exception->getMessage(),
            ]);
        }
    }

    static function rConsultas(){
        $upgd = Upgd::find(Auth::user()->upgd_id)->with(['TipoInstitucion'])->first();
        $arrayFechas = [];
        $arraySemana =  explode("W", $upgd->ultima_semana_reporte);
        $intSemana = $arraySemana[1];
        for($i=0; $i<7; $i++){
            $arrayFechas[$i] = date('Y-m-d',strtotime($upgd->ultima_semana_reporte.'-'.$i));
        }
        $consultas = Consulta::whereIn('fecha_consulta', $arrayFechas,'and')
            ->where('activa','=',1)
            ->with(['DetalleConsulta', 'DiagnosticoNutricional', 'ServicioUpgd.Servicio','Persona.Barrio.Municipio', 'Persona.RangoEdad', 'Persona.Regimen', 'Persona.Ep', 'Persona.TipoAreaResidencial', 'Persona.GrupoEtnico', 'Persona.SubgrupoEtnico', 'Persona.GrupoPoblacional', 'Persona.ProgramaSocial'])
            ->get();

//        var_dump($consultas);
        Excel::create('reporte', function ($excel) use($consultas, $upgd, $intSemana){
            // Set the title
            $excel->setTitle('Our new awesome title');

            // Chain the setters
            $excel->setCreator('Software SISVAN')
                ->setCompany('SISVAN');

            // Call them separately
            $excel->setDescription('Reporte de consultas');

            $excel->sheet('Reporte', function($sheet) use($consultas, $upgd, $intSemana){
                $rowRegistros=4;
                function extraeValor($detalles, $variable){
                    foreach ($detalles as $key=>$detalle ) {
                        if($detalle->rango_edad_variable_id == $variable){
                            return $detalle->valor;
                        }
                    }
                }

                function extraeIMC($detalles){
                    $peso=0;
                    $talla=0;
                    foreach ($detalles as $key=>$detalle ) {
                        if($detalle->rango_edad_variable_id == 1){
                            $peso = $detalle->valor;
                        }
                        if($detalle->rango_edad_variable_id == 2){
                            $talla = $detalle->valor;
                        }
                    }
                    return round(($peso / pow (($talla / 100),2)),1);
                }

                function extraeDiagnostico($diagnosticos, $tipo, $variable){
                    foreach ($diagnosticos as $key=>$diagnostico ) {
                        switch($diagnostico->tipo_diagnostico_id){
                            case $tipo:{
                                return is_numeric($diagnostico[$variable])? round($diagnostico[$variable],2):$diagnostico[$variable];
                            }
                        }
                    }
                }

                function extraePrograma($programas, $id){
                    foreach ($programas as $key=>$programa ) {
                        if($programa->id == $id){
                            return 'Yes';
                        }
                    }
                    return 'No';
                }
                $sheet->row(1,['UniqueKey', 'FECHAVALORACION', 'MUNICIPIO', 'INSTITUCION', 'TIPODEINSTITUCION', 'SERVICIO', 'SEMANAEPI', 'TIPODEDOCUMENTO', 'NUMERODELDOCUMENTO', 'NOMBRE1', 'NOMBRE2', 'APELLIDO1', 'APELLIDO2', 'FECHANACIMIENTO', 'EDADMESES', 'EDADYEARS', 'TIPODEUSUARIO', 'TIPOAFILIACION', 'ENTIDAD', 'AREADERESIDANCIA', 'NOMBREDELBARRIOOLAVEREDA', 'DIRECCIONOINDICACIONES', 'NUMERODETELEFONO', 'PERTENENCIAETNICA', 'GRUPOINDIGENA', 'GRUPOPOBLACION', 'BENEFICIARIODEPROGRAMAS', 'DESAYUNOINFANTIL', 'CDI', 'RESTAURANTEESCOLAR', 'RECUPERACIONNUTRICIONAL', 'FAMILIASENACCION', 'MODALIDADFAMILIARICBF', 'REDUNIDOS', 'OTRO', 'PESOGES', 'TALLAGES', 'IMCGES', 'FUR', 'EDADGESTACIONAL', 'SUPLEMENTACION', 'HEMOGLOBINAgdl', 'CLASIFICAICONHB', 'DXNUTRIGESTANTE', 'SEXO', 'PESOKG', 'TALLACM', 'IMC', 'PERIMCEFALICO', 'HGMENORES18', 'CLASIFICACIONHGMENORES', 'LMEXCLUSIVA', 'LMACTUAL', 'ZPESOTALLAM5', 'FLAGPTM5', 'DXPTM5', 'ZTALLAEDADM5', 'FLAGTEM5', 'DXTEM5', 'ZPCEFEDADM5', 'FLAGPCEM5', 'DXPCEM5', 'ZPESOEDADM5', 'FLAGPEM5', 'DXPEM5', 'ZIMCEDADM5', 'FLAGIMCM5', 'DXIMCM5', 'ZIMCEDAD518', 'FLAGIMC518', 'DXIMC518', 'ZTALLAEDAD518', 'FLAGTE518', 'DXTE518', 'SEXOAD', 'PESOAD', 'TALLAAD', 'IMCAD', 'CIRCUNFCINTURAD', 'DXADULTOIMC', 'SUBCLAOBESIDAD', 'DXCCINTURADULTO']);

                foreach ($consultas as $key=>$consulta ) {
                    $regis = $rowRegistros + $key;
                    $sheet->row($regis,[$consulta->id, $consulta->fecha_consulta, $consulta->Persona->Barrio->Municipio->nombre, $upgd->razon_social, $upgd->TipoInstitucion->descripcion, $consulta->ServicioUpgd->Servicio->descripcion, $intSemana, $consulta->Persona->tipo_identificacion_id , $consulta->Persona->identificacion, $consulta->Persona->nombre1, $consulta->Persona->nombre2, $consulta->Persona->apellido1, $consulta->Persona->apellido2, $consulta->Persona->fecha_nacimiento, 'Edad mes', 'Edad anios', $consulta->Persona->RangoEdad->descripcion, $consulta->Persona->Regimen->nombre, $consulta->Persona->Ep->nombre, $consulta->Persona->TipoAreaResidencial->nombre, $consulta->Persona->Barrio->nombre, $consulta->Persona->direccion, $consulta->Persona->telefono, $consulta->Persona->GrupoEtnico->nombre, $consulta->Persona->subgrupo_etnico_id!=null? $consulta->Persona->SubgrupoEtnico->nombre:'', $consulta->Persona->GrupoPoblacional->nombre, $consulta->Persona->beneficiario, $consulta->Persona->ProgramaSocial != null?extraePrograma($consulta->Persona->ProgramaSocial, 1):'No', $consulta->Persona->ProgramaSocial != null?extraePrograma($consulta->Persona->ProgramaSocial, 5):'No', $consulta->Persona->ProgramaSocial != null?extraePrograma($consulta->Persona->ProgramaSocial, 2):'No', $consulta->Persona->ProgramaSocial != null?extraePrograma($consulta->Persona->ProgramaSocial, 6):'No', $consulta->Persona->ProgramaSocial != null?extraePrograma($consulta->Persona->ProgramaSocial, 3):'No', $consulta->Persona->ProgramaSocial != null?extraePrograma($consulta->Persona->ProgramaSocial, 7):'No', $consulta->Persona->ProgramaSocial != null?extraePrograma($consulta->Persona->ProgramaSocial, 4):'No', $consulta->Persona->ProgramaSocial != null?extraePrograma($consulta->Persona->ProgramaSocial, 8):'No', '', '', '', '', '', '', '', '', '', $consulta->Persona->genero=='Masculino'?1:2, extraeValor($consulta->DetalleConsulta,1), extraeValor($consulta->DetalleConsulta,2), extraeIMC($consulta->DetalleConsulta), extraeValor($consulta->DetalleConsulta,3), extraeValor($consulta->DetalleConsulta,4), extraeDiagnostico($consulta->DiagnosticoNutricional,1,'cn'), extraeValor($consulta->DetalleConsulta,6), extraeValor($consulta->DetalleConsulta,5), extraeDiagnostico($consulta->DiagnosticoNutricional,2,'zs'), extraeDiagnostico($consulta->DiagnosticoNutricional,2,'dv'), extraeDiagnostico($consulta->DiagnosticoNutricional,2,'cn'), extraeDiagnostico($consulta->DiagnosticoNutricional,3,'zs'), extraeDiagnostico($consulta->DiagnosticoNutricional,3,'dv'), extraeDiagnostico($consulta->DiagnosticoNutricional,3,'cn'), extraeDiagnostico($consulta->DiagnosticoNutricional,4,'zs'), extraeDiagnostico($consulta->DiagnosticoNutricional,4,'dv'), extraeDiagnostico($consulta->DiagnosticoNutricional,4,'cn'), extraeDiagnostico($consulta->DiagnosticoNutricional,5,'zs'), extraeDiagnostico($consulta->DiagnosticoNutricional,5,'dv'), extraeDiagnostico($consulta->DiagnosticoNutricional,5,'cn'), extraeDiagnostico($consulta->DiagnosticoNutricional,6,'zs'), extraeDiagnostico($consulta->DiagnosticoNutricional,6,'dv'), extraeDiagnostico($consulta->DiagnosticoNutricional,6,'cn')]);
                }
            });
        })->export('xls');

    }


    public function calcularEdad($fecha, $consulta){
        $arrayDate = explode("-", $fecha);
        $arrayDateConsulta = explode("-", $consulta);
        $date = Carbon::createFromDate((int)$arrayDate[0], (int)$arrayDate[1], (int)$arrayDate[2], 'America/Bogota');
        $ahora = Carbon::createFromDate((int)$arrayDateConsulta[0], (int)$arrayDateConsulta[1], (int)$arrayDateConsulta[2], 'America/Bogota');
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
              var $tipo_diagnostico_id = '';
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

            $clasificacion->hg->tipo_diagnostico_id = 1;
            $clasificacion->pesotalla->tipo_diagnostico_id = 2;
            $clasificacion->tallaedad->tipo_diagnostico_id = 3;
            $clasificacion->pcedad->tipo_diagnostico_id = 4;
            $clasificacion->pesoedad->tipo_diagnostico_id = 5;
            $clasificacion->imcedad->tipo_diagnostico_id = 6;

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

                    $lmsDatos = new $datosLms;
                    $lmsDatos->genero = $variables->genero;

                    if(is_numeric($variables->talla)){
                        $lmsDatos->x = $variables->talla;
                        $lmsDatos->id_tipo_lms_datos = 2;
                        if(is_numeric($variables->edadSemanas) && ($variables->edadSemanas<13)){
                            $lmsDatos->tipo_r = 'Semana';
                            $lmsDatos->r = $variables->edadSemanas;
                        }else{
                            $lmsDatos->tipo_r = 'Mes';
                            $lmsDatos->r = round($variables->edadMeses);
                        }
                        //TALLA PARA LA EDAD
                        $clasificacion->tallaedad = $this->calcularZ($lmsDatos, $variables);
                    }

                    if(is_numeric($variables->peso)){
                        $lmsDatos->x = $variables->peso;
                        $lmsDatos->id_tipo_lms_datos = 4;
                        if(is_numeric($variables->edadSemanas) && ($variables->edadSemanas<13)){
                            $lmsDatos->tipo_r = 'Semana';
                            $lmsDatos->r = $variables->edadSemanas;
                        }else{
                            $lmsDatos->tipo_r = 'Mes';
                            $lmsDatos->r = round($variables->edadMeses);
                        }
                        //PESO PARA LA EDAD
                        $clasificacion->pesoedad = $this->calcularZ($lmsDatos, $variables);

                        if(is_numeric($variables->talla) && $variables->talla>=45){
                            //PESO PARA LA TALLA
                            $lmsDatos->tipo_r = 'Centimetro';
                            $lmsDatos->r = $variables->talla;
                            if($variables->edadMeses<24){
                                $lmsDatos->id_tipo_lms_datos = 1;
                            }else if($variables->edadMeses>=24 && $variables->edadMeses<60){
                                $lmsDatos->id_tipo_lms_datos = 6;
                            }
                            $clasificacion->pesotalla = $this->calcularZ($lmsDatos, $variables);
                        }
                    }

                    if(is_numeric($variables->peso) && is_numeric($variables->talla)){
                        //IMC
                        $variables->imc = round(($variables->peso / pow (($variables->talla / 100),2)),1);
                        $clasificacion->imc = $variables->imc;

                        $lmsDatos->x = $variables->imc;
                        $lmsDatos->id_tipo_lms_datos = 5;
                        if(is_numeric($variables->edadSemanas) && ($variables->edadSemanas<13)){
                            $lmsDatos->tipo_r = 'Semana';
                            $lmsDatos->r = $variables->edadSemanas;
                        }else{
                            $lmsDatos->tipo_r = 'Mes';
                            $lmsDatos->r = round($variables->edadMeses);
                        }
                        //IMC PARA LA EDAD
                        $clasificacion->imcedad = $this->calcularZ($lmsDatos, $variables);
                    }

                    if(is_numeric($variables->pc)){
                        $lmsDatos->x = $variables->pc;
                        $lmsDatos->id_tipo_lms_datos = 3;
                        if(is_numeric($variables->edadSemanas) && ($variables->edadSemanas<13)){
                            $lmsDatos->tipo_r = 'Semana';
                            $lmsDatos->r = $variables->edadSemanas;
                        }else{
                            $lmsDatos->tipo_r = 'Mes';
                            $lmsDatos->r = round($variables->edadMeses);
                        }
                        //PC PARA LA EDAD
                        $clasificacion->pcedad = $this->calcularZ($lmsDatos, $variables);
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

    public function calcularZ($lmsDatos, $variables){
        $datos = new class{
            var $tipo_diagnostico_id = '';
            var $zs = '';
            var $dv = '';
            var $cn = '';
            var $clase = '';
        };
        $fz = LmsDato::where('genero','=',$lmsDatos->genero,'and')->where('tipo_r','=',$lmsDatos->tipo_r,'and')->where('id_tipo_lms_datos','=',$lmsDatos->id_tipo_lms_datos,'and')->where('r','=',$lmsDatos->r)->first();
        if ($fz){
            //Z-SCORE
            $datos->zs = (pow(((double)$lmsDatos->x / (double)$fz->m),(double)$fz->l)-1)/((double)$fz->l * (double)$fz->s);

//            var_dump($datos->zs.' -- '.$lmsDatos->id_tipo_lms_datos);
            if($variables->edadMeses<60){
                if($fz->id_tipo_lms_datos==4){
                    $datos->tipo_diagnostico_id = 5;
                    //DATOS VALIDOS
                    if($datos->zs >= 5){
                        $datos->dv = 'DATOS EXTREMOS ALTOS';
                    }else if($datos->zs <= -5){
                        $datos->dv = 'DATOS EXTREMOS BAJOS';
                    }else if($datos->zs < 5 && $datos->zs > -5){
                        $datos->dv = 'EN EL RANGO';
                    }

                    //CLASIFICACION NUTRICIONAL
                    if($datos->zs < -2){
                        $datos->cn = 'DESNUTRICIÓN GLOBAL';
                        $datos->clase = 'bg-danger';
                    }else if($datos->zs >= -2 && $datos->zs < -1){
                        $datos->cn = 'RIESGO DE DESNUTRICIÓN GLOBAL';
                        $datos->clase = 'bg-warning';
                    }else if($datos->zs >= -1 && $datos->zs <= 1){
                        $datos->cn = 'PESO ADECUADO PARA LA EDAD';
                        $datos->clase = 'bg-success';
                    }else if($datos->zs > 1){
                        $datos->cn = 'NO APLICA... VERIFICAR CON IMC/E';
                        $datos->clase = 'bg-info';
                    }
                }else
                    if($fz->id_tipo_lms_datos==1 || $fz->id_tipo_lms_datos==6){
                        $datos->tipo_diagnostico_id = 2;
                    //DATOS VALIDOS
                    if($datos->zs >= 5){
                        $datos->dv = 'DATOS EXTREMOS ALTOS';
                    }else if($datos->zs <= -5){
                        $datos->dv = 'DATOS EXTREMOS BAJOS';
                    }else if($datos->zs < 5 && $datos->zs > -5){
                        $datos->dv = 'EN EL RANGO';
                    }

                    //CLASIFICACION NUTRICIONAL
                    if($datos->zs < -3){
                        $datos->cn = 'DESNUTRICION AGUDA SEVERA';
                        $datos->clase = 'bg-danger';
                    }else if($datos->zs < -2 && $datos->zs >= -3){
                        $datos->cn = 'DESNUTRICION AGUDA MODERADA';
                        $datos->clase = 'bg-danger';
                    }else if($datos->zs >= -2 && $datos->zs < -1){
                        $datos->cn = 'RIESGO DE PESO BAJO';
                        $datos->clase = 'bg-warning';
                    }else if($datos->zs >= -1 && $datos->zs <= 1){
                        $datos->cn = 'PESO ADECUADO PARA LA TALLA';
                        $datos->clase = 'bg-success';
                    }else if($datos->zs > 1 && $datos->zs <= 2){
                        $datos->cn = 'RIESGO DE SOBREPESO';
                        $datos->clase = 'bg-warning';
                    }else if($datos->zs > 2 && $datos->zs <= 3){
                        $datos->cn = 'SOBREPESO';
                        $datos->clase = 'bg-danger';
                    }else if($datos->zs > 3){
                        $datos->cn = 'OBESIDAD';
                        $datos->clase = 'bg-danger';
                    }
                }else
                    if($fz->id_tipo_lms_datos==2){
                        $datos->tipo_diagnostico_id = 3;
                    //DATOS VALIDOS
                    if($datos->zs >= 6){
                        $datos->dv = 'DATOS EXTREMOS ALTOS';
                    }else if($datos->zs <= -6){
                        $datos->dv = 'DATOS EXTREMOS BAJOS';
                    }else if($datos->zs < 6 && $datos->zs > -6){
                        $datos->dv = 'EN EL RANGO';
                    }

                    //CLASIFICACION NUTRICIONAL
                    if($datos->zs < -2){
                        $datos->cn = 'TALLA BAJA PARA LA EDAD';
                        $datos->clase = 'bg-danger';
                    }else if($datos->zs >= -2 && $datos->zs < -1){
                        $datos->cn = 'RIESGO DE TALLA BAJA';
                        $datos->clase = 'bg-warning';
                    }else if($datos->zs >= -1){
                        $datos->cn = 'TALLA ADECUADA PARA LA EDAD';
                        $datos->clase = 'bg-success';
                    }
                }else
                    if($fz->id_tipo_lms_datos==5){
                        $datos->tipo_diagnostico_id = 6;
                    //DATOS VALIDOS
                    if($datos->zs >= 5){
                        $datos->dv = 'DATOS EXTREMOS ALTOS';
                    }else if($datos->zs <= -5){
                        $datos->dv = 'DATOS EXTREMOS BAJOS';
                    }else if($datos->zs < 5 && $datos->zs > -5){
                        $datos->dv = 'EN EL RANGO';
                    }

                    //CLASIFICACION NUTRICIONAL
                    if($datos->zs <= 1){
                        $datos->cn = '*NO APLICA';
                        $datos->clase = 'bg-info';
                    }else if($datos->zs > 1 && $datos->zs <= 2){
                        $datos->cn = 'RIESGO DE SOBREPESO';
                        $datos->clase = 'bg-warning';
                    }else if($datos->zs > 2 && $datos->zs <= 3){
                        $datos->cn = 'SOBREPESO';
                        $datos->clase = 'bg-danger';
                    }else if($datos->zs > 3){
                        $datos->cn = 'OBESIDAD';
                        $datos->clase = 'bg-danger';
                    }
                }else
                    if($fz->id_tipo_lms_datos==3){
                        $datos->tipo_diagnostico_id = 4;
                    //DATOS VALIDOS
                    if($datos->zs >= 5){
                        $datos->dv = 'DATOS EXTREMOS ALTOS';
                    }else if($datos->zs <= -5){
                        $datos->dv = 'DATOS EXTREMOS BAJOS';
                    }else if($datos->zs < 5 && $datos->zs > -5){
                        $datos->dv = 'EN EL RANGO';
                    }

                    //CLASIFICACION NUTRICIONAL
                    if($datos->zs < -2){
                        $datos->cn = 'FACTOR DE RIESGO PARA EL NEURO DESARROLLO MICRO';
                        $datos->clase = 'bg-warning';
                    }else if($datos->zs >= -2 && $datos->zs <= 2){
                        $datos->cn = 'NORMAL';
                        $datos->clase = 'bg-success';
                    }else if($datos->zs > 2){
                        $datos->cn = 'FACTOR DE RIESGO PARA EL NEURO DESARROLLO MACRO';
                        $datos->clase = 'bg-warning';
                    }
                }
            }
            return $datos;
        }
    }

    public function procesaEdadPacientes(Request $request)
    {
        DB::beginTransaction();
        try {
            $request =  json_decode($request->getContent());
//            var_dump($request->paciente->id);
            $persona = Persona::find($request->paciente->id);
            $ahora = $request->consulta->fecha_consulta?$request->consulta->fecha_consulta:Carbon::now('America/Bogota');
            $persona->rango_edad_id = $this->calcularRangoEdad($request->paciente->fecha_nacimiento, $ahora);
            $persona->save();
            DB::commit();
            $edad = $this->calcularEdad($request->paciente->fecha_nacimiento, $ahora);
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
            $ahora = Carbon::now('America/Bogota');
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
                $persona->rango_edad_id = $this->calcularRangoEdad($request->fecha_nacimiento, $ahora);
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
                $persona->rango_edad_id = $this->calcularRangoEdad($request->fecha_nacimiento, $ahora);
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
//            var_dump($request['clasificacion']);
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
                $ahora = $request['consulta']['fecha_consulta']?$request['consulta']['fecha_consulta']:Carbon::now('America/Bogota');
                $persona->rango_edad_id = $this->calcularRangoEdad($request['paciente']['fecha_nacimiento'],$ahora);

                $persona->ProgramaSocial()->detach();
                $persona->ProgramaSocial()->attach($request['paciente']['programa_social']);
                $persona->save();

                $oldConsultas = Consulta::where('persona_id','=',$persona->id, 'and')
                ->where('activa','=',1)
                ->get();

                foreach ($oldConsultas as $key=>$oldConsulta ) {
                    $oldConsulta->activa = 0;
                    $oldConsulta->save();
                }

                $consulta = new Consulta();
                $consulta->servicio_upgd_id = $request['consulta']['servicio_upgd_id'];
                $consulta->fecha_consulta = $request['consulta']['fecha_consulta'];
                $consulta->persona_id = $persona->id;
                $consulta->gestante = 0;
                $consulta->activa = 1;
                $consulta->user_id = Auth::user()->id;
                $consulta->save();

                foreach ($request['consulta']['detalle_consulta'] as $key=>$detalle ) {
                    $detalleconsulta = new DetalleConsulta();
                    $detalleconsulta->consulta_id = $consulta->id;
                    $detalleconsulta->rango_edad_variable_id = $detalle['rango_edad_variable_id'];
                    $detalleconsulta->valor = $detalle['valor'];
                    $detalleconsulta->save();
                }

                foreach ($request['clasificacion'] as $key=>$clasificacion ) {
                    if($key!='imc'){
//                        var_dump($clasificacion);
                        $diagnostico = new DiagnosticoNutricional();
                        $diagnostico->consulta_id = $consulta->id;
                        $diagnostico->tipo_diagnostico_id = $clasificacion['tipo_diagnostico_id'];
                        $diagnostico->zs = $clasificacion['zs'];
                        $diagnostico->dv = $clasificacion['dv'];
                        $diagnostico->cn = $clasificacion['cn'];
                        $diagnostico->clase = $clasificacion['clase'];
                        $diagnostico->save();
                    }
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
