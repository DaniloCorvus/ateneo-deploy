<?php

namespace App\Exports;

use App\Models\RotatingTurnDiary;
use App\Services\DiaryService;
use Maatwebsite\Excel\Concerns\FromCollection;


use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class RotatingTurnDiaryExport implements FromCollection,  WithHeadings, ShouldAutoSize, WithEvents
{
    private $dates;
    public function __construct($dates)
    {
        $this->dates = $dates;
    }
    public function headings(): array
    {
        return [
            'Área', 'Funcionario', 'Fecha',
            'Entrada',
          
            'Lunch', 
       
            'Lunch 2', 
          
            'Breack 1',
           
            'Breack 2',
          
            'Salida',

            'Trabajado'
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DiaryService::getDiariesRotativeDowload($this->dates);
    }


    public function registerEvents(): array
    {

        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }
}
