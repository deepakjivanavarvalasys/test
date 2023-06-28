<?php

namespace App\Exports;

use App\Models\Campaign;
use App\Models\VendorLead;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class VendorLeadExport implements FromCollection, WithHeadings, WithEvents, WithColumnFormatting
{
    private $campaignId;

    public function __construct($campaignId = null)
    {
        $this->campaignId = $campaignId;
    }

    public function collection()
    {
        $exportData = [];

        //get data
        $resultCampaign = Campaign::find($this->campaignId);

        $query = VendorLead::query();
        $query->whereCampaignId($this->campaignId);
        $query->whereStatus(1);
        $query->whereNotNull('send_date');
        $query->whereNull('qc_download_date');
        $resultLeads = $query->get();

        //convert to final array
        foreach($resultLeads as $key => $lead) {
            $exportData[$key]['Vendor'] = $lead->vendor->name;
            $exportData[$key]['Date'] = date('d-M-Y', strtotime($lead->created_at));
            $exportData[$key]['Campaign'] = $resultCampaign->name;
            $exportData[$key]['First Name'] = $lead->first_name;
            $exportData[$key]['Last Name'] = $lead->last_name;
            $exportData[$key]['Company Name'] = $lead->company_name;
            $exportData[$key]['Email Address'] = $lead->email_address;
            $exportData[$key]['Specific Title'] = $lead->specific_title;
            $exportData[$key]['Job Level'] = $lead->job_level;
            $exportData[$key]['Job Role'] = $lead->job_role;
            $exportData[$key]['Phone Number'] = $lead->phone_number;
            $exportData[$key]['Address 1'] = $lead->address_1;
            $exportData[$key]['Address 2'] = $lead->address_2;
            $exportData[$key]['City'] = $lead->city;
            $exportData[$key]['State'] = $lead->state;
            $exportData[$key]['Zipcode'] = $lead->zipcode;
            $exportData[$key]['Country'] = $lead->country;
            $exportData[$key]['Industry'] = $lead->industry;
            $exportData[$key]['Employee Size'] = $lead->employee_size;
            $exportData[$key]['Employee Size 2'] = $lead->employee_size_2;
            $exportData[$key]['Revenue'] = $lead->revenue;
            $exportData[$key]['Company Domain'] = $lead->company_domain;
            $exportData[$key]['Website'] = $lead->website;
            $exportData[$key]['Company Linkedin URL'] = $lead->company_linkedin_url;
            $exportData[$key]['LinkedIn Profile Link'] = $lead->linkedin_profile_link;
            $exportData[$key]['LinkedIn Profile Link SN'] = $lead->linkedin_profile_sn_link;
            $exportData[$key]['Comment'] = $lead->comment;
            $exportData[$key]['Comments'] = '';
            $exportData[$key]['QA Status'] = '';
            $exportData[$key]['Comment'] = '';
            $exportData[$key]['EBB Status'] = '';
            $exportData[$key]['Auditor'] = Auth::user()->full_name;
            $exportData[$key]['QA Date'] = date('d-M-Y');
            //$exportData[$key]['QA Date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::dateTimeToExcel(now());

        }
        //dd($exportData);
        return collect($exportData);
    }

    public function headings(): array
    {
        return [
            'Vendor',
            'Date',
            'Campaign',
            'First Name',
            'Last Name',
            'Company Name',
            'Email Address',
            'Specific Title',
            'Job Level',
            'Job Role',
            'Phone Number',
            'Address 1',
            'Address 2',
            'City',
            'State',
            'Zipcode',
            'Country',
            'Industry',
            'Employee Size',
            'Employee Size 2',
            'Revenue',
            'Company Domain',
            'Website',
            'Company Linkedin URL',
            'LinkedIn Profile Link',
            'LinkedIn Profile Link SN',
            'Comments',
            'QA Status',
            'Comment',
            'EBB Status',
            'Auditor',
            'Date',

        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);

                $event->sheet->getStyle('K')->getNumberFormat()->setFormatCode('#0');

                /*$event->sheet->getStyle('M1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => '#FFFAA9']
                    ]
                ]);*/
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            //'K' => NumberFormat::FORMAT_GENERAL,
        ];
    }

}
