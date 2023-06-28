<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Repository\CampaignStatusRepository\CampaignStatusRepository;
use App\Repository\CampaignTypeRepository\CampaignTypeRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $data;
    /**
     * @var CampaignStatusRepository
     */
    private $campaignStatusRepository;
    /**
     * @var CampaignTypeRepository
     */
    private $campaignTypeRepository;

    public function __construct(
        CampaignStatusRepository $campaignStatusRepository,
        CampaignTypeRepository $campaignTypeRepository
    )
    {
        $this->data = array();
        $this->campaignStatusRepository = $campaignStatusRepository;

        $this->data['resultCampaignStatuses'] = $this->campaignStatusRepository->get(array('status' => 1));
        $this->campaignTypeRepository = $campaignTypeRepository;
    }

    public function index(Request $request)
    {
        return view('manager.dashboard', $this->data);
    }

    public function getCounts(Request $request)
    {
        $response = array();

        foreach($this->data['resultCampaignStatuses'] as $campaignStatus) {
            $response[$campaignStatus->slug] = 0;
        }

        $query = Campaign::query();
        $query->whereNull('parent_id');
        $resultCampaigns = $query->get();

        foreach($resultCampaigns as $campaign) {
            if($campaign->children->count()) {
                $campaign_status_id = $campaign->children[0]->campaign_status_id;
            } else {
                $campaign_status_id = $campaign->campaign_status_id;
            }

            switch ($campaign_status_id) {
                case 1:  $response['live']++;break;
                case 2:  $response['paused']++;break;
                case 3:  $response['cancelled']++;break;
                case 4:  $response['delivered']++;break;
                case 5:  $response['reactivated']++;break;
                case 6:  $response['shortfall']++;break;
            }
        }

        return response()->json($response);
    }

    public function getRadialChartData(Request $request)
    {
        $response['chartData'] = array();

        $campaignTypes = $this->campaignTypeRepository->get(['status' => '1']);

        $chartData = array();

        foreach($this->data['resultCampaignStatuses'] as $campaignStatus) {
            $chartData[$campaignStatus->id]['status'] = $campaignStatus->name;
            $chartData[$campaignStatus->id]['count'] = 0;

            //initialize subChart Data
            foreach ($campaignTypes as $key => $campaignType) {
                $chartData[$campaignStatus->id]['subData'][$key]['name'] = $campaignType->name;
                $chartData[$campaignStatus->id]['subData'][$key]['value'] = 0;
            }
        }

        $query = Campaign::query();
        $query->whereNull('parent_id');
        $resultCampaigns = $query->get();

        foreach($resultCampaigns as $campaign) {
            if($campaign->children->count()) {
                $campaign_status_id = $campaign->children[0]->campaign_status_id;
            } else {
                $campaign_status_id = $campaign->campaign_status_id;
            }

            $chartData[$campaign_status_id]['count']++;

            //subChart Data
            foreach ($campaignTypes as $key => $campaignType) {
                if($campaign->campaign_type_id == $campaignType->id) {
                    $chartData[$campaign_status_id]['subData'][$key]['value']++;
                }
            }
        }
        $response['chartData'] = $chartData;
        $response['message'] = "Record fetched successfully.";
        return response()->json($response);
    }
}
