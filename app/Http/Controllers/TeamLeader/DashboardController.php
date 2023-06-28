<?php

namespace App\Http\Controllers\TeamLeader;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignAssignRATL;
use App\Repository\CampaignStatusRepository\CampaignStatusRepository;
use App\Repository\CampaignTypeRepository\CampaignTypeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $data;
    /**
     * @var CampaignStatusRepository
     */
    private $campaignStatusRepository;

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

    public function index()
    {
        return view('team_leader.dashboard', $this->data);
    }

    public function getCounts(Request $request)
    {
       $startdate=$request->startdate;
       $enddate=$request->enddate;
        //get RATL's Campaigns
        $resultMyCampaigns = CampaignAssignRATL::where('user_id', Auth::id())->whereIn('status', [0,1])->get();
        //dd($resultMyCampaigns->pluck('campaign_id')->toArray());
        $response = array();

        foreach($this->data['resultCampaignStatuses'] as $campaignStatus) {
            $response[$campaignStatus->slug] = 0;
        }

        $query = Campaign::query();
        $query->whereIn('id', $resultMyCampaigns->pluck('campaign_id')->toArray());
        if($startdate!=0)
        {
        $query->where(function($query) use ($startdate, $enddate){
            $query->where('start_date', '>=', $startdate)
               ->where('end_date', '<=', $enddate);
            });
        }
        $resultCampaigns = $query->get();

        //dd($resultCampaigns->toArray());

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
        //dd($response);
        return response()->json($response);
    }

    public function getRadialChartData(Request $request)
    {
        $startdate=$request->startdate;
        $enddate=$request->enddate;
        $response['chartData'] = array();
        $resultMyCampaigns = CampaignAssignRATL::where('user_id', Auth::id())->whereIn('status', [0,1])->get();

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
        // $query->whereNull('parent_id');
        $query->whereIn('id', $resultMyCampaigns->pluck('campaign_id')->toArray());
        if($startdate!=0)
        {
        $query->where(function($query) use ($startdate, $enddate){
            $query->where('start_date', '>=', $startdate)
               ->where('end_date', '<=', $enddate);
            });
        }
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
