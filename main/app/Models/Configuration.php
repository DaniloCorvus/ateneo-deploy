<?php

namespace App\Models;

use Egulias\EmailValidator\Exception\ConsecutiveAt;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpParser\ErrorHandler\Collecting;

class Configuration extends Model
{

    protected $table = 'Configuracion';
    protected $primaryKey = 'Id_Configuracion';
    public $timestamps = false;

    public function responsableRecursosHumanos()
    {
        return $this->hasOne(Funcionario::class, 'Identificacion_Funcionario', 'Responsable_Rh');
    }

    function prefijoConsecutivo($index)
    {
        $prop = "Prefijo_$index";
        return  $this->$prop;
    }

    function guardarConsecutivoConfig($index, $consecutivo)
    {

        $this->$index = $consecutivo += 1;
        $this->save();
    }

    function getConsecutivo($mod, $tipo_consecutivo)
    {
        // sleep(strval( rand(2, 8)));
        $query = "SELECT MAX(N.Codigo) AS Codigo FROM ( SELECT Codigo FROM $mod ORDER BY Id_$mod DESC LIMIT 10 )N ";
        $res = DB::select($query);
        $res = $res[0];


        $prefijo = $this->prefijoConsecutivo($tipo_consecutivo);
        $NumeroCodigo = substr($res->Codigo, strlen($prefijo));

        $NumeroCodigo += 1;

        $cod = $prefijo . $NumeroCodigo;

        $query = "SELECT Id_$mod AS ID FROM $mod WHERE Codigo = '$cod'";

        $res2 = DB::select($query);

        $res2 = $res2 ? $res2[0] : $res2;
        if ($res2) {
            sleep(strval(rand(0, 3)));
            $this->getConsecutivo($mod, $tipo_consecutivo);
        }

        $this->guardarConsecutivoConfig($tipo_consecutivo, $NumeroCodigo);

        return $cod;
    }

    function getConsecutivoPro($mod, $tipo_consecutivo)
    {
        // sleep(strval( rand(2, 8)));
        $query = "SELECT MAX(N.code) AS code FROM ( SELECT code FROM $mod ORDER BY Id_$mod DESC LIMIT 10 )N ";
        $res = DB::select($query);
        $res = $res[0];


        $prefijo = $this->prefijoConsecutivo($tipo_consecutivo);
        $NumeroCodigo = substr($res->code, strlen($prefijo));

        $NumeroCodigo += 1;

        $cod = $prefijo . $NumeroCodigo;

        $query = "SELECT Id_$mod AS ID FROM $mod WHERE code = '$cod'";

        $res2 = DB::select($query);

        $res2 = $res2 ? $res2[0] : $res2;
        if ($res2) {
            sleep(strval(rand(0, 3)));
            $this->getConsecutivo($mod, $tipo_consecutivo);
        }

        $this->guardarConsecutivoConfig($tipo_consecutivo, $NumeroCodigo);

        return $cod;
    }

    function Consecutivo($index)
    {
        $num_cotizacion = $this->$index;

        $consecutivo = number_format((int) $this->$index, 0, "", "");
        $this->$index = $consecutivo + 1;
        $this->save();

        $d = "Prefijo_" . $index;
        $cod = $this->$d;
        $cod . sprintf("%05d", $num_cotizacion);

        return $cod;
    }


    function consecutivoLevel1($tipo_consecutivo)
    {

        $prefix = 'Prefijo_' . $tipo_consecutivo;
        $numberPos = 'Consecutivo_' . $tipo_consecutivo;

        $data = new Collection();
        $data->prefix = $this->$prefix;
        $data->number = $this->$numberPos + 1;
        $data->code = $this->$prefix . ($this->$numberPos + 1);
       
        return  $data;
    }

    function savePrefix($tipo_consecutivo)
    {
        $numberPos = 'Consecutivo_' . $tipo_consecutivo;
        $this->$numberPos = $this->$numberPos + 1;
        $this->save();
    }
}
