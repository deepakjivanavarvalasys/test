<?php

namespace App\Http\Controllers\VendorManager\RA;

use App\Http\Controllers\Controller;
use App\Models\AgentData;
use App\Models\Data;
use App\Repository\AgentDataRepository\AgentDataRepository;
use App\Repository\AgentLeadRepository\AgentLeadRepository;
use App\Repository\CampaignAssignRepository\AgentRepository\AgentRepository;
use App\Repository\DataRepository\DataRepository;
use Illuminate\Http\Request;

class DataController extends Controller
{
    private $data;
    /**
     * @var AgentRepository
     */
    private $agentRepository;
    /**
     * @var DataRepository
     */
    private $dataRepository;
    /**
     * @var AgentLeadRepository
     */
    private $agentLeadRepository;
    /**
     * @var AgentDataRepository
     */
    private $agentDataRepository;

    public function __construct(
        AgentRepository $agentRepository,
        DataRepository $dataRepository,
        AgentLeadRepository $agentLeadRepository,
        AgentDataRepository $agentDataRepository
    )
    {
        $this->data = array();
        $this->agentRepository = $agentRepository;
        $this->dataRepository = $dataRepository;
        $this->agentLeadRepository = $agentLeadRepository;
        $this->agentDataRepository = $agentDataRepository;
    }

    public function index($id)
    {
        $this->data['resultCAAgent'] = $this->agentRepository->find(base64_decode($id));
        return view('vendor_manager.ra.data.list', $this->data);
    }

    public function edit($id)
    {
        $result = $this->dataRepository->find(base64_decode($id));
        if(!empty($result)) {
            return response()->json(array('status' => true, 'message' => 'Data found', 'data' => $result));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function update($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->dataRepository->update(base64_decode($id), $attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function takeLead(Request $request)
    {
        $attributes = $request->all();
        $resultData = $this->dataRepository->find(base64_decode($attributes['data_id']));
        $attributes = array_merge($attributes, $resultData->toArray());

        $resultAgentData = AgentData::select('id')
            ->where('ca_agent_id', base64_decode($attributes['ca_agent_id']))
            ->where('data_id', base64_decode($attributes['data_id']))
            ->where('status', 1)
            ->first();
        if(isset($resultAgentData) && $resultAgentData->id) {
            $response = $this->agentLeadRepository->store($attributes);
            if($response['status'] == TRUE) {
                //Change agentData status to taken
                $response_agentData = $this->agentDataRepository->update($resultAgentData->id, array('status' => 2));
                if($response_agentData['status'] == TRUE) {
                    return response()->json(array('status' => true, 'message' => $response['message']));
                } else {
                    return response()->json(array('status' => false, 'message' => $response['message']));
                }
            } else {
                return response()->json(array('status' => false, 'message' => $response['message']));
            }
        } else {
            return response()->json(array('status' => false, 'message' => 'Lead already used, please try another lead.'));
        }

    }

    public function getAgentData(Request $request)
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        //get assigned data_ids
        $resultDataIds = array();
        $resultAgentData = AgentData::select('data_id')
            ->where('ca_agent_id', base64_decode($request->get('ca_agent_id')))
            ->where('status', 1)
            ->get();
        if($resultAgentData->count()) {
            $resultDataIds = $resultAgentData->pluck('data_id')->toArray();
        }

        $query = Data::query();
        $query->whereIn('id', $resultDataIds);
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
            case '0': $query->orderBy('first_name', $orderDirection); break;
            case '1': $query->orderBy('last_name', $orderDirection); break;
            case '2': $query->orderBy('company_name', $orderDirection); break;
            case '3': $query->orderBy('email_address', $orderDirection); break;
            case '4': $query->orderBy('specific_title', $orderDirection); break;
            case '5': $query->orderBy('job_level', $orderDirection); break;
            case '6': $query->orderBy('job_role', $orderDirection); break;
            case '7': $query->orderBy('phone_number', $orderDirection); break;
            case '8': $query->orderBy('address_1', $orderDirection); break;
            case '9': $query->orderBy('address_2', $orderDirection); break;
            case '10': $query->orderBy('city', $orderDirection); break;
            case '11': $query->orderBy('state', $orderDirection); break;
            case '12': $query->orderBy('zipcode', $orderDirection); break;
            case '13': $query->orderBy('country', $orderDirection); break;
            case '14': $query->orderBy('employee_size', $orderDirection); break;
            case '15': $query->orderBy('revenue', $orderDirection); break;
            case '16': $query->orderBy('company_domain', $orderDirection); break;
            case '17': $query->orderBy('website', $orderDirection); break;
            case '18': $query->orderBy('company_linkedin_url', $orderDirection); break;
            case '19': $query->orderBy('linkedin_profile_link', $orderDirection); break;
            case '20': $query->orderBy('linkedin_profile_sn_link', $orderDirection); break;
            default: $query->orderBy('first_name'); break;
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
