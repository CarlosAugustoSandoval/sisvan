<?php
namespace App\Http\helpers;
use Illuminate\Support\Facades\DB;

/**
 * Created by PhpStorm.
 * User: ojo
 * Date: 15/09/17
 * Time: 12:29 PM
 */
trait EnumsTrait
{
    public static function getEnumValues($table, $column) {

        $type = DB::select( DB::raw("SHOW COLUMNS FROM ".$table." WHERE Field = '".$column."'") )[0]->Type;
        $dataTypes = explode("(", $type);
        $dataType = $dataTypes[0];
        if ($dataType == 'enum' || $dataType=='set'){

            preg_match('/^'.$dataType.'\((.*)\)$/', $type, $matches);
            $enum = array();
            foreach( explode(',', $matches[1]) as $value )
            {
                $v = trim( $value, "'" );
                $enum = array_add($enum, $v, $v);
            }
            return $enum;
        }

        return;

    }

}