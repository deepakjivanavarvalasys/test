<?php

namespace App\Http\Controllers\VendorManager;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignAssignQATL;
use App\Models\CampaignAssignRATL;
use App\Models\CampaignAssignVendor;
use App\Models\CampaignAssignVendorManager;
use App\Models\Role;
use App\Models\User;
use App\Repository\CampaignAssignRepository\CampaignAssignRepository;
use App\Repository\CampaignAssignRepository\QATLRepository\QATLRepository as CAQATLRepository;
use App\Repository\CampaignAssignRepository\VendorManagerRepository\VendorManagerRepository as CAVendorManagerRepository;
use App\Repository\CampaignAssignRepository\VendorRepository\VendorRepository as CAVendorRepository;
use App\Repository\CampaignRepository\CampaignRepository;
use App\Repository\Notification\QATL\QATLNotificationRepository;
use App\Repository\VendorRepository\VendorRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignAssignController extends Controller
{
    private $data;
    private $campaignAssignRepository;
    private $CAVendorRepository;
    private $vendorRepository;
    private $campaignRepository;
    /**
     * @var \App\Http\Controllers\VendorManager\CAVendorManagerRepository
     */
    private $CAVendorManagerRepository;
    /**
     * @var CAQATLRepository
     */
    private $CAQATLRepository;

    public function __construct(
        CampaignAssignRepository $campaignAssignRepository,
        CAVendorRepository $CAVendorRepository,
        VendorRepository $vendorRepository,
        CampaignRepository $campaignRepository,
        CAVendorManagerRepository $CAVendorManagerRepository,
        CAQATLRepository $CAQATLRepository
    )
    {
        $this->data = array();
        $this->campaignAssignRepository = $campaignAssignRepository;
        $this->CAVendorRepository = $CAVendorRepository;
        $this->vendorRepository = $vendorRepository;
        $this->campaignRepository =$campaignRepository;
        $this->CAVendorManagerRepository = $CAVendorManagerRepository;
        $this->CAQATLRepository = $CAQATLRepository;
    }

    public function index()
    {
        //Deprecated By CRocker => $this->data['resultCampaigns'] = $this->campaignAssignRepository->getCampaignToAssignForVM(Auth::id());

        $resultCAVendors = CampaignAssignVendor::where('assigned_by', Auth::id())->get();
        $this->data['resultCampaigns'] = CampaignAssignVendorManager::whereNotIn('campaign_id', $resultCAVendors->pluck('campaign_id')->toArray())->where('status', 1)->with('campaign')->whereUserId(Auth::id())->get();
        $this->data['resultVendors'] = $this->vendorRepository->get(array('status' => 1));
        //dd($this->data['resultCampaigns']->toArray());
        return view('vendor_manager.campaign_assign.list', $this->data);
    }
    public function show($cavm_id)
    {
        try {
            $this->data['resultCAVM'] = $this->CAVendorManagerRepository->find(base64_decode($cavm_id));
            $this->data['resultCampaign'] = $this->campaignRepository->find($this->data['resultCAVM']->campaign->id);
            //dd($this->data['resultCAVM']->toArray());
            return view('vendor_manager.campaign_assign.show', $this->data);
        } catch (\Exception $exception) {
            return redirect()->route('vendor_manager.campaign_assign.list')->with('error', ['title' => 'Error while processing request', 'message' => 'Campaign details not found']);
        }
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $successCount = $failedCount = 0;
        $resultCAVM = CampaignAssignVendorManager::findOrFail($request->campaign_assign_vm_id);

        if(empty($resultCAVM->started_at)) {

            foreach ($request->vendors as $key => $vendor) {

                $attributes = array(
                    'campaign_id' => $request->campaign_id,
                    'campaign_assign_vm_id' => $request->campaign_assign_vm_id,
                    'vendor_id' => $vendor['vendor_id'],
                    'display_date' => date('Y-m-d', strtotime($resultCAVM->display_date)),
                    'started_at' => date('Y-m-d'),
                    'allocation' => $vendor['allocation'],
                    'assigned_by' => Auth::id()
                );

                $response = $this->CAVendorRepository->store($attributes);

                if($response['status'] == TRUE) {
                    $successCount++;
                } else {
                    $failedCount++;
                }

            }

            if($successCount) {
                $resultCAVM->started_at = date('Y-m-d');
                $resultCAVM->save();
                return redirect()->route('vendor_manager.campaign_assign.list')->with('success', ['title' => 'Successful', 'message' => 'Campaign assigned successfully']);
            } else {
                return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => 'Something went wrong, please try again.']);
            }

        } else {
            return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => 'Campaign already assigned, please check and try again.']);
        }

    }

    public function submitCampaign($id, Request $request)
    {
        $attributes['submitted_at'] = date('Y-m-d H:i:s');
        $response = $this->CAVendorManagerRepository->update(base64_decode($id), $attributes);
        if($response['status'] == TRUE) {
            //Add Campaign History
            $resultCampaign = Campaign::findOrFail($response['details']->campaign_id);
            add_campaign_history($resultCampaign->id, $resultCampaign->parent_id, 'Submitted the campaign');
            add_history('Campaign Submitted By Vendor Manager', 'Submitted the campaign');

            //check submitted by all vendor manager's
            if(!CampaignAssignVendorManager::where('campaign_id', $response['details']->campaign_id)->whereNull('submitted_at')->count()) {

                $resultCAVM = $this->CAVendorManagerRepository->find(base64_decode($id));
                $resultRole = Role::whereSlug('qa_team_leader')->whereStatus(1)->first();
                $resultUser = User::whereRoleId($resultRole->id)->whereStatus(1)->first();
                $resultCAQATL = CampaignAssignQATL::where('campaign_id', $resultCAVM->campaign_id)->where('user_id', $resultUser->id)->first();

                if(empty($resultCAQATL->id)) {
                    $attributes = array(
                        'campaign_id' => $resultCAVM->campaign_id,
                        'user_id' => $resultUser->id,
                        'display_date' => $resultCAVM->display_date,
                        'assigned_by' => $resultCAVM->user_id,
                    );
                    $responseCAQATL = $this->CAQATLRepository->store($attributes);
                } else {
                    $responseCAQATL['status'] = TRUE;
                    $responseCAQATL['message'] = 'Campaign submitted successfully';

                    //Send notification to qatl
                    QATLNotificationRepository::store(array(
                        'sender_id' => $resultCAVM->user_id,
                        'recipient_id' => $resultUser->id,
                        'message' => 'Campaign submitted by Vendor Manager - '.$resultCampaign->name,
                        'url' => route('qa_team_leader.campaign.show', base64_encode($resultCAQATL->campaign_id))
                    ));
                }

                if($responseCAQATL['status']) {
                    return response()->json(array('status' => true, 'message' => $responseCAQATL['message']));
                } else {
                    return response()->json(array('status' => false, 'message' => $responseCAQATL['message']));
                }
            } else {
                return response()->json(array('status' => true, 'message' => 'Campaign submitted successfully'));
            }
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function getAssignedCampaigns(Request $request): \Illuminate\Http\JsonResponse
    {

        //$this->data['resultAssignedCampaigns'] = $this->campaignAssignRepository->getAssignedCampaignToVendors(Auth::id());
        $resultCAVendors = CampaignAssignVendor::where('assigned_by', Auth::id())->get();


        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = CampaignAssignVendorManager::query();

        $query->whereIn('id', $resultCAVendors->pluck('campaign_assign_vm_id')->toArray());

        $query->whereUserId(Auth::id());

        $query->with('campaign');
        $query->with('vendors');

        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->where("id", "like", "%$searchValue%");
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
            case '0': $query->orderBy('id', $orderDirection); break;
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
