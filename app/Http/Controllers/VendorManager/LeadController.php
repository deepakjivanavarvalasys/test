<?php

namespace App\Http\Controllers\VendorManager;

use App\Http\Controllers\Controller;
use App\Models\CampaignAssignVendor;
use App\Models\VendorLead;
use App\Repository\CampaignAssignRepository\VendorManagerRepository\VendorManagerRepository as CAVendorManagerRepository;
use App\Repository\CampaignAssignRepository\VendorRepository\VendorRepository as CAVendorRepository;
use App\Repository\VendorLead\VendorLeadRepository;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    private $data;
    /**
     * @var CAVendorRepository
     */
    private $CAVendorRepository;
    /**
     * @var CAVendorManagerRepository
     */
    private $CAVendorManagerRepository;
    /**
     * @var VendorLeadRepository
     */
    private $vendorLeadRepository;

    public function __construct(
        CAVendorManagerRepository $CAVendorManagerRepository,
        CAVendorRepository $CAVendorRepository,
        VendorLeadRepository $vendorLeadRepository
    )
    {
        $this->data = array();
        $this->CAVendorRepository = $CAVendorRepository;
        $this->CAVendorManagerRepository = $CAVendorManagerRepository;
        $this->vendorLeadRepository = $vendorLeadRepository;
    }

    public function index($cavm_id)
    {
        $this->data['resultCAVM'] = $this->CAVendorManagerRepository->find(base64_decode($cavm_id));
        //dd($this->data['resultCAVM']->vendors->toArray());
        return view('vendor_manager.lead.list', $this->data);
    }

    public function importLeads(Request $request)
    {
        $attributes = $request->all();
        $ca_vendor_id = $attributes['vendor_id'];

        $response = $this->vendorLeadRepository->import($ca_vendor_id, $attributes['lead_file']);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message'], 'data' => $response['data']));
        } else {
            if(!empty($response['data'])) {
                return response()->json(array('status' => false, 'message' => $response['message'], 'data' => $response['data']));
            } else{
                return response()->json(array('status' => false, 'message' => $response['message']));
            }
        }
    }

    public function getVendorLeads(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = VendorLead::query();
        $query->where('status', 1);

        if($request->has('ca_vm_id')) {
            $resultCAVendors = CampaignAssignVendor::where('campaign_assign_vm_id', base64_decode($request->get('ca_vm_id')))->get();
            $query->whereIn('ca_vendor_id', $resultCAVendors->pluck('id')->toArray());
        }

        $query->with('vendor');

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
            case '0': $query->orderBy('created_at', $orderDirection); break;
            case '1': $query->orderBy('first_name', $orderDirection); break;
            case '2': $query->orderBy('last_name', $orderDirection); break;
            case '3': $query->orderBy('company_name', $orderDirection); break;
            case '4': $query->orderBy('email_address', $orderDirection); break;
            case '5': $query->orderBy('specific_title', $orderDirection); break;
            case '6': $query->orderBy('job_level', $orderDirection); break;
            case '7': $query->orderBy('job_role', $orderDirection); break;
            case '8': $query->orderBy('phone_number', $orderDirection); break;
            case '9': $query->orderBy('address_1', $orderDirection); break;
            case '10': $query->orderBy('address_2', $orderDirection); break;
            case '11': $query->orderBy('city', $orderDirection); break;
            case '12': $query->orderBy('state', $orderDirection); break;
            case '13': $query->orderBy('zipcode', $orderDirection); break;
            case '14': $query->orderBy('country', $orderDirection); break;
            case '15': $query->orderBy('industry', $orderDirection); break;
            case '16': $query->orderBy('employee_size', $orderDirection); break;
            case '17': $query->orderBy('employee_size_2', $orderDirection); break;
            case '18': $query->orderBy('revenue', $orderDirection); break;
            case '19': $query->orderBy('company_domain', $orderDirection); break;
            case '20': $query->orderBy('website', $orderDirection); break;
            case '21': $query->orderBy('company_linkedin_url', $orderDirection); break;
            case '22': $query->orderBy('linkedin_profile_link', $orderDirection); break;
            case '23': $query->orderBy('linkedin_profile_sn_link', $orderDirection); break;
            case '24': $query->orderBy('comment', $orderDirection); break;
            case '25': $query->orderBy('comment_2', $orderDirection); break;
            case '26': $query->orderBy('qc_comment', $orderDirection); break;
            case '27': $query->orderBy('status', $orderDirection); break;
            case '28': $query->orderBy('created_at', $orderDirection); break;
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
