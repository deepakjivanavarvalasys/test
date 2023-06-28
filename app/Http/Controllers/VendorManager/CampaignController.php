<?php

namespace App\Http\Controllers\VendorManager;

use App\Http\Controllers\Controller;
use App\Models\CampaignAssignRATL;
use App\Models\CampaignAssignVendorManager;
use App\Repository\CampaignAssignRepository\VendorManagerRepository\VendorManagerRepository as CAVendorManagerRepository;
use App\Repository\CampaignRepository\CampaignRepository;
use App\Repository\CampaignSpecificationRepository\CampaignSpecificationRepository;
use App\Repository\CampaignStatusRepository\CampaignStatusRepository;
use App\Repository\VendorRepository\VendorRepository;
use App\Repository\CampaignAssignRepository\CampaignAssignRepository;
//use App\Repository\UserRepository\UserRepository;
use App\Repository\CampaignAssignRepository\VendorRepository\VendorRepository as CAVendorRepository;
use Illuminate\Http\Request;
use App\Models\Vendor;
use Auth;


class CampaignController extends Controller
{
    private $data;
    private $vendorRepository;
    private $campaignAssignRepository;
    private $campaignRepository;
    private $campaignStatusRepository;
    private $CAVendorRepository;
    private $CAVendorManagerRepository;

    public function __construct(
        VendorRepository $vendorRepository,
        CampaignAssignRepository $campaignAssignRepository,
        CampaignRepository $campaignRepository,
        CampaignStatusRepository $campaignStatusRepository,
        CAVendorRepository $CAVendorRepository,
        CAVendorManagerRepository $CAVendorManagerRepository

    )
    {
        $this->data = array();
        $this->vendorRepository = $vendorRepository;
        $this->campaignAssignRepository = $campaignAssignRepository;
        $this->campaignRepository = $campaignRepository;
        $this->campaignStatusRepository = $campaignStatusRepository;
        $this->CAVendorRepository = $CAVendorRepository;
        $this->CAVendorManagerRepository = $CAVendorManagerRepository;
    }

    public function index()
    {
        return view('vendor_manager.campaign.list');
    }

    public function show($cavm_id)
    {
        try {
            $this->data['resultCAVM'] = $this->CAVendorManagerRepository->find(base64_decode($cavm_id));
            $this->data['resultCampaign'] = $this->campaignRepository->find($this->data['resultCAVM']->campaign_id);
            return view('vendor_manager.campaign.show', $this->data);
        } catch (\Exception $exception) {
            return redirect()->route('vendor_manager.campaign.list')->with('error', ['title' => 'Error while processing request', 'message' => 'Campaign details not found']);
        }
    }
    public function getCampaigns(Request $request)
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = CampaignAssignVendorManager::query();

        //$query->whereIn('id', $campaignIds);
        $query->whereUserId(\Illuminate\Support\Facades\Auth::id())->with('campaign');

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
            case '0': $query->orderBy('created_at', $orderDirection); break;
            case '1':  break;
            case '2':  break;
            case '3':  break;
            case '4':  break;
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
