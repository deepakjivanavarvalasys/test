<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ImportLeadsFailedDataExport implements FromCollection, WithHeadings, WithEvents
{
    use Exportable;

    private $failedLeadData;

    public function __construct($failedLeadData = array())
    {
        $this->failedLeadData = $failedLeadData;
    }

    public function collection()
    {
        $exportData = array();

        foreach ($this->failedLeadData as $key => $failedLead) {
            $failedLead['data'][24] = $failedLead['errorMessage'];

            //dd($key, $row['data'], $row['invalidCells']);

            $exportData[] = $failedLead['data'];
        }

        return collect($exportData);
    }

    public function headings(): array
    {
        return [
            'first_name',
            'last_name',
            'company_name',
            'email_address',
            'specific_title',
            'job_level',
            'job_role',
            'phone_number',
            'address_1',
            'address_2',
            'city',
            'state',
            'zipcode',
            'country',
            'industry',
            'employee_size',
            'employee_size_2',
            'revenue',
            'company_domain',
            'website',
            'company_linkedin_url',
            'linkedin_profile_link',
            'linkedin_profile_sn_link',
            'comment',
            'reasons',
        ];
    }

    public function registerEvents(): array
    {
        $dataToStyle = array();

        $columns = array(
            0 => 'A', //first_name
            1 => 'B', //last_name
            2 => 'C', //company_name
            3 => 'D', //email_address
            4 => 'E', //specific_title
            5 => 'F', //job_level
            6 => 'G', //job_role
            7 => 'H', //phone_number
            8 => 'I', //address_1
            9 => 'J', //address_2
            10 => 'K', //city
            11 => 'L', //state
            12 => 'M', //zipcode
            13 => 'N', //country
            14 => 'O', //industry
            15 => 'P', //employee_size
            16 => 'Q', //employee_size_2
            17 => 'R', //revenue
            18 => 'S', //company_domain
            19 => 'T', //website
            20 => 'U', //company_linkedin_url
            21 => 'V', //linkedin_profile_link
            22 => 'W', //linkedin_profile_sn_link
            23 => 'X', //comment
            24 => 'Y', //Failed Reasons
        );

        foreach ($this->failedLeadData as $key => $row) {
            foreach ($row['invalidCells'] as $key2 => $value) {
                $dataToStyle[] = $columns[$key2] . ($key + 2);
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
