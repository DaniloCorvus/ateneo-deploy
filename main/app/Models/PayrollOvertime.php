<?php

namespace App\Models;

use App\Http\Libs\Nomina\Calculos\CalculoExtra;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollOvertime extends Model
{
    protected static $funcionario;

    protected $guarded = ['id'];

    public function scopePrefijo($query, $indice)
    {
   
        return $query->where('prefix', $indice)->first(['prefix','percentage'])['percentage'];
      
    }

    public static function enviarPorcentajes($indices = [])
    {
        $porcentajes = [];
        foreach ($indices as $indice) {
          
            $porcentajes[$indice] = self::prefijo($indice);
        }
      
        return $porcentajes;
    }
    /**
     * Settea la propiedad funcionario filtrando al funcionario que se pase por el parámetro $id,
     * retorna una nueva instancia de la clase 
     *
     * @param integer $id
     * @return NominaExtras
     */
    public static function extrasFuncionarioWithId($id)
    {
        self::$funcionario = Person::find($id);

        return new self;
    }

     /**
     * Obtiene los prefijos de la tabla nomina_horas_extras, filtra los reportes de horas extras que tenga
     * el funcionario entre el parámetro fecha de inicio y el de fecha de fin , obtiene su salario base 
     * y realiza el cálculo correspondiente con esos tres valores (Ver la clase Cálculo extras)
     * 
     * @param integer $fechaInicio
     * @param integer $fechaFin
     * @return Illuminate\Support\Facades\Collection
     */


    public function fromTo($fechaInicio, $fechaFin)
    {
        $prefijos = PayrollOvertime::get(['prefix'])->keyBy('prefix')->keys();
        $reporteExtras =  ExtraHourReport::where('person_id', self::$funcionario->id)->whereBetween('date', [$fechaInicio, $fechaFin]);
        $salarioPartial = Person::with('contractultimate')->where('id', self::$funcionario->id)->firstOrFail();
        $salario = $salarioPartial->contractultimate->salary;
        
        $calculoExtras = new CalculoExtra($prefijos, $reporteExtras, $salario);
        $calculoExtras->calcularCantidadHoras();
        $calculoExtras->setHorasReportadas($calculoExtras->getCantidadHoras());
        $calculoExtras->setPorcentajes(
            PayrollOvertime::enviarPorcentajes($calculoExtras->getPrefijos())
        );
        $calculoExtras->calcularTotalHoras();
        $calculoExtras->calcularValorTotalHoras();
       
        return $calculoExtras->crearColeccion();
    }
}
