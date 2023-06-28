<?php

namespace App\Http\Controllers\TeamLeader;

use App\Http\Controllers\Controller;
use App\Models\AgentLead;
use App\Models\Campaign;
use App\Models\CampaignAssignAgent;
use App\Models\CampaignAssignQATL;
use App\Models\CampaignAssignRATL;
use App\Models\Data;
use App\Models\Role;
use App\Models\User;
use App\Repository\AgentDataRepository\AgentDataRepository;
use App\Repository\AgentWorkType\AgentWorkTypeRepository;
use App\Repository\Campaign\IssueRepository\IssueRepository;
use App\Repository\CampaignAssignRepository\AgentRepository\AgentRepository;
use App\Repository\CampaignAssignRepository\CampaignAssignRepository;
use App\Repository\CampaignAssignRepository\QATLRepository\QATLRepository;
use App\Repository\CampaignAssignRepository\RATLRepository\RATLRepository;
use App\Repository\CampaignRepository\CampaignRepository;
use App\Repository\DataRepository\DataRepository;
use App\Repository\Notification\QATL\QATLNotificationRepository;
use App\Repository\UserRepository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repository\Suppression\Email\EmailRepository as SuppressionEmailRepository;
use App\Repository\Suppression\Domain\DomainRepository as SuppressionDomainRepository;
use App\Repository\Suppression\AccountName\AccountNameRepository as SuppressionAccountNameRepository;
use App\Repository\Target\Domain\DomainRepository as TargetDomainRepository;


class CampaignAssignController extends Controller
{
    private $data;
    private $campaignRepository;
    private $campaignAssignRepository;
    private $RATLRepository;
    private $agentRepository;
    private $userRepository;
    /**
     * @var DataRepository
     */
    private $dataRepository;
    /**
     * @var SuppressionAccountNameRepository
     */
    private $accountNameRepository;
    /**
     * @var SuppressionDomainRepository
     */
    private $suppressionDomainRepository;
    /**
     * @var SuppressionAccountNameRepository
     */
    private $suppressionAccountNameRepository;
    /**
     * @var SuppressionEmailRepository
     */
    private $suppressionEmailRepository;
    /**
     * @var TargetDomainRepository
     */
    private $targetDomainRepository;
    /**
     * @var AgentDataRepository
     */
    private $agentDataRepository;
    /**
     * @var IssueRepository
     */
    private $issueRepository;
    /**
     * @var QATLRepository
     */
    private $QATLRepository;

    public function __construct(
        CampaignRepository $campaignRepository,
        CampaignAssignRepository $campaignAssignRepository,
        RATLRepository $RATLRepository,
        AgentRepository $agentRepository,
        UserRepository $userRepository,
        DataRepository $dataRepository,
        SuppressionEmailRepository $suppressionEmailRepository,
        SuppressionDomainRepository $suppressionDomainRepository,
        SuppressionAccountNameRepository $suppressionAccountNameRepository,
        TargetDomainRepository $targetDomainRepository,
        AgentDataRepository $agentDataRepository,
        IssueRepository $issueRepository,
        QATLRepository $QATLRepository
    )
    {
        $this->data = array();
        $this->campaignRepository = $campaignRepository;
        $this->campaignAssignRepository = $campaignAssignRepository;
        $this->RATLRepository = $RATLRepository;
        $this->agentRepository = $agentRepository;
        $this->userRepository = $userRepository;
        $this->dataRepository = $dataRepository;
        $this->suppressionEmailRepository = $suppressionEmailRepository;
        $this->suppressionDomainRepository = $suppressionDomainRepository;
        $this->suppressionAccountNameRepository = $suppressionAccountNameRepository;
        $this->targetDomainRepository = $targetDomainRepository;
        $this->agentDataRepository = $agentDataRepository;
        $this->issueRepository = $issueRepository;
        $this->QATLRepository = $QATLRepository;
    }

    public function index()
    {
        $this->data['resultCampaigns'] = $this->campaignAssignRepository->getCampaignToAssignForTL(Auth::id());

        $resultAgents = $this->userRepository->get(array(
            'status' => 1,
            'designation_slug' => array('research_analyst'),
            'order_by' => array('value' => 'first_name', 'order' => 'ASC'),
            'reporting_to' => array(Auth::id())
        ));

        $resultEMEs = $this->userRepository->get(array(
            'status' => 1,
            'designation_slug' => array('email_marketing_executive'),
            'order_by' => array('value' => 'first_name', 'order' => 'ASC')
        ));

        $resultVMs = $this->userRepository->get(array(
            'status' => 1,
            'designation_slug' => array('sr_vendor_management_specialist'),
            'order_by' => array('value' => 'first_name', 'order' => 'ASC')
        ));

        $this->data['resultUsers'] = $allItems = new \Illuminate\Database\Eloquent\Collection;;
        $this->data['resultUsers'] = $this->data['resultUsers']->merge($resultAgents);
        $this->data['resultUsers'] = $this->data['resultUsers']->merge($resultEMEs);
        $this->data['resultUsers'] = $this->data['resultUsers']->merge($resultVMs);
        //$this->data['resultUsers'] = $resultAgents->merge($resultEMEs);

        $this->data['resultAgentWorkTypes'] = AgentWorkTypeRepository::get(array('status' => 1));

        //dd($this->data['resultAgentWorkTypes']->toArray());
        return view('team_leader.campaign_assign.list', $this->data);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        $user_names = '';
        try {
            $attributes = $request->all();
            if(!$request->has('display_date')) {
                $resultCampaignAssignRATL = CampaignAssignRATL::findOrFail($attributes['campaign_assign_ratl_id']);
                $attributes['display_date'] = date('Y-m-d', strtotime($resultCampaignAssignRATL->display_date));
            }
            $attributes['assigned_by'] = Auth::id();
            $resultCampaign = Campaign::findOrFail($attributes['campaign_id']);
            foreach ($attributes['users'] as $user) {
                $resultUser = User::findOrFail($user['user_id']);
                $user_names .= $resultUser->full_name.', ';
                $attributes['user_id'] = $user['user_id'];
                $attributes['allocation'] = $user['allocation'];

                $result = $this->agentRepository->store($attributes);

                if($result['status'] == TRUE) {
                    $response = array('status' => TRUE, 'message' => 'Campaign assigned successfully');
                } else {
                    throw new \Exception($result['message'], 1);
                }
            }
        } catch (\Exception $exception) {
            $response = array('status' => FALSE, 'message' => $exception->getMessage());
        }

        if($response['status'] == TRUE) {
            $res = $this->RATLRepository->update($attributes['campaign_assign_ratl_id'], array('started_at' => date('Y-m-d H:i:s')));

            //Add Campaign History
            add_campaign_history($resultCampaign->id, $resultCampaign->parent_id, 'Campaign assigned to agent(s) - '.$user_names);
            add_history('Campaign assigned to agent(s)', 'Campaign assigned to agent(s) - '.$user_names);

            return redirect()->route('team_leader.campaign_assign.list')->with('success', ['title' => 'Successful', 'message' => $response['message']]);
        } else {
            return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => $response['message']]);
        }
    }

    public function show($id)
    {
        try {
            $this->data['resultCARATL'] = $this->RATLRepository->find(base64_decode($id));

            //Data for filter
            $this->data['resultFilterJobLevels'] = Data::distinct()->get(['job_level']);
            $this->data['resultFilterJobRoles'] = Data::distinct()->get(['job_role']);
            $this->data['resultFilterEmployeeSizes'] = Data::distinct()->get(['employee_size']);
            $this->data['resultFilterRevenues'] = Data::distinct()->get(['revenue']);
            $this->data['resultFilterCountries'] = Data::distinct()->get(['country']);
            $this->data['resultFilterStates'] = Data::distinct()->get(['state']);

            $this->data['resultCampaign'] = $this->campaignRepository->find($this->data['resultCARATL']->campaign->id);
            $this->data['resultCampaignIssues'] = $this->issueRepository->get(array('campaign_ids' => [$this->data['resultCARATL']->campaign->id]));
            $this->data['resultCAQATL'] = CampaignAssignQATL::where('campaign_id', $this->data['resultCARATL']->campaign_id)->first();
            //get count if data already assigned
            $this->data['countAgentData'] = $this->agentDataRepository->get(array('ca_ratl_ids' => [$this->data['resultCARATL']->id]))->count();

            $resultAgents = $this->userRepository->get(array(
                'status' => 1,
                'designation_slug' => array('research_analyst'),
                'order_by' => array('value' => 'first_name', 'order' => 'ASC'),
                'reporting_to' => array(Auth::id())
            ));

            $resultEMEs = $this->userRepository->get(array(
                'status' => 1,
                'designation_slug' => array('email_marketing_executive'),
                'order_by' => array('value' => 'first_name', 'order' => 'ASC')
            ));

            $resultVMs = $this->userRepository->get(array(
                'status' => 1,
                'designation_slug' => array('sr_vendor_management_specialist'),
                'order_by' => array('value' => 'first_name', 'order' => 'ASC')
            ));

            $this->data['resultUsers'] = $allItems = new \Illuminate\Database\Eloquent\Collection;;
            $this->data['resultUsers'] = $this->data['resultUsers']->merge($resultAgents);
            $this->data['resultUsers'] = $this->data['resultUsers']->merge($resultEMEs);
            $this->data['resultUsers'] = $this->data['resultUsers']->merge($resultVMs);
            //$this->data['resultUsers'] = $resultAgents->merge($resultEMEs);
            $this->data['resultAssignedUsers'] = $this->data['resultCARATL']->agents->pluck('user_id')->toArray();

            //dd($this->data['resultCARATL']->toArray());
            return view('team_leader.campaign_assign.show', $this->data);

        } catch (\Exception $exception) {
            return redirect()->route('team_leader.campaign_assign.list')->with('error', ['title' => 'Error while processing request', 'message' => 'Campaign details not found']);
        }
    }

    public function getAssignedCampaigns(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = CampaignAssignRATL::query();
        $query->whereUserId(Auth::id());

        $query_agent_campaigns = CampaignAssignAgent::query();
        $query_agent_campaigns->whereIn('campaign_assign_ratl_id', $query->get()->pluck('id')->toArray());
        $resultAgentCampaigns = $query_agent_campaigns->get();

        //$this->data['resultAssignedCampaigns'] = $this->campaignAssignRepository->getCampaignToAgents(Auth::id());

        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query->whereIn('id', $resultAgentCampaigns->pluck('campaign_assign_ratl_id')->toArray());

        $query->with('campaign');


        $query->with('campaign.children');
        $query->with('agents');

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
            case '1': $query->orderBy('name', $orderDirection); break;
            case '2':
                break;
            case '3': $query->orderBy('start_date', $orderDirection); break;
            case '4': $query->orderBy('end_date', $orderDirection); break;
            case '5': $query->orderBy('allocation', $orderDirection); break;
            case '6': $query->orderBy('campaign_status_id', $orderDirection); break;
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

    public function viewAssignmentDetails($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $result = $this->agentRepository->get(array('caratl_id' => base64_decode($id)));
        //dd($result->toArray());
        if(!empty($result)) {
            return response()->json(array('status' => true, 'data' => $result));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function revokeCampaign($id)
    {
        $attributes['submitted_at'] = date('Y-m-d H:i:s');
        $attributes['status'] = 2;
        $response = $this->agentRepository->update(base64_decode($id), $attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => 'Campaign revoked successfully'));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function assignCampaign(Request $request)
    {
        $attributes = $request->all();
        try {
            if($attributes['allocation'] > 0) {
                $resultCARATL = CampaignAssignRATL::where('campaign_id', base64_decode($attributes['campaign_id']))->where('user_id', Auth::id())->first();
                $resultCAAgent = CampaignAssignAgent::where('campaign_assign_ratl_id', $resultCARATL->id)->first();

                $new_attributes['campaign_assign_ratl_id'] = $resultCARATL->id;
                $new_attributes['campaign_id'] = $resultCARATL->campaign_id;
                $new_attributes['display_date'] = $resultCARATL->display_date;

                $new_attributes['agent_work_type_id'] = $resultCAAgent->agent_work_type_id;
                $new_attributes['allocation'] = $attributes['allocation'];
                $new_attributes['assigned_by'] = $resultCARATL->user_id;

                $response['status'] = FALSE;

                foreach ($attributes['user_list'] as $user) {
                    $new_attributes['user_id'] = $user;
                    $response = $this->agentRepository->store($new_attributes);
                }

                if($response['status'] == TRUE) {
                    return response()->json(array('status' => true, 'message' => 'Campaign assigned successfully'));
                } else {
                    return response()->json(array('status' => false, 'message' => $response['message']));
                }

            } else{
                return response()->json(array('status' => false, 'message' => 'Enter valid allocation, please try again.'));
            }

        } catch (\Exception $exception) {
            return response()->json(array('status' => false, 'message' => 'Something went wrong, please try again.'));
        }
    }

    public function reAssignCampaign($id, Request $request)
    {
        $attributes = $request->all();
        $new_attributes['submitted_at'] = NULL;
        $new_attributes['status'] = 1;
        $response = $this->agentRepository->update(base64_decode($id), $new_attributes);

        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => 'Campaign re-assigned successfully'));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function getData(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = $request->all();
        $campaign_id = base64_decode($filters['campaign_id']);

        $resultCARATL = $this->RATLRepository->find(base64_decode($filters['ca_ratl_id']));

        $filters['limit'] = $resultCARATL->allocation * 10;

        //get suppression lists
        $suppressionList = array();
        $suppressionList['suppression_email'] = $this->suppressionEmailRepository->get(array('campaign_id' => [$campaign_id]));
        $suppressionList['suppression_domain'] = $this->suppressionDomainRepository->get(array('campaign_id' => [$campaign_id]));
        $suppressionList['suppression_account_name'] = $this->suppressionAccountNameRepository->get(array('campaign_id' => [$campaign_id]));

        //get target lists
        $targetList = array();
        $targetList['target_domain'] = $this->targetDomainRepository->get(array('campaign_id' => [$campaign_id]));

        $result = $this->dataRepository->get($filters, $suppressionList, $targetList);

        if(!empty($result)) {
            return response()->json(array('status' => true, 'message' => $result->count().' records found.', 'data' => $result->pluck('id')->toArray()));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function assignData(Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->agentDataRepository->assignData($attributes);
        if($response['status'] == TRUE) {
            $countAgentData = $this->agentDataRepository->get(array('ca_ratl_ids' => [base64_decode($attributes['ca_ratl_id'])]))->count();
            return response()->json(array('status' => true, 'message' => $response['message'], 'countAgentData' => $countAgentData));
        } else {
            return response()->json(array('status' => false, 'message' => 'Something went wrong, please try again.'));
        }
    }

    public function sendForQualityCheck($ca_ratl_id): \Illuminate\Http\JsonResponse
    {
        $resultCARATL = $this->RATLRepository->find(base64_decode($ca_ratl_id));

        //Update status of leads to sent
        $resultCAAgents = CampaignAssignAgent::where('campaign_assign_ratl_id', $resultCARATL->id)->get();

        AgentLead::whereIn('ca_agent_id', $resultCAAgents->pluck('id')->toArray())
            ->whereNull('send_date')
            ->update(array('send_date' => date('Y-m-d H:i:s')));

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
                'message' => 'Campaign submitted by RATLs - '.$resultCARATL->campaign->name,
                'url' => route('qa_team_leader.campaign.show', base64_encode($resultCAQATL->id))
            ));
        }

        if($responseCAQATL['status']) {
            return response()->json(array('status' => true, 'message' => 'Campaign sent to quality check successfully.'));
        } else {
            return response()->json(array('status' => false, 'message' => $responseCAQATL['message']));
        }
    }

    //public function sendForQualityCheck(); //This functionality is in campaign controller

}
