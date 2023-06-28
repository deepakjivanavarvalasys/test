<?php

namespace App\Exports;

use App\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Campaign\enum\CampaignStatus;
use Modules\Campaign\models\LeadDetail;
use Modules\Campaign\models\PacingDetail;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CampaignExport implements FromCollection, WithHeadings, WithEvents
{
    private $campaignId;

    public function __construct($campaignId = null)
    {
        $this->campaignId = $campaignId;
    }

    public function collection()
    {
        $exportData = [];

        $leadDetails = LeadDetail::query();

        if(isset($this->campaignId) && null != $this->campaignId) {
            $leadDetails = $leadDetails->whereCampaignId(base64_decode($this->campaignId));
        }

        $leadDetails = $leadDetails->with(['pacingDetails', 'campaign', 'campaign.campaignType']);
        $leadDetails = $leadDetails->get();

        foreach($leadDetails as $campaignIndex => $leadDetail) {

            $exportData[$campaignIndex]['campaign_id'] = $leadDetail->campaign_id;
            $exportData[$campaignIndex]['vmail_id'] = $leadDetail->campaign->v_mail_campaign_id;
            $exportData[$campaignIndex]['campaign_name'] = $leadDetail->campaign->name;
            $exportData[$campaignIndex]['campaign_type'] = $leadDetail->campaign->campaignType->name;
            $exportData[$campaignIndex]['start_date'] = $leadDetail->start_date;
            $exportData[$campaignIndex]['end_date'] = $leadDetail->end_date;

            $percentage = ($leadDetail->deliver_count / $leadDetail->allocation) * 100;
            $exportData[$campaignIndex]['completion_percentage'] = number_format($percentage, 2);

            $exportData[$campaignIndex]['delivery_count'] = $leadDetail->deliver_count;
            $exportData[$campaignIndex]['allocation'] = $leadDetail->allocation;
            $exportData[$campaignIndex]['short_fall'] = $leadDetail->shortfall_count;
            $exportData[$campaignIndex]['status'] = CampaignStatus::CAMPAIGN_STATUS[$leadDetail->campaign_status];
            $exportData[$campaignIndex]['pacing'] = $leadDetail->pacing;

            foreach($leadDetail->pacingDetails as $index => $pacingDetail) {

                $exportData[$campaignIndex]["date$index"] = $pacingDetail->date;
                $exportData[$campaignIndex]["sub_allocation$index"] = $pacingDetail->sub_allocation;
            }
        }

        return collect($exportData);
    }

    public function headings(): array
    {
        $headings = [

            'Campaign Id',
            'V-Mail ID',
            'Campaign Name',
            'Campaign Type',
            'Start Date',
            'End Date',
            'Completion %',
            'Deliver Count',
            'Allocation',
            'Shortfall',
            'Status',
            'Pacing',
        ];

        $pacingDetail = PacingDetail::query();

        if(isset($this->campaignId) && null != $this->campaignId) {
            $pacingDetail = $pacingDetail->whereCampaignId(base64_decode($this->campaignId));
        }

        $pacingDetail = $pacingDetail->groupBy('lead_detail_id');
        $pacingDetail = $pacingDetail->orderBy(DB::raw('COUNT(*)'), 'DESC');
        $pacingDetail = $pacingDetail->count();

        for($i = 1; $i <= $pacingDetail; $i++) {

            $headings[] = 'SubDate' . $i;
            $headings[] = 'Sub Allocation' . $i;
        }

        return $headings;
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
                /*$event->sheet->getStyle('M1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => '#FFFAA9']
                    ]
                ]);*/
            },
        ];
    }

}
