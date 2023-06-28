<?php

namespace App\Http\Controllers\TeamLeader;

use App\Http\Controllers\Controller;
use App\Models\AgentLead;
use App\Models\Campaign;
use App\Models\CampaignAssignAgent;
use App\Models\CampaignAssignQATL;
use App\Models\CampaignAssignRATL;
use App\Models\Role;
use App\Models\User;
use App\Repository\AgentLeadRepository\AgentLeadRepository;
use App\Repository\CampaignAssignRepository\QATLRepository\QATLRepository;
use App\Repository\CampaignAssignRepository\RATLRepository\RATLRepository;
use App\Repository\CampaignRepository\CampaignRepository;
use App\Repository\Notification\QATL\QATLNotificationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    private $data;
    private $RATLRepository;
    private $campaignRepository;
    private $agentLeadRepository;
    /**
     * @var QATLRepository
     */
    private $QATLRepository;

    public function __construct(
        RATLRepository $RATLRepository,
        QATLRepository $QATLRepository,
        CampaignRepository $campaignRepository,
        AgentLeadRepository $agentLeadRepository
    )
    {
        $this->data = array();
        $this->RATLRepository = $RATLRepository;
        $this->campaignRepository = $campaignRepository;
        $this->agentLeadRepository = $agentLeadRepository;
        $this->QATLRepository = $QATLRepository;
    }

    public function index()
    {
        return view('team_leader.campaign.list');
    }

    public function show($id)
    {
        try {
            $this->data['resultCARATL'] = $this->RATLRepository->find(base64_decode($id));
            $this->data['resultCampaign'] = $this->campaignRepository->find($this->data['resultCARATL']->campaign->id);
            return view('team_leader.campaign.show', $this->data);
        } catch (\Exception $exception) {
            return redirect()->back();
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

        $query = CampaignAssignRATL::query();

        $query->whereUserId(Auth::id());
        $query->with('campaign');
        $query->with('campaign.children');

        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->whereHas('campaign', function ($campaign) use($searchValue) {
                $campaign->where("campaign_id", "like", "%$searchValue%");
                $campaign->orWhere("name", "like", "%$searchValue%");
                //$campaign->orWhere("deliver_count", "like", "%$searchValue%");
            });
            $query->orWhere("allocation", "like", "%$searchValue%");
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
            case '0': $query->orderBy('campaign_id', $orderDirection); break;
            case '1': $query->orderBy('campaign_id', $orderDirection);break;
            case '2': break;
            case '3': break;
            case '4': $query->orderBy('display_date', $orderDirection); break;
            case '5': $query->orderBy('allocation', $orderDirection); break;
            case '6': break;
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

    public function getAgentLeadDetails($ca_agent_id, Request $request): \Illuminate\Http\JsonResponse
    {
        $result = $this->agentLeadRepository->get(array('ca_agent_id' => base64_decode($ca_agent_id)));
        //dd($result->toArray());
        if(!empty($result)) {
            return response()->json(array('status' => true, 'data' => $result));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function submitCampaign($id, Request $request)
    {
        $attributes['submitted_at'] = date('Y-m-d H:i:s');
        $response = $this->RATLRepository->update(base64_decode($id), $attributes);
        if($response['status'] == TRUE) {

            $resultCAAgents = CampaignAssignAgent::where('campaign_assign_ratl_id', base64_decode($id))->get();
            AgentLead::whereIn('ca_agent_id', $resultCAAgents->pluck('id')->toArray())
                ->whereNull('send_date')
                ->update(array('send_date' => date('Y-m-d H:i:s')));

            //Add Campaign History
            $resultCampaign = Campaign::findOrFail($response['details']->campaign_id);
            add_campaign_history($resultCampaign->id, $resultCampaign->parent_id, 'Submitted the campaign');
            add_history('Campaign Submitted By RATL', 'Submitted the campaign');

            //check submitted by all ratl's
            if(!CampaignAssignRATL::where('campaign_id', $response['details']->campaign_id)->whereNull('submitted_at')->count()) {

                $resultCARATL = $this->RATLRepository->find(base64_decode($id));
                $resultRole = Role::whereSlug('qa_team_leader')->whereStatus(1)->first();
                $resultUser = User::whereRoleId($resultRole->id)->whereStatus(1)->first();
                $resultCAQATL = CampaignAssignQATL::where('campaign_id', $resultCARATL->campaign_id)->where('user_id', $resultUser->id)->first();

                if(empty($resultCAQATL->id)) {
                    $attributes = array(
                        'campaign_id' => $resultCARATL->campaign_id,
                        'user_id' => $resultUser->id,
                        'display_date' => $resultCARATL->display_date,
                        'assigned_by' => $resultCARATL->user_id,
                    );
                    $responseCAQATL = $this->QATLRepository->store($attributes);
                } else {
                    $responseCAQATL['status'] = TRUE;
                    $responseCAQATL['message'] = 'Campaign submitted successfully';

                    //Send notification to qatl
                    QATLNotificationRepository::store(array(
                        'sender_id' => $resultCARATL->user_id,
                        'recipient_id' => $resultUser->id,
                        'message' => 'Campaign submitted by RATLs - '.$resultCampaign->name,
                        'url' => route('qa_team_leader.campaign.show', base64_encode($resultCAQATL->campaign_id))
                    ));
                }


                if($responseCAQATL['status']) {
                    return response()->json(array('status' => true, 'message' => $responseCAQATL['message']));
                } else {
                    return response()->json(array('status' => false, 'message' => $responseCAQATL['message']));
                }
            } else {
                return response()->json(array('status' => true, 'message' => 'Campaign submitted successfully'));
            }
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

}
