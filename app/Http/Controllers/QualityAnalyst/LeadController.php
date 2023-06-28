<?php

namespace App\Http\Controllers\QualityAnalyst;

use App\Exports\CampaignLeadExport;
use App\Http\Controllers\Controller;
use App\Models\AgentLead;
use App\Models\CampaignAssignAgent;
use App\Repository\AgentLeadRepository\AgentLeadRepository;
use App\Repository\CampaignAssignRepository\QualityAnalystRepository\QualityAnalystRepository as CAQARepository;
use Illuminate\Http\Request;
use Excel;

class LeadController extends Controller
{
    private $data;
    /**
     * @var CAQARepository
     */
    private $CAQARepository;
    /**
     * @var AgentLeadRepository
     */
    private $agentLeadRepository;

    public function __construct(
        CAQARepository $CAQARepository,
        AgentLeadRepository $agentLeadRepository
    )
    {
        $this->data = array();
        $this->CAQARepository = $CAQARepository;
        $this->agentLeadRepository = $agentLeadRepository;
    }

    public function index($ca_qa_id)
    {
        $this->data['resultCAQA'] = $this->CAQARepository->find(base64_decode($ca_qa_id));
        return view('quality_analyst.lead.list', $this->data);
    }

    public function reject($agent_lead_id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $attributes['status'] = 0;
        $response = $this->agentLeadRepository->update(base64_decode($agent_lead_id), $attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => 'Lead rejected successfully'));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function approve($agent_lead_id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $attributes['status'] = 1;
        $response = $this->agentLeadRepository->update(base64_decode($agent_lead_id), $attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => 'Lead approved successfully'));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function export($caqa_id, Request $request)
    {
        $response = array('status' => true, 'message' => 'Something went wrong, please try again.');
        $filters = array();
        try {
            $resultCAQA = $this->CAQARepository->find(base64_decode($caqa_id));

            if($request->has('export_filter')) {
                $filters = array('qc_custom_filter' => $request->export_filter);
            }

            $path = 'public/campaigns/'.$resultCAQA->campaign->campaign_id.'/quality_analyst/';
            $path_to_download = '/public/storage/campaigns/'.$resultCAQA->campaign->campaign_id.'/quality_analyst/';
            $filename = str_replace(' ', '_', trim($resultCAQA->campaign->name)) .'_'.time().'_'.strtoupper($request->export_filter).'_LEADS.xlsx';


            if(Excel::store(new CampaignLeadExport($resultCAQA->campaign_id, $filters), $path.$filename)) {
                $response = array('status' => true, 'message' => 'Successful', 'file_name' => $path_to_download.$filename);
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            $response = array('status' => false, 'message' => 'Something went wrong, please try again.');
        }

        return response()->json($response);
    }

    public function getCampaignLeads(Request $request): \Illuminate\Http\JsonResponse
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

        if($request->has('campaign_id')) {
            $resultCAAgents = CampaignAssignAgent::where('campaign_id', base64_decode($request->get('campaign_id')))->get();
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

}
