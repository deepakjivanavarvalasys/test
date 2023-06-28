<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignAssignAgent;
use App\Repository\AgentDataRepository\AgentDataRepository;
use App\Repository\AgentLeadRepository\AgentLeadRepository;
use App\Repository\Campaign\IssueRepository\IssueRepository;
use App\Repository\CampaignAssignRepository\AgentRepository\AgentRepository;
use App\Repository\CampaignRepository\CampaignRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    private $data;
    private $campaignRepository;
    private $agentRepository;
    private $agentLeadRepository;
    /**
     * @var AgentDataRepository
     */
    private $agentDataRepository;
    /**
     * @var IssueRepository
     */
    private $issueRepository;

    public function __construct(
        CampaignRepository $campaignRepository,
        AgentRepository $agentRepository,
        AgentLeadRepository $agentLeadRepository,
        AgentDataRepository $agentDataRepository,
        IssueRepository $issueRepository
    )
    {
        $this->data = array();
        $this->campaignRepository = $campaignRepository;
        $this->agentRepository = $agentRepository;
        $this->agentLeadRepository = $agentLeadRepository;
        $this->agentDataRepository = $agentDataRepository;
        $this->issueRepository = $issueRepository;
    }

    public function index()
    {
        return view('agent.campaign.list', $this->data);
    }

    public function show($id)
    {
        try {
            $this->data['countAgentData'] = $this->agentDataRepository->get(array('ca_agent_ids' => [base64_decode($id)]))->count();
            $this->data['resultCAAgent'] = $this->agentRepository->find(base64_decode($id));
            $this->data['resultCampaign'] = $this->campaignRepository->find($this->data['resultCAAgent']->campaign_id);
            $this->data['resultCampaignIssues'] = $this->issueRepository->get(array('campaign_ids' => [$this->data['resultCAAgent']->campaign_id]));
            //dd($this->data['resultCAAgent']->toArray());
            if(isset($this->data['resultCampaign']->parent_id) && !empty($this->data['resultCampaign']->parent_id)) {
                $this->data['resultCampaignParent'] = $this->campaignRepository->find($this->data['resultCampaign']->parent_id);
            }
            //dd($this->data['resultCAAgent']->toArray());
            return view('agent.campaign.show', $this->data);
        } catch (\Exception $exception) {
            return redirect()->route('agent.campaign.list')->with('error', ['title' => 'Error while processing request', 'message' => 'Campaign details not found']);
        }
    }

    public function getCampaigns(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = CampaignAssignAgent::query();

        $query->whereUserId(Auth::id());
        $query->with('campaign');
        $query->with('agent_work_type');
        $query->with('campaign.children');

        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->whereHas('campaign', function ($campaign) use($searchValue) {
                $campaign->where("campaign_id", "like", "%$searchValue%");
                $campaign->orWhere("name", "like", "%$searchValue%");
                $campaign->orWhere("allocation", "like", "%$searchValue%");
                $campaign->orWhere("deliver_count", "like", "%$searchValue%");
            });
        }
        //Filters
        if(!empty($filters)) {

        }


        //Order By
        $orderColumn = null;
        if ($request->has('order')){
            $order = $request->get('order');
            $orderColumn = $order[0]['column'];
            $orderDirection = $order[0]['dir'];
        }
        switch ($orderColumn) {
            default: $query->orderBy('created_at', 'DESC'); break;
        }

        $totalFilterRecords = $query->count();
        if($limit > 0) {
            $query->offset($offset);
            $query->limit($limit);
        }

        $result = $query->get();
        //dd($result->toArray());

        $ajaxData = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalFilterRecords,
            "aaData" => $result
        );

        return response()->json($ajaxData);
    }

    public function startCampaign($id, Request $request)
    {
        $attributes['started_at'] = date('Y-m-d H:i:s');

        $response = $this->agentRepository->update(base64_decode($id), $attributes);

        if($response['status'] == TRUE) {
            //Add Campaign History
            $resultCampaign = Campaign::findOrFail($response['details']->campaign_id);
            add_campaign_history($resultCampaign->id, $resultCampaign->parent_id, 'Started working on campaign');
            add_history('Campaign Started', 'Started working on campaign');

            return response()->json(array('status' => true, 'message' => 'Campaign Started'));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function submitCampaign($id, Request $request)
    {
        $attributes = $request->all();
        $attributes['submitted_at'] = date('Y-m-d H:i:s');

        $response = $this->agentRepository->update(base64_decode($id), $attributes);

        if($response['status']) {
            //Add Campaign History
            $resultCampaign = Campaign::findOrFail($response['details']->campaign_id);
            add_campaign_history($resultCampaign->id, $resultCampaign->parent_id, 'Submitted the campaign');
            add_history('Campaign Submitted By Agent', 'Submitted the campaign');
            return response()->json(array('status' => true, 'message' => 'Campaign submitted successfully'));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function restartCampaign($id, Request $request)
    {
        $attributes['submitted_at'] = NULL;

        $response = $this->agentRepository->update(base64_decode($id), $attributes);

        if($response['status']) {
            return response()->json(array('status' => true, 'message' => 'Campaign started successfully'));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

}
