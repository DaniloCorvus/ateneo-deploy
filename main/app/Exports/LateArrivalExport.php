<?php

namespace App\Exports;
use Illuminate\Database\Eloquent\Collection;

use App\Models\LateArrival;
use App\Services\LateArrivalService;
use GuzzleHttp\Psr7\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LateArrivalExport implements FromCollection,  WithHeadings, ShouldAutoSize, WithEvents
{
    private $dates ;
    public function __construct($dates)
    {   
        $this->dates = $dates;
    }
    public function headings(): array
    {
        return [
          'Área', 'Funcionario', 'Fecha', 'Entrada Turno', 'Entrada real', 'Tiempo retraso'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
       
        
         return   new Collection( 
            LateArrivalService::getPeopleDownload($this->dates) 
            ) ;
         
    }

    public function registerEvents(): array
    {
        
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }

}
