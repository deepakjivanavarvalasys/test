<?php

namespace App\Exports;

use App\Models\AgentLead;
use App\Models\Campaign;
use App\Models\CampaignAssignRATL;
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

class CampaignLeadExport implements FromCollection, WithHeadings, WithEvents, WithColumnFormatting
{
    private $campaign_id;
    private $filters;

    public function __construct($campaign_id = null, $filters = array())
    {
        $this->campaign_id = $campaign_id;
        $this->filters = $filters;
    }

    public function collection()
    {
        $exportData = array();

        //get data
        $query = AgentLead::query();
        $query->where('campaign_id', $this->campaign_id);

        if(array_key_exists('status', $this->filters)) {
            $query->where('status', $this->filters);
        }

        if(array_key_exists('qc_custom_filter', $this->filters)) {
            $query->whereNotNull('send_date');
            switch ($this->filters['qc_custom_filter']){
                case 'new':
                    $query->whereNull('qc_download_date');
                    break;
                case 'old':
                    $query->whereNotNull('qc_download_date');
                    break;
                case 'all':
                default: break;
            }
        }

        $query->orderBy('ca_agent_id');
        $resultLeads = $query->get();

        if($resultLeads->count()) {
            foreach($resultLeads as $key => $agentLead) {

                $exportData[$key]['RA'] = $agentLead->agent->full_name;
                $exportData[$key]['Date'] = date('d-M-Y', strtotime($agentLead->created_at));
                $exportData[$key]['Campaign'] = $agentLead->campaign->name;
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
                $exportData[$key]['RATL Comments'] = $agentLead->comment_2;
                $exportData[$key]['QC Comments'] = $agentLead->qc_comment;
                $exportData[$key]['Status'] = $agentLead->status ? 'Ok' : 'Rejected';
                $exportData[$key]['QA Date'] = date('d-M-Y');

            }
        }

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
            'RATL Comments',
            'QC Comments',
            'Status',
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
