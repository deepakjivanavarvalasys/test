<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignAssignQATL;
use App\Models\CampaignDeliveryDetail;
use App\Repository\Campaign\History\CampaignHistoryRepository;
use App\Repository\CampaignFilterRepository\CampaignFilterRepository;
use App\Repository\CampaignRepository\CampaignRepository;
use App\Repository\CampaignSpecificationRepository\CampaignSpecificationRepository;
use App\Repository\CampaignStatusRepository\CampaignStatusRepository;
use App\Repository\CampaignTypeRepository\CampaignTypeRepository;
use App\Repository\CountryRepository\CountryRepository;
use App\Repository\HolidayRepository\HolidayRepository;
use App\Repository\PacingDetailRepository\PacingDetailRepository;
use App\Repository\RegionRepository\RegionRepository;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    private $data;
    private $campaignStatusRepository;
    private $campaignFilterRepository;
    private $campaignTypeRepository;
    private $countryRepository;
    private $regionRepository;
    private $holidayRepository;
    private $campaignRepository;
    private $campaignSpecificationRepository;
    private $pacingDetailRepository;
    /**
     * @var CampaignHistoryRepository
     */
    private $campaignHistoryRepository;

    public function __construct(
        CampaignStatusRepository $campaignStatusRepository,
        CampaignFilterRepository $campaignFilterRepository,
        CampaignTypeRepository $campaignTypeRepository,
        CountryRepository $countryRepository,
        RegionRepository $regionRepository,
        HolidayRepository $holidayRepository,
        CampaignRepository $campaignRepository,
        CampaignSpecificationRepository $campaignSpecificationRepository,
        PacingDetailRepository $pacingDetailRepository,
        CampaignHistoryRepository $campaignHistoryRepository
    )
    {
        $this->data = array();
        $this->campaignStatusRepository = $campaignStatusRepository;
        $this->campaignFilterRepository = $campaignFilterRepository;
        $this->campaignTypeRepository =$campaignTypeRepository;
        $this->countryRepository = $countryRepository;
        $this->regionRepository = $regionRepository;
        $this->holidayRepository = $holidayRepository;
        $this->campaignRepository = $campaignRepository;
        $this->campaignSpecificationRepository = $campaignSpecificationRepository;
        $this->pacingDetailRepository = $pacingDetailRepository;
        $this->campaignHistoryRepository = $campaignHistoryRepository;
    }

    public function index()
    {
        $this->data['dataFilter']['resultCountries'] = $this->countryRepository->get(array('status' => 1));
        $this->data['dataFilter']['resultRegions'] = $this->regionRepository->get(array('status' => 1));
        $this->data['dataFilter']['resultCampaignTypes'] = $this->campaignTypeRepository->get(array('status' => 1));
        $this->data['dataFilter']['resultCampaignFilters'] = $this->campaignFilterRepository->get(array('status' => 1));
        $this->data['dataFilter']['resultCampaignStatuses'] = $this->campaignStatusRepository->get(array('status' => 1));
        return view('manager.campaign.list', $this->data);
    }

    public function show($id)
    {
        try {
            $this->data['resultCampaignStatuses'] = $this->campaignStatusRepository->get(array('status' => 1));
            $this->data['resultCampaignFilters'] = $this->campaignFilterRepository->get(array('status' => 1));
            $this->data['resultCampaignTypes'] = $this->campaignTypeRepository->get(array('status' => 1));
            $this->data['resultCountries'] = $this->countryRepository->get(array('status' => 1));
            $this->data['resultRegions'] = $this->regionRepository->get(array('status' => 1));
            $this->data['resultCampaign'] = $this->campaignRepository->find(base64_decode($id));
            //dd($this->data['resultCampaign']->children->count());
            return view('manager.campaign.show', $this->data);
        } catch (\Exception $exception) {
            return redirect()->route('manager.campaign.list')->with('error', ['title' => 'Error while processing request', 'message' => 'Campaign details not found']);
        }
    }

    public function create()
    {
        $this->data['resultCampaignStatuses'] = $this->campaignStatusRepository->get(array('status' => 1));
        $this->data['resultCampaignFilters'] = $this->campaignFilterRepository->get(array('status' => 1));
        $this->data['resultCampaignTypes'] = $this->campaignTypeRepository->get(array('status' => 1));
        $this->data['resultCountries'] = $this->countryRepository->get(array('status' => 1));
        $this->data['resultRegions'] = $this->regionRepository->get(array('status' => 1));
        $this->data['resultHolidays'] = $this->holidayRepository->get(array('status' => 1));
        return view('manager.campaign.create', $this->data);
    }

    public function createIncremental($id)
    {
        $this->data['resultCampaign'] = $this->campaignRepository->find(base64_decode($id));
        $this->data['resultCampaignStatuses'] = $this->campaignStatusRepository->get(array('status' => 1));
        $this->data['resultCampaignFilters'] = $this->campaignFilterRepository->get(array('status' => 1));
        $this->data['resultCampaignTypes'] = $this->campaignTypeRepository->get(array('status' => 1));
        $this->data['resultCountries'] = $this->countryRepository->get(array('status' => 1));
        $this->data['resultRegions'] = $this->regionRepository->get(array('status' => 1));
        $this->data['resultHolidays'] = $this->holidayRepository->get(array('status' => 1));
        return view('manager.campaign.incremental.create', $this->data);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $attributes = $request->all();
        $response = $this->campaignRepository->store($attributes);
        if($response['status'] == TRUE) {
            if($request->has('parent_id')) {
                return redirect()->route('manager.campaign.show', $attributes['parent_id'])->with('success', ['title' => 'Successful', 'message' => $response['message']]);
            } else {
                return redirect()->route('manager.campaign.list')->with('success', ['title' => 'Successful', 'message' => $response['message']]);
            }
        } else {
            return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => $response['message']]);
        }
    }

    public function editPacingDetails($id): \Illuminate\Http\JsonResponse
    {
        $resultCampaign = $this->campaignRepository->find(base64_decode($id));
        if(!empty($resultCampaign)) {
            return response()->json(array('status' => true, 'message' => 'Data found', 'data' => $resultCampaign));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function editSubAllocations($id): \Illuminate\Http\JsonResponse
    {
        $this->data['resultCampaign'] = $this->campaignRepository->find(base64_decode($id));
        $this->data['resultSubAllocations'] = $this->pacingDetailRepository->get(base64_decode($id));
        if(!empty($this->data['resultSubAllocations'])) {
            $start_date    = (new \DateTime($this->data['resultCampaign']->start_date));
            $end_date      = (new \DateTime($this->data['resultCampaign']->end_date));
        }
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start_date, $interval, $end_date);
        $this->data['resultMonthList'] = array();
        $this->data['total_sub_allocation'] = 0;

        $month = strtotime($this->data['resultCampaign']->start_date);
        $end = strtotime(date('Y-m-t', strtotime($this->data['resultCampaign']->end_date)));

        while($month < $end)
        {
            $resultSubAllocations = $this->pacingDetailRepository->get(base64_decode($id), array('month' => date('m', $month),'year' => date('Y', $month)));
            $this->data['resultMonthList'][] = array(
                'month_name' => date('M', $month),
                'month' => date('m', $month),
                'year' => date('Y', $month),
                'sub_allocations' => $resultSubAllocations,
                'days' => $resultSubAllocations->pluck('day')->unique()->toArray()
            );

            $month = strtotime("+1 month", $month);
        }

        if(!empty($this->data)) {
            return response()->json(array('status' => true, 'message' => 'Data found', 'data' => $this->data));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function edit($campaign_id, Request $request)
    {
        try {
            $this->data['resultCampaignStatuses'] = $this->campaignStatusRepository->get(array('status' => 1));
            $this->data['resultCampaignFilters'] = $this->campaignFilterRepository->get(array('status' => 1));
            $this->data['resultCampaignTypes'] = $this->campaignTypeRepository->get(array('status' => 1));
            $this->data['resultCountries'] = $this->countryRepository->get(array('status' => 1));
            $this->data['resultRegions'] = $this->regionRepository->get(array('status' => 1));
            $this->data['resultCampaign'] = $this->campaignRepository->find(base64_decode($campaign_id));
            return view('manager.campaign.edit', $this->data);
        } catch (\Exception $exception) {
            return redirect()->route('manager.campaign.list')->with('error', ['title' => 'Error while processing request', 'message' => 'Campaign details not found']);
        }
    }

    public function update($id, Request $request)
    {
        $attributes = $request->all();
        $response = $this->campaignRepository->update(base64_decode($id), $attributes);
        if($request->ajax()) {
            if($response['status'] == TRUE) {
                return response()->json(array('status' => true, 'message' => $response['message']));
            } else {
                return response()->json(array('status' => false, 'message' => $response['message']));
            }
        } else {
            if($response['status'] == TRUE) {
                return redirect()->route('manager.campaign.show', $id)->with('success', ['title' => 'Successful', 'message' => $response['message']]);
            } else {
                return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => $response['message']]);
            }

        }

    }

    public function updateSubAllocations($id, Request $request)
    {
        $attributes = $request->all();
        $response = $this->pacingDetailRepository->update(base64_decode($attributes['campaign_id']), $attributes);
        if($request->ajax()) {
            if($response['status'] == TRUE) {
                return response()->json(array('status' => true, 'message' => $response['message']));
            } else {
                return response()->json(array('status' => false, 'message' => $response['message']));
            }
        } else {
            if($response['status'] == TRUE) {
                return redirect()->route('manager.campaign.show', $id)->with('success', ['title' => 'Successful', 'message' => $response['message']]);
            } else {
                return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => $response['message']]);
            }
        }
    }

    public function validateVMailCampaignId(Request $request)
    {
        $campaign = Campaign::query();
        $campaign = $campaign->where('v_mail_campaign_id', $request->v_mail_campaign_id);

        if($request->has('campaign_id') || $request->has('parent_id')) {
            $campaignIds = array();
            $campaign_id = 0;
            if($request->has('parent_id')) {
                $campaign_id = base64_decode($request->parent_id);
                //$campaign = $campaign->where('id', '!=', base64_decode($request->parent_id));
            } else {
                $campaign_id = base64_decode($request->campaign_id);
                //$campaign = $campaign->where('id', '!=', base64_decode($request->campaign_id));
            }
            $resultCampaign = Campaign::find($campaign_id);
            if(isset($resultCampaign->children) ) {
                $campaignIds = $resultCampaign->children->pluck('id')->toArray();
            }
            $campaignIds[] = $resultCampaign->id;
            $campaign = $campaign->whereNotIn('id', $campaignIds);
        }

        if($campaign->exists()) {
            return 'false';
        } else {
            return 'true';
        }
    }

    public function validateCampaignName(Request $request)
    {
        $campaign = Campaign::query();
        $campaign->where(function($query) use ($request){
            $query->where('name', $request->name);
            $query->orWhere('name', trim($request->name));
        });

        if($request->has('campaign_id') || $request->has('parent_id')) {
            $campaignIds = array();
            $campaign_id = 0;
            if($request->has('parent_id')) {
                $campaign_id = base64_decode($request->parent_id);
                //$campaign = $campaign->where('id', '!=', base64_decode($request->parent_id));
            } else {
                $campaign_id = base64_decode($request->campaign_id);
                //$campaign = $campaign->where('id', '!=', base64_decode($request->campaign_id));
            }
            $resultCampaign = Campaign::find($campaign_id);
            if(isset($resultCampaign->children) ) {
                $campaignIds = $resultCampaign->children->pluck('id')->toArray();
            }
            $campaignIds[] = $resultCampaign->id;
            $campaign = $campaign->whereNotIn('id', $campaignIds);
        }

        if($campaign->exists()) {
            return 'false';
        } else {
            return 'true';
        }
    }

    public function attachSpecification($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->campaignRepository->updateSpecification(base64_decode($id), $attributes);
        if($response['status'] == TRUE) {

            return response()->json(array('status' => true, 'message' => $response['message'], 'data' => $response['data']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function attachCampaignFile($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->campaignRepository->updateCampaignFile(base64_decode($id), $attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message'], 'data' => $response['data']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function removeSpecification($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $response = $this->campaignSpecificationRepository->destroy(base64_decode($id));
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    //Import bulk campaigns
    public function import(Request $request)
    {
        $attributes = $request->all();
        if(isset($attributes['campaign_file']) && !empty($attributes['campaign_file'])) {
            $response = $this->campaignRepository->import($attributes);
            if($response['status'] == TRUE) {
                return response(json_encode(array('status' => true, 'message' => $response['message'])), 205);
            } else {
                if(!empty($response['file'])) {
                    return $response['file'];
                    //return response(json_encode(array('status' => false, 'message' => $response['message'], 'file' => base64_encode($response['file']))));
                } else {
                    return response(json_encode(array('status' => false, 'message' => $response['message'])),201);
                }
            }
        } else {
            return response(json_encode(array('status' => false, 'message' => 'Please upload file')),202);
        }
    }

    public function getCampaigns(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];

        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = Campaign::query();
        $query->whereNull('parent_id');

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
            case '3':
                break;
            case '4': $query->orderBy('start_date', $orderDirection); break;
            case '5': $query->orderBy('end_date', $orderDirection); break;
            case '7': $query->orderBy('allocation', $orderDirection); break;
            case '14': $query->orderBy('campaign_status_id', $orderDirection); break;
            default: $query->orderBy('created_at', 'DESC'); break;
        }

        $totalFilterRecords = $query->count();
        if($limit > 0) {
            $query->offset($offset);
            $query->limit($limit);
        }

        $query->with('delivery_detail');
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

    public function getCampaignHistory($id, Request $request)
    {
        $filters = $request->all();
        $filters['order_by']['column'] = 'created_at';
        $filters['order_by']['dir'] = 'desc';
        $filters['campaign_ids'] = [base64_decode($id)];

        $this->data['resultCampaignHistories'] = $this->campaignHistoryRepository->get($filters);

        return view('manager.extra.campaign_history', $this->data);
    }


    /**
     * Remote Validation
     */
    public function checkCampaignNameAlreadyExists($id = null, Request $request)
    {
        $query = Campaign::query();
        if(isset($id) && !empty($id)) {
            $query->whereCampaignId(base64_decode($id));
        }
        $query->whereName(trim($request->name));

        if($query->exists()) {
            return 'false';
        } else {
            return 'true';
        }
    }
}
