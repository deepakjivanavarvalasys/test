<?php

namespace App\Exports;

use App\Models\AgentLead;
use App\Models\Campaign;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AgentLeadExport implements FromCollection, WithHeadings, WithEvents, WithColumnFormatting
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

        $query = AgentLead::query();
        $query->whereCampaignId($this->campaignId);
        $query->whereStatus(1);
        $query->whereNotNull('send_date');
        $query->whereNull('qc_download_date');
        $resultAgentLeads = $query->get();

        //convert to final array
        foreach($resultAgentLeads as $key => $agentLead) {

            $exportData[$key]['RA'] = $agentLead->agent->full_name;
            $exportData[$key]['Date'] = date('d-M-Y', strtotime($agentLead->created_at));
            $exportData[$key]['Campaign'] = $resultCampaign->name;
            $exportData[$key]['First Name'] = $agentLead->first_name;
            $exportData[$key]['Last Name'] = $agentLead->last_name;
            $exportData[$key]['Company Name'] = $agentLead->company_name;
            $exportData[$key]['Email Address'] = $agentLead->email_address;
            $exportData[$key]['Specific Title'] = $agentLead->specific_title;
            $exportData[$key]['Job Level'] = $agentLead->job_level;
            $exportData[$key]['Job Role'] = $agentLead->job_role;
            $exportData[$key]['Phone Number'] = $agentLead->phone_number;
            $exportData[$key]['Address 1'] = $agentLead->address_1;
            $exportData[$key]['Address 2'] = $agentLead->address_2;
            $exportData[$key]['City'] = $agentLead->city;
            $exportData[$key]['State'] = $agentLead->state;
            $exportData[$key]['Zipcode'] = $agentLead->zipcode;
            $exportData[$key]['Country'] = $agentLead->country;
            $exportData[$key]['Industry'] = $agentLead->industry;
            $exportData[$key]['Employee Size'] = $agentLead->employee_size;
            $exportData[$key]['Employee Size 2'] = $agentLead->employee_size_2;
            $exportData[$key]['Revenue'] = $agentLead->revenue;
            $exportData[$key]['Company Domain'] = $agentLead->company_domain;
            $exportData[$key]['Website'] = $agentLead->website;
            $exportData[$key]['Company Linkedin URL'] = $agentLead->company_linkedin_url;
            $exportData[$key]['LinkedIn Profile Link'] = $agentLead->linkedin_profile_link;
            $exportData[$key]['LinkedIn Profile Link SN'] = $agentLead->linkedin_profile_sn_link;
            $exportData[$key]['Comment'] = $agentLead->comment;
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
            'RA',
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
