<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ArrayToExcel implements FromArray, WithHeadings, WithEvents
{
    use Exportable;

    private $data;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $dataToExport = array();
        foreach ($this->data as $key => $row) {
            $row['data'][11] = $row['errorMessage'];
            //dd($key, $row['data'], $row['invalidCells']);
            array_push($dataToExport, $row['data']);
        }
        return $dataToExport;
    }

    public function headings(): array
    {
        $headings = [
            'Campaign Name',
            'V-Mail Campaign ID',
            'Campaign Type',
            'Campaign Filter',
            'Country(s)',
            'Start Date',
            'End Date',
            'Allocation',
            'Status',
            'Pacing',
            'Deliver Count',
            'Reason(s)',
        ];

        return $headings;
    }

    public function registerEvents(): array
    {
        $dataToStyle = array();
        $columns = array(
            0 => 'A', //'Campaign Name',
            1 => 'B', //'V-Mail Campaign ID',
            2 => 'C', //'Campaign Type',
            3 => 'D', //'Campaign Filter',
            4 => 'E', //'Country(s)',
            5 => 'F', //'Start Date',
            6 => 'G', //'End Date',
            7 => 'H', //'Allocation',
            8 => 'I', //'Status',
            9 => 'J', //'Pacing',
            10 => 'K', //'Delivery Count',
            11 => 'L', //'Reason(s)',
        );

        foreach ($this->data as $key => $row) {
            foreach ($row['invalidCells'] as $key2 => $value) {
                array_push($dataToStyle,$columns[$key2].($key+2));
            }
        }


        return [
            AfterSheet::class    => function(AfterSheet $event) use($dataToStyle) {
                $event->sheet->getStyle('1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);

                foreach ($dataToStyle as $value) {
                    $event->sheet->getStyle($value)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => [
                                'rgb' => 'ff0000',
                            ]
                        ]
                    ]);
                }

            },
        ];
    }


}
