<?php

namespace App\Http\Controllers;

// use App\Attention;
// use App\Invoice;
// use Maatwebsite\Excel\Concerns\FromQuery;
// use Maatwebsite\Excel\Concerns\Exportable;

// class InvoicesExport implements FromQuery
// {
//     public function __construct($data)
//     {
//         $this->data = $data;
//     }

//     use Exportable;

//     public function query()
//     {
//         return Attention::all();
//     }
// }

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class InvoicesExport implements FromView, WithEvents, ShouldAutoSize, WithChunkReading
{
    
     use Exportable;
     
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        
         
        return view('exports.' . $this->data['type'], [
            'data' => $this->data['data']
        ]);
    }
    
        public function chunkSize(): int
    {
        return 500;
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

             