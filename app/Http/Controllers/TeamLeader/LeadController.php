<?php

namespace App\Http\Controllers\TeamLeader;

use App\Exports\RATLLeadExport;
use App\Http\Controllers\Controller;
use App\Models\AgentLead;
use App\Models\CampaignAssignAgent;
use App\Models\SuppressionAccountName;
use App\Models\SuppressionDomain;
use App\Models\SuppressionEmail;
use App\Models\TargetDomain;
use App\Repository\AgentLeadRepository\AgentLeadRepository;
use App\Repository\CampaignAssignRepository\AgentRepository\AgentRepository;
use App\Repository\CampaignAssignRepository\RATLRepository\RATLRepository;
use Illuminate\Http\Request;
use Excel;

class LeadController extends Controller
{
    private $data;
    private $agentRepository;
    /**
     * @var RATLRepository
     */
    private $RATLRepository;
    /**
     * @var AgentLeadRepository
     */
    private $agentLeadRepository;

    public function __construct(
        AgentRepository $agentRepository,
        RATLRepository $RATLRepository,
        AgentLeadRepository $agentLeadRepository
    )
    {
        $this->agentRepository = $agentRepository;
        $this->RATLRepository = $RATLRepository;
        $this->agentLeadRepository = $agentLeadRepository;
    }

    public function index($ca_ratl_id)
    {
        $this->data['resultCARATL'] = $this->RATLRepository->find(base64_decode($ca_ratl_id));
        return view('team_leader.lead.list', $this->data);
    }

    public function edit($agent_lead_id)
    {
        $this->data['resultAgentLead'] = $this->agentLeadRepository->find(base64_decode($agent_lead_id));
        $this->data['resultCAAgent'] = $this->agentRepository->find($this->data['resultAgentLead']->ca_agent_id);
        return view('team_leader.lead.edit', $this->data);
    }

    public function update($agent_lead_id, Request $request): \Illuminate\Http\RedirectResponse
    {
        $attributes = $request->all();
        $response = $this->agentLeadRepository->update(base64_decode($agent_lead_id), $attributes);
        if($response['status'] == TRUE) {
            $this->data['resultCAAgent'] = $this->agentRepository->find($response['details']->ca_agent_id);
            return redirect()->route('team_leader.lead.list', base64_encode($this->data['resultCAAgent']->campaign_assign_ratl_id))->with('success', ['title' => 'Successful', 'message' => $response['message']]);
        } else {
            return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => $response['message']]);
        }
    }

    public function reject($agent_lead_id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $attributes['status'] = 0;
        $response = $this->agentLeadRepository->update(base64_decode($agent_lead_id), $attributes);

        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function export($caratl_id, Request $request)
    {
        $response = array('status' => true, 'message' => 'Something went wrong, please try again.');
        $filters = array();
        try {
            $resultCARATL = $this->RATLRepository->find(base64_decode($caratl_id));

            $path = 'public/campaigns/'.$resultCARATL->campaign->campaign_id.'/team_leader/';
            $path_to_download = '/public/storage/campaigns/'.$resultCARATL->campaign->campaign_id.'/team_leader/';
            $filename = str_replace(' ', '_', trim($resultCARATL->campaign->name)) .'_'.time(). "_AGENT_LEADS.xlsx";

            if($request->has('export_filter')) {
                switch ($request->export_filter) {
                    case 'sent':
                        $filters = array('send_date' => 'NOT_NULL');
                        break;
                    case 'un_send':
                        $filters = array('send_date' => 'NULL');
                        break;
                    case 'all':
                    default: $filters = array(); break;
                }
            }

            if(Excel::store(new RATLLeadExport($resultCARATL->id, $filters), $path.$filename)) {
                $response = array('status' => true, 'message' => 'Successful', 'file_name' => $path_to_download.$filename);
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            $response = array('status' => false, 'message' => 'Something went wrong, please try again.');
        }

        return response()->json($response);
    }

    public function getAgentLeads(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = AgentLead::query();
        //$query->where('status', 1);

        if($request->has('ca_ratl_id')) {
            $resultCAAgents = CampaignAssignAgent::where('campaign_assign_ratl_id', base64_decode($request->get('ca_ratl_id')))->get();
            $query->whereIn('ca_agent_id', $resultCAAgents->pluck('id')->toArray());
            //if($resultCAAgents->count())
        }

        $query->with('agent');

        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->where(function ($query) use($searchValue) {
                $query->where("first_name", "like", "%$searchValue%");
                $query->orWhere("last_name", "like", "%$searchValue%");
                $query->orWhere("company_name", "like", "%$searchValue%");
                $query->orWhere("email_address", "like", "%$searchValue%");
                $query->orWhere("specific_title", "like", "%$searchValue%");
                $query->orWhere("job_level", "like", "%$searchValue%");
                $query->orWhere("job_role", "like", "%$searchValue%");
                $query->orWhere("phone_number", "like", "%$searchValue%");
                $query->orWhere("address_1", "like", "%$searchValue%");
                $query->orWhere("address_2", "like", "%$searchValue%");
                $query->orWhere("city", "like", "%$searchValue%");
                $query->orWhere("state", "like", "%$searchValue%");
                $query->orWhere("zipcode", "like", "%$searchValue%");
                $query->orWhere("employee_size", "like", "%$searchValue%");
                $query->orWhere("employee_size_2", "like", "%$searchValue%");
                $query->orWhere("revenue", "like", "%$searchValue%");
                $query->orWhere("country", "like", "%$searchValue%");
                $query->orWhere("company_domain", "like", "%$searchValue%");
                $query->orWhere("website", "like", "%$searchValue%");
                $query->orWhere("company_linkedin_url", "like", "%$searchValue%");
                $query->orWhere("linkedin_profile_link", "like", "%$searchValue%");
                $query->orWhere("linkedin_profile_sn_link", "like", "%$searchValue%");
                $query->orWhere("comment", "like", "%$searchValue%");
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
            case '0': break;
            case '1': $query->orderBy('agent_id', $orderDirection); break;
            case '2': $query->orderBy('created_at', $orderDirection); break;
            case '3': $query->orderBy('first_name', $orderDirection); break;
            case '4': $query->orderBy('last_name', $orderDirection); break;
            case '5': $query->orderBy('company_name', $orderDirection); break;
            case '6': $query->orderBy('email_address', $orderDirection); break;
            case '7': $query->orderBy('specific_title', $orderDirection); break;
            case '8': $query->orderBy('job_level', $orderDirection); break;
            case '9': $query->orderBy('job_role', $orderDirection); break;
            case '10': $query->orderBy('phone_number', $orderDirection); break;
            case '11': $query->orderBy('address_1', $orderDirection); break;
            case '12': $query->orderBy('address_2', $orderDirection); break;
            case '13': $query->orderBy('city', $orderDirection); break;
            case '14': $query->orderBy('state', $orderDirection); break;
            case '15': $query->orderBy('zipcode', $orderDirection); break;
            case '16': $query->orderBy('country', $orderDirection); break;
            case '17': $query->orderBy('industry', $orderDirection); break;
            case '18': $query->orderBy('employee_size', $orderDirection); break;
            case '19': $query->orderBy('employee_size_2', $orderDirection); break;
            case '20': $query->orderBy('revenue', $orderDirection); break;
            case '21': $query->orderBy('company_domain', $orderDirection); break;
            case '22': $query->orderBy('website', $orderDirection); break;
            case '23': $query->orderBy('company_linkedin_url', $orderDirection); break;
            case '24': $query->orderBy('linkedin_profile_link', $orderDirection); break;
            case '25': $query->orderBy('linkedin_profile_sn_link', $orderDirection); break;
            case '26': $query->orderBy('comment', $orderDirection); break;
            case '27': $query->orderBy('comment_2', $orderDirection); break;
            case '28': $query->orderBy('qc_comment', $orderDirection); break;
            case '29': $query->orderBy('status', $orderDirection); break;
            case '30': $query->orderBy('send_date', $orderDirection); break;
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

    public function checkSuppressionEmail($id, Request $request)
    {
        $resultCAAgent = $this->agentRepository->find(base64_decode($id));
        $query = SuppressionEmail::query();
        $query->whereCampaignId($resultCAAgent->campaign_id);
        $query->whereEmail(trim($request->email_address));
        if($query->exists()) {
            return 'false';
        } else {
            return 'true';
        }
    }

    public function checkSuppressionDomain($id, Request $request)
    {
        $resultCAAgent = $this->agentRepository->find(base64_decode($id));
        $query = SuppressionDomain::query();
        $query->whereCampaignId($resultCAAgent->campaign_id);
        if($query->count()) {
            $query->whereDomain(trim($request->company_domain));
            if($query->exists()) {
                return 'false';
            } else {
                return 'true';
            }
        } else {
            return 'true';
        }
    }

    public function checkSuppressionAccountName($id, Request $request)
    {
        $resultCAAgent = $this->agentRepository->find(base64_decode($id));
        $query = SuppressionAccountName::query();
        $query->whereCampaignId($resultCAAgent->campaign_id);
        $query->where('account_name', trim($request->company_name));
        if($query->exists()) {
            return 'false';
        } else {
            return 'true';
        }
    }

    public function checkTargetDomain($id, Request $request)
    {
        $resultCAAgent = $this->agentRepository->find(base64_decode($id));
        $query = TargetDomain::query();
        $query->whereCampaignId($resultCAAgent->campaign_id);
        if($query->count()) {
            $query->whereDomain(trim($request->company_domain));
            if($query->exists()) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'true';
        }



    }


}
