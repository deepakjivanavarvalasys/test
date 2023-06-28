<?php

namespace App\Http\Controllers\QualityAnalyst;

use App\Exports\AgentLeadExport;
use App\Exports\NPFExport;
use App\Exports\VendorLeadExport;
use App\Http\Controllers\Controller;
use App\Models\AgentLead;
use App\Models\Campaign;
use App\Models\CampaignAssignQualityAnalyst;
use App\Models\User;
use App\Models\VendorLead;
use App\Repository\CampaignAssignRepository\EMERepository\EMERepository as CAEMERepository;
use App\Repository\CampaignAssignRepository\QATLRepository\QATLRepository as CAQATLRepository;
use App\Repository\CampaignAssignRepository\QualityAnalystRepository\QualityAnalystRepository;
use App\Repository\CampaignEBBFileRepository\CampaignEBBFileRepository;
use App\Repository\UserRepository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Excel;

class CampaignController extends Controller
{
    private $data;
    /**
     * @var QualityAnalystRepository
     */
    private $qualityAnalystRepository;
    /**
     * @var CAEMERepository
     */
    private $CAEMERepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var CampaignEBBFileRepository
     */
    private $campaignEBBFileRepository;
    /**
     * @var CampaignFileRepository
     */
    private $campaignFileRepository;
    /**
     * @var CAQATLRepository
     */
    private $CAQATLRepository;

    public function __construct(
        QualityAnalystRepository $qualityAnalystRepository,
        CAEMERepository $CAEMERepository,
        UserRepository $userRepository,
        CampaignEBBFileRepository $campaignEBBFileRepository,
        CAQATLRepository $CAQATLRepository
    )
    {
        $this->data = array();
        $this->qualityAnalystRepository = $qualityAnalystRepository;
        $this->CAEMERepository = $CAEMERepository;
        $this->userRepository = $userRepository;
        $this->campaignEBBFileRepository = $campaignEBBFileRepository;
        $this->CAQATLRepository = $CAQATLRepository;
    }

    public function index()
    {
        return view('quality_analyst.campaign.list', $this->data);
    }

    public function show($id)
    {
        $this->data['resultCAQA'] = $this->qualityAnalystRepository->find(base64_decode($id));
        $this->data['resultCampaignEBBFiles'] = $this->campaignEBBFileRepository->get(array('campaign_ids' => [$this->data['resultCAQA']->campaign_id]));
        $this->data['resultEMEUsers'] = $this->userRepository->get(array('designation_slug' => ['email_marketing_executive']));
        //dd($this->data['resultCAQA']->campaign->toArray());
        return view('quality_analyst.campaign.show', $this->data);
    }

    public function submitCampaign($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $finalResponse = array('status' => false, 'message' => 'Something went wrong, please try again.');
        if($request->hasFile('final_file')) {
            $attributes = $request->all();
            $attributes['submitted_at'] = date('Y-m-d H:i:s');
            $response = $this->qualityAnalystRepository->update(base64_decode($id), $attributes);
            if($response['status'] == TRUE) {
                //Add Campaign History
                $resultCampaign = Campaign::findOrFail($response['details']->campaign_id);
                $resultUser = User::findOrFail($response['details']->user_id);
                add_campaign_history($resultCampaign->id, $resultCampaign->parent_id, 'Campaign submitted by QA -'.$resultUser->full_name);
                add_history('Campaign submitted by QA', 'Campaign submitted by QA -'.$resultUser->full_name);

                $finalResponse = array('status' => true, 'message' => 'Campaign submitted successfully');
            } else {
                $finalResponse = array('status' => false, 'message' => $response['message']);
            }
        } else {
            $finalResponse = array('status' => false, 'message' => 'Please upload file');
        }

        return response()->json($finalResponse);
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

        $query = CampaignAssignQualityAnalyst::query();

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

    public function downloadFile($id)
    {
        $response = array('status' => true, 'message' => 'Something went wrong, please try again.');
        try {
            $resultCAQA = $this->qualityAnalystRepository->find(base64_decode($id));
            $resultCAQATL = $this->CAQATLRepository->find($resultCAQA->campaign_assign_qatl_id);
            $resultCampaign = Campaign::find($resultCAQA->campaign_id);

            $path = 'public/campaigns/'.$resultCampaign->campaign_id.'/quality/';
            $path_to_download = '/public/storage/campaigns/'.$resultCampaign->campaign_id.'/quality/';

            switch ($resultCAQATL->userAssignedBy->role->slug)
            {
                case 'vendor_management' :
                    $filename = str_replace(' ', '_', trim($resultCampaign->name)) .'_'.time(). "_VENDOR_DATA.xlsx";
                    if(Excel::store(new VendorLeadExport($resultCAQA->campaign_id), $path.$filename)) {
                        VendorLead::whereCampaignId($resultCAQA->campaign_id)
                            ->whereStatus(1)
                            ->whereNotNull('send_date')
                            ->whereNull('qc_download_date')
                            ->update(array('qc_download_date' => date('Y-m-d H:i:s')));
                        $response = array('status' => true, 'message' => 'Successful', 'file_name' => $path_to_download.$filename);
                    } else {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                    break;
                case 'team_leader':
                default:
                    $filename = str_replace(' ', '_', trim($resultCampaign->name)) .'_'.time(). "_AGENT_DATA.xlsx";
                    if(Excel::store(new AgentLeadExport($resultCAQA->campaign_id), $path.$filename)) {

                        AgentLead::whereCampaignId($resultCAQA->campaign_id)
                            ->whereStatus(1)
                            ->whereNotNull('send_date')
                            ->whereNull('qc_download_date')
                            ->update(array('qc_download_date' => date('Y-m-d H:i:s')));

                        $response = array('status' => true, 'message' => 'Successful', 'file_name' => $path_to_download.$filename);
                    } else {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                    break;
            }

        } catch (\Exception $exception) {
            $response = array('status' => false, 'message' => 'Something went wrong, please try again.');
        }

        return response()->json($response);
    }

    public function downloadNPF($id)
    {
        $response = array('status' => true, 'message' => 'Something went wrong, please try again.');
        try {
            $resultCAQA = $this->qualityAnalystRepository->find(base64_decode($id));
            $resultCampaign = Campaign::find($resultCAQA->campaign_id);

            $path = 'public/campaigns/'.$resultCampaign->campaign_id.'/quality/';
            $path_to_download = '/public/storage/campaigns/'.$resultCampaign->campaign_id.'/quality/';
            $filename = str_replace(' ', '_', trim($resultCampaign->name)) .'_'.time(). "_NPF.xlsx";
            if(Excel::store(new NPFExport($resultCAQA->campaign_id), $path.$filename)) {
                $response = array('status' => true, 'message' => 'Successful', 'file_name' => $path_to_download.$filename);
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            $response = array('status' => false, 'message' => 'Something went wrong, please try again.');
        }

        return response()->json($response);

    }

    public function uploadNPF($id, Request $request)
    {
        $response = array('status' => false, 'message' => 'Something went wrong, please try again.');
        if($request->hasFile('npf_file') && !empty($request->get('user_id'))) {
            $attributes = $request->all();
            $resultCAQA = $this->qualityAnalystRepository->find(base64_decode($id));
            $attributes['campaign_id'] = $resultCAQA->campaign_id;
            $attributes['display_date'] = date('Y-m-d', strtotime($resultCAQA->display_date));
            $response = $this->CAEMERepository->store($attributes);
            if($response['status'] == TRUE) {
                $response = array('status' => true, 'message' => $response['message']);
            } else {
                $response = array('status' => false, 'message' => $response['message']);
            }
        } else {
            $response = array('status' => false, 'message' => 'Required field missing');
        }

        return response()->json($response);
    }
}
