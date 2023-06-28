<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignAssignAgent;
use App\Models\CampaignAssignRATL;
use App\Models\CampaignAssignVendorManager;
use App\Models\User;
use App\Repository\AgentWorkType\AgentWorkTypeRepository;
use App\Repository\Campaign\DeliveryDetailRepository\DeliveryDetailRepository;
use App\Repository\Campaign\IssueRepository\IssueRepository;
use App\Repository\CampaignAssignRepository\AgentRepository\AgentRepository;
use App\Repository\CampaignAssignRepository\CampaignAssignRepository;
use App\Repository\CampaignAssignRepository\RATLRepository\RATLRepository as CARATLRepository;
use App\Repository\CampaignAssignRepository\VendorRepository\VendorRepository as CAVendorRepository ;
use App\Repository\CampaignFilterRepository\CampaignFilterRepository;
use App\Repository\CampaignRepository\CampaignRepository;
use App\Repository\CampaignStatusRepository\CampaignStatusRepository;
use App\Repository\CampaignTypeRepository\CampaignTypeRepository;
use App\Repository\CountryRepository\CountryRepository;
use App\Repository\RegionRepository\RegionRepository;
use App\Repository\UserRepository\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignAssignController extends Controller
{
    private $data;
    private $campaignRepository;
    private $userRepository;
    private $campaignAssignRepository;
    private $agentRepository;
    /**
     * @var CampaignStatusRepository
     */
    private $campaignStatusRepository;
    /**
     * @var CampaignFilterRepository
     */
    private $campaignFilterRepository;
    /**
     * @var CampaignTypeRepository
     */
    private $campaignTypeRepository;
    /**
     * @var CountryRepository
     */
    private $countryRepository;
    /**
     * @var RegionRepository
     */
    private $regionRepository;
    /**
     * @var DeliveryDetailRepository
     */
    private $deliveryDetailRepository;
    /**
     * @var IssueRepository
     */
    private $issueRepository;
    /**
     * @var CARATLRepository
     */
    private $CARATLRepository;
    /**
     * @var CAVendorRepository
     */
    private $CAVendorRepository;

    public function __construct(
        CampaignStatusRepository $campaignStatusRepository,
        CampaignFilterRepository $campaignFilterRepository,
        CampaignTypeRepository $campaignTypeRepository,
        CountryRepository $countryRepository,
        RegionRepository $regionRepository,
        CampaignRepository $campaignRepository,
        UserRepository $userRepository,
        CampaignAssignRepository $campaignAssignRepository,
        AgentRepository $agentRepository,
        DeliveryDetailRepository $deliveryDetailRepository,
        IssueRepository $issueRepository,
        CARATLRepository $CARATLRepository,
        CAVendorRepository $CAVendorRepository
    )
    {
        $this->data = array();
        $this->campaignRepository = $campaignRepository;
        $this->userRepository = $userRepository;
        $this->campaignAssignRepository = $campaignAssignRepository;
        $this->agentRepository = $agentRepository;
        $this->campaignStatusRepository = $campaignStatusRepository;
        $this->campaignFilterRepository = $campaignFilterRepository;
        $this->campaignTypeRepository = $campaignTypeRepository;
        $this->countryRepository = $countryRepository;
        $this->regionRepository = $regionRepository;
        $this->deliveryDetailRepository = $deliveryDetailRepository;
        $this->issueRepository = $issueRepository;
        $this->CARATLRepository = $CARATLRepository;
        $this->CAVendorRepository = $CAVendorRepository;
    }

    public function index()
    {
        $this->data['dataFilter']['resultCountries'] = $this->countryRepository->get(array('status' => 1));
        $this->data['dataFilter']['resultRegions'] = $this->regionRepository->get(array('status' => 1));
        $this->data['dataFilter']['resultCampaignTypes'] = $this->campaignTypeRepository->get(array('status' => 1));
        $this->data['dataFilter']['resultCampaignFilters'] = $this->campaignFilterRepository->get(array('status' => 1));
        $this->data['dataFilter']['resultCampaignStatuses'] = $this->campaignStatusRepository->get(array('status' => 1));

        $this->data['resultCampaigns'] = $this->campaignAssignRepository->getCampaignToAssign();
        $this->data['resultUsers'] = $this->userRepository->get(array(
            'status' => 1,
            'designation_slug' => array('ra_team_leader', 'ra_team_leader_business_delivery', 'research_analyst', 'sr_vendor_management_specialist'),
            'order_by' => array('value' => 'first_name', 'order' => 'ASC'),
        ));

        $this->data['resultAgentWorkTypes'] = AgentWorkTypeRepository::get(array('status' => 1));

        return view('manager.campaign_assign.list', $this->data);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $attributes = $request->all();
        $response = $this->campaignAssignRepository->store($attributes);
        if($response['status'] == TRUE) {
            return redirect()->route('manager.campaign_assign.list')->with('success', ['title' => 'Successful', 'message' => $response['message']]);
        } else {
            return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => $response['message']]);
        }
    }

    public function show($id, Request $request)
    {
        try {
            $this->data['resultCampaign'] = $this->campaignRepository->find(base64_decode($id), array('delivery_detail'));
            $this->data['resultCampaignIssues'] = $this->issueRepository->get(array('campaign_ids' => [base64_decode($id)]));
            $this->data['resultWorkTypes'] = AgentWorkTypeRepository::get(array('status' => 1));

            $this->data['resultAssignedUsers'] = array();
            $resultCARATLs = CampaignAssignRATL::where('campaign_id', base64_decode($id))->get();
            if(!empty($resultCARATLs) && $resultCARATLs->count()) {
                $this->data['resultAssignedUsers'] = array_merge($this->data['resultAssignedUsers'], $resultCARATLs->pluck('user_id')->toArray());
            }
            $resultCAAgents = CampaignAssignAgent::where('campaign_id', base64_decode($id))->get();
            if(!empty($resultCAAgents) && $resultCAAgents->count()) {
                $this->data['resultAssignedUsers'] = array_merge($this->data['resultAssignedUsers'], $resultCAAgents->pluck('user_id')->toArray());
            }
            $resultCAVMs = CampaignAssignVendorManager::where('campaign_id', base64_decode($id))->get();
            if(!empty($resultCAVMs) && $resultCAVMs->count()) {
                $this->data['resultAssignedUsers'] = array_merge($this->data['resultAssignedUsers'], $resultCAVMs->pluck('user_id')->toArray());
            }

            $this->data['resultUsers'] = $this->userRepository->get(array(
                'status' => 1,
                'designation_slug' => array('ra_team_leader', 'ra_team_leader_business_delivery', 'research_analyst', 'sr_vendor_management_specialist'),
            ));

            if($request->ajax() && !empty($this->data['resultCampaign'])) {
                return response()->json(array('status' => true, 'message' => 'Data Found', 'data' => $this->data['resultCampaign']));
            }

            return view('manager.campaign_assign.show', $this->data);
        } catch (\Exception $exception) {
            if($request->ajax()) {
                return response()->json(array('status' => false, 'message' => 'Something went wrong.'));
            } else {
                return redirect()->route('manager.campaign.list')->with('error', ['title' => 'Error while processing request', 'message' => 'Campaign details not found']);
            }
        }
    }

    public function updateDeliveryDetails(Request $request)
    {
        $attributes = $request->all();
        $attributes['updated_by'] = Auth::id();
        if($request->has('id') && !empty($request->get('id'))) {
            $cdd_id = base64_decode($request->get('id'));
        } else {
            $cdd_id = null;
        }

        if($request->has('campaign_id')) {
            $attributes['campaign_id'] = base64_decode($attributes['campaign_id']);
        }
        $response = $this->deliveryDetailRepository->update($cdd_id, $attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function getAssignedCampaigns(Request $request): \Illuminate\Http\JsonResponse
    {

        $this->data['resultAssignedCampaigns'] = $this->campaignAssignRepository->getAssignedCampaigns();

        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = Campaign::query();
        $query->whereNull('parent_id');

        $query->whereIn('id', $this->data['resultAssignedCampaigns']->pluck('id')->toArray());
        $query->with([
            'assigned_ratls',
            'assigned_agents',
            'assigned_vendor_managers',
            'children.assigned_ratls',
            'children.assigned_agents',
            'children.assigned_vendor_managers',
            ]);
        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->where(function($query) use ($searchValue){
                $query->where("campaign_id", "like", "%$searchValue%");
                $query->orWhere("name", "like", "%$searchValue%");
                $query->orWhere("allocation", "like", "%$searchValue%");
                $query->orWhere("deliver_count", "like", "%$searchValue%");
            });
        }

        //Filters
        if(!empty($filters)) {

            if(isset($filters['start_date']) && !empty($filters['start_date'])) {
                $start_date = date('Y-m-d', strtotime($filters['start_date']));
                $query->where('start_date', '>=', $start_date);
            }

            if(isset($filters['end_date']) && !empty($filters['end_date'])) {
                $end_date = date('Y-m-d', strtotime($filters['end_date']));
                $query->where('end_date', '<=', $end_date);
            }

            if(isset($filters['campaign_status_id']) && !empty($filters['campaign_status_id'])) {
                $query->whereIn('campaign_status_id',  $filters['campaign_status_id']);
            }

            if(isset($filters['delivery_day'])) {
                $query->whereHas('pacingDetails', function($pacingDetails) use($filters) {
                    $pacingDetails->whereNotNull('sub_allocation');
                    $pacingDetails->whereIn('day', $filters['delivery_day']);
                });
            }

            if(isset($filters['due_in'])) {

                $today_date = date('Y-m-d');

                switch ($filters['due_in']) {
                    case 'Today':
                        $query->where('end_date', '=', $today_date);
                        break;
                    case 'Tomorrow':
                        $tomorrow_date = date('Y-m-d', strtotime('+1 days'));
                        $query->where('end_date', '=', $tomorrow_date);
                        break;
                    case '7 Days':
                        $date_7days_later = date('Y-m-d', strtotime('+6 days'));
                        $query->whereBetween('end_date', [$today_date, $date_7days_later]);
                        break;
                    case 'Past Due':
                        $query->where('end_date', '<', $today_date);
                        break;
                }
            }

            if(isset($filters['country_id'])) {
                $query->whereHas('countries', function ($countries) use($filters) {
                    $countries->whereIn('country_id', $filters['country_id']);
                });
            }

            if(isset($filters['country_id'])) {
                $query->whereHas('countries', function ($countries) use($filters) {
                    $countries->whereIn('country_id', $filters['country_id']);
                });
            }

            if(isset($filters['region_id'])) {
                $query->whereHas('countries.country', function ($countries) use($filters) {
                    $countries->whereHas('region', function ($region) use($filters) {
                        $region->whereIn('id', $filters['region_id']);
                    });
                });
            }

            if(isset($filters['campaign_type_id'])) {
                $query->where('campaign_type_id', $filters['campaign_type_id']);
            }

            if(isset($filters['campaign_filter_id'])) {
                $query->where('campaign_filter_id', $filters['campaign_filter_id']);
            }
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
        //Do not take incremental and reactivated
        $query->with('children', function($children) {
            $children->orderBy('created_at', 'DESC');
        });


        $result = $query->get();

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
        $campaign_id = base64_decode($id);
        $result['resultRATLs'] = $this->campaignAssignRepository->getAssignedRATL($campaign_id);

        $result['resultVMs'] = $resultCAVMs = CampaignAssignVendorManager::where('campaign_id', $campaign_id)->with('vendors')->with('user')->with('userAssignedBy')->get();
        //$resultRATLs = CampaignAssignRATL::where('campaign_id', $campaign_id)->get();

        //$result['resultUsers'] = $resultRATLs->merge($resultCAVMs);

        if(!empty($result)) {
            return response()->json(array('status' => true, 'data' => $result));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function viewAssignedAgents($id, Request $request)
    {
        $result = $this->agentRepository->get(array('caratl_id' => base64_decode($id)));
        if(!empty($result)) {
            return response()->json(array('status' => true, 'data' => $result));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function viewAssignmentVendors($id, Request $request)
    {
        $result = $this->CAVendorRepository->get(array('cavm_id' => base64_decode($id)));
        if(!empty($result)) {
            return response()->json(array('status' => true, 'data' => $result));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function revokeCampaign($id)
    {
        $response = $this->campaignAssignRepository->revokeCampaign(base64_decode($id));
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function assignCampaign(Request $request)
    {
        $attributes = $request->all();
        $new_attributes['campaign_id'] = base64_decode($attributes['campaign_id']);
        $new_attributes['display_date'] = $attributes['display_date'];
        if(isset($attributes['agent_work_type_id']) && !empty($attributes['agent_work_type_id'])) {
            $new_attributes['agent_work_type_id'] = $attributes['agent_work_type_id'];
        }
        foreach ($attributes['user_list'] as $user) {
            $new_attributes['users'][] = array('user_id' => $user, 'allocation' => $attributes['allocation']);
        }
        $response = $this->campaignAssignRepository->store($new_attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function reAssignCampaign($id, Request $request)
    {
        $attributes = $request->all();
        $new_attributes['submitted_at'] = NULL;
        $new_attributes['status'] = 1;
        $response = $this->CARATLRepository->update(base64_decode($id), $new_attributes);

        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => 'Campaign re-assigned successfully'));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

}
