<?php

namespace App\Http\Controllers\EmailMarketingExecutive;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignAssignEME;
use App\Models\User;
use App\Repository\CampaignAssignRepository\EMERepository\EMERepository as CAEMERepository;
use App\Repository\CampaignEBBFileRepository\CampaignEBBFileRepository;
use App\Repository\CampaignNPFFileRepository\CampaignNPFFileRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromotionCampaignController extends Controller
{
    private $data;
    /**
     * @var CAEMERepository
     */
    private $CAEMERepository;
    /**
     * @var CampaignNPFFileRepository
     */
    private $campaignNPFFileRepository;
    /**
     * @var CampaignEBBFileRepository
     */
    private $campaignEBBFileRepository;

    public function __construct(
        CAEMERepository $CAEMERepository,
        CampaignNPFFileRepository $campaignNPFFileRepository,
        CampaignEBBFileRepository $campaignEBBFileRepository
    )
    {
        $this->data = array();
        $this->CAEMERepository = $CAEMERepository;
        $this->campaignNPFFileRepository = $campaignNPFFileRepository;
        $this->campaignEBBFileRepository = $campaignEBBFileRepository;
    }

    public function index()
    {
        return view('email_marketing_executive.promotion_campaign.list', $this->data);
    }

    public function show($id)
    {
        $this->data['resultCAEME'] = $this->CAEMERepository->find(base64_decode($id));
        $this->data['resultCampaignNPFFiles'] = $this->campaignNPFFileRepository->get(array('ca_eme_ids' => [base64_decode($id)]));
        //dd($this->data['resultCampaignNPFFiles']->toArray());
        return view('email_marketing_executive.promotion_campaign.show', $this->data);
    }

    public function uploadEBBFile($id, Request $request)
    {
        $response = array('status' => false, 'message' => 'Something went wrong, please try again.');
        if($request->hasFile('ebb_file')) {
            $attributes = $request->all();
            $resultCAEME = $this->CAEMERepository->find(base64_decode($id));
            $attributes['ca_eme_id'] = base64_decode($id);
            $attributes['campaign_id'] = $resultCAEME->campaign_id;
            $response = $this->campaignEBBFileRepository->store($attributes);
            if($response['status'] == TRUE) {
                $response = array('status' => true, 'message' => $response['message']);
            } else {
                $response = array('status' => false, 'message' => $response['message']);
            }
        } else {
            $response = array('status' => false, 'message' => 'Please upload file');
        }
        return response()->json($response);
    }

    public function submitCampaign($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes['submitted_at'] = date('Y-m-d H:i:s');
        $response = $this->CAEMERepository->update(base64_decode($id), $attributes);
        if($response['status'] == TRUE) {
            //Add Campaign History
            $resultCampaign = Campaign::findOrFail($response['details']->campaign_id);
            $resultUser = User::findOrFail($response['details']->user_id);
            add_campaign_history($resultCampaign->id, $resultCampaign->parent_id, 'Campaign promotion completed by -'.$resultUser->full_name);
            add_history('Campaign promotion completed', 'Campaign promotion completed by -'.$resultUser->full_name);

            return response()->json(array('status' => true, 'message' => 'Campaign submitted successfully'));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
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

        $query = CampaignAssignEME::query();

        $query->whereUserId(Auth::id());

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
            case '0': $query->orderBy('campaign_id', $orderDirection); break;
            case '1': $query->orderBy('name', $orderDirection); break;
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
}
