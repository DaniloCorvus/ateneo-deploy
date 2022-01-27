<?php

namespace App\Http\Libs\Nomina\Calculos;

use Illuminate\Support\Collection;

/** Clase para calcular las deducciones */
class CalculoDeducciones implements Coleccion
{
    protected $deducciones;
    protected $totalDeducciones;
    protected $deduccionesRegistradas;
    protected $suma = 0;
    protected $conceptoAux = 0;

    /**
     * Constructor
     *
     * @param Collection $deducciones
     */
    public function __construct(Collection $deducciones)
    {
        $this->deducciones = $deducciones;
        $this->deduccionesRegistradas = collect([]);
        $this->totalDeducciones = 0;
        $this->getDeduccionesCustom();
    }

    public function getDeducciones()
    {
        return $this->deducciones;
    }

    /**
     * Retornar las deducciones registradas en el container (array)
     *
     * @return Array
     */
    public function getDeduccionesRegistradas()
    {
        if (collect($this->deduccionesRegistradas)->isEmpty()) {
            return null;
        }
        return $this->deduccionesRegistradas;
    }

    /**
     * Calcular el valor total de las deducciones
     *
     * @return void
     */
    public function calcularTotalDeducciones()
    {
        if ($this->deducciones->isNotEmpty()) {
            $this->totalDeducciones = $this->deducciones->sum('value');
        }
    }

    /**
     * Getter para el total de las deducciones
     *
     * @return Array
     */
    public function getTotalDeducciones()
    {
        return $this->totalDeducciones;
    }

    /**
     * Aplicar el contract de la interfaz, crear la colección
     *
     * @return Illuminate\Support\Collection
     */
    public function crearColeccion()
    {
        return new Collection([
            'valor_total' => $this->getTotalDeducciones(),
            'deducciones' => $this->getDeduccionesRegistradas()
        ]);
    }

    public function crearCustomColeccion()
    {
        return new Collection([
            'valor_total' => $this->getTotalDeducciones(),
            'deducciones' => $this->deducciones
        ]);
    }

    /**
     * @return data 
     * 
     */

    public function getDeduccionesCustom()
    {
     


        foreach ($this->deducciones->groupBy('deduccion.concept') as  $deduccion) {
            $this->suma = 0;
           # dd($deduccion);
            foreach ($deduccion as  $concepto) {
                $this->suma += $concepto->value;
                $this->conceptoAux = $concepto;
            }
            $this->deduccionesRegistradas->put($this->conceptoAux->deduccion->concept, $this->suma);
        }
    }
}
