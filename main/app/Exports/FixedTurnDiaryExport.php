<?php

namespace App\Exports;

use App\Services\DiaryService;
use Maatwebsite\Excel\Concerns\FromCollection;


use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class FixedTurnDiaryExport implements FromCollection,  WithHeadings, ShouldAutoSize, WithEvents
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
            'Entrada 1',

            'Salida 1',

            'Entrada 2',

            'Salida 2',

            'Trabajado'
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        $lates =  DiaryService::getDiariesFixedDowload($this->dates);


        return $lates;
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
