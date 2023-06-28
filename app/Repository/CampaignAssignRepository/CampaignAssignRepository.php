<?php

namespace App\Repository\CampaignAssignRepository;

use App\Models\Campaign;
use App\Models\CampaignAssignAgent;
use App\Models\CampaignAssignRATL;
use App\Models\CampaignAssignVendor;
use App\Models\CampaignAssignVendorManager;
use App\Models\CampaignDeliveryDetail;
use App\Models\RANotification;
use App\Models\RATLNotification;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VMNotification;
use App\Repository\CampaignAssignRepository\AgentRepository\AgentRepository;
use App\Repository\CampaignAssignRepository\RATLRepository\RATLRepository;
use App\Repository\CampaignAssignRepository\VendorManagerRepository\VendorManagerRepository;
use App\Repository\Notification\RA\RANotificationRepository;
use App\Repository\Notification\RATL\RATLNotificationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignAssignRepository implements CampaignAssignInterface
{
    /**
     * @var RATLRepository
     */
    private $RATLRepository;
    /**
     * @var RATLNotificationRepository
     */
    private $RATLNotificationRepository;
    /**
     * @var AgentRepository
     */
    private $agentRepository;
    /**
     * @var RANotificationRepository
     */
    private $RANotificationRepository;
    /**
     * @var VendorManagerRepository
     */
    private $vendorManagerRepository;

    public function __construct(
        RATLRepository $RATLRepository,
        AgentRepository $agentRepository,
        VendorManagerRepository $vendorManagerRepository,
        RATLNotificationRepository $RATLNotificationRepository,
        RANotificationRepository $RANotificationRepository
    )
    {

        $this->RATLRepository = $RATLRepository;
        $this->RATLNotificationRepository = $RATLNotificationRepository;
        $this->agentRepository = $agentRepository;
        $this->RANotificationRepository = $RANotificationRepository;
        $this->vendorManagerRepository = $vendorManagerRepository;
    }

    public function getAssignedCampaigns($filters = array())
    {
        $resultAssignedCampaigns = array();
        //get campaigns already assigned
        $result['RATL'] = CampaignAssignRATL::select('campaign_id')->whereIn('status',[1,2])->get();
        $result['AGENT'] = CampaignAssignAgent::select('campaign_id')->whereIn('status',[1,2])->get();
        $result['VM'] = CampaignAssignVendorManager::select('campaign_id')->whereIn('status',[1,2])->get();

        if(!empty($result['RATL'])) {
            $resultAssignedCampaigns = array_unique (array_merge ($resultAssignedCampaigns, $result['RATL']->pluck('campaign_id')->toArray()));
        }

        if(!empty($result['AGENT'])) {
            $resultAssignedCampaigns = array_unique (array_merge ($resultAssignedCampaigns, $result['AGENT']->pluck('campaign_id')->toArray()));
        }

        if(!empty($result['VM'])) {
            $resultAssignedCampaigns = array_unique (array_merge ($resultAssignedCampaigns, $result['VM']->pluck('campaign_id')->toArray()));
        }


        $resultAssignedCampaigns = Campaign::whereIn('id', $resultAssignedCampaigns)->get();

        $resultAssignedCampaignIds = array();
        if($resultAssignedCampaigns->count()) {
            foreach ($resultAssignedCampaigns as $campaign) {
                if($campaign->parent_id) {
                    $resultAssignedCampaignIds[] = $campaign->parent_id;
                } else {
                    $resultAssignedCampaignIds[] = $campaign->id;
                }
            }
        }
        $query = Campaign::query();
        $query->whereIn('id', $resultAssignedCampaignIds);
        return $query->get();
    }

    public function getCampaignToAgents($id, $filters = array())
    {
        //get TL's member list
        $resultUsers = User::whereReportingUserId($id)->whereStatus(1)->get();

        $resultAssignedCampaigns = array();
        //get campaigns already assigned to agents
        $result['AGENT'] = CampaignAssignAgent::select('campaign_id')
            ->whereIn('user_id', $resultUsers->pluck('id')->toArray())
            ->whereStatus(1)
            ->get();

        if(!empty($result['AGENT'])) {
            $resultAssignedCampaigns = array_unique (array_merge ($resultAssignedCampaigns, $result['AGENT']->pluck('campaign_id')->toArray()));
        }

        $query = CampaignAssignRATL::query();
        $query->with('campaign');
        $query->whereUserId($id);
        $query->whereIn('campaign_id', $resultAssignedCampaigns);
        return $query->get();
    }

    public function getAssignedCampaignToVendors($id, $filters = array())
    {
        //get Vendor's list
        $resultVendors = Vendor::whereStatus(1)->get();
        $resultAssignedCampaigns = array();
        //get campaigns already assigned to agents
        $result['VENDOR'] = CampaignAssignVendor::select('campaign_id')
            ->whereIn('campaign_assign_vm_id', $resultVendors->pluck('id')->toArray())
            ->whereStatus(1)
            ->get();

        if(!empty($result['VENDOR'])) {
            $resultAssignedCampaigns = array_unique (array_merge ($resultAssignedCampaigns, $result['VENDOR']->pluck('campaign_id')->toArray()));
        }

        $query = CampaignAssignVendorManager::query();
        $query->with('campaign');
        $query->whereUserId($id);
        $query->whereIn('campaign_id', $resultAssignedCampaigns);
        return $query->get();
    }

    public function getCampaignToAssignForTL($id, $filters = array())
    {
        $mainQuery = CampaignAssignRATL::query();
        $mainQuery->whereUserId($id);

        $query = CampaignAssignAgent::query();
        $query->whereIn('campaign_assign_ratl_id', $mainQuery->get()->pluck('id')->toArray());
        $resultCampaignAssigned = $query->get();

        /*
        //get TL's member list
        $resultUsers = User::whereReportingUserId($id)->whereStatus(1)->get();

        $resultAssignedCampaigns = array();
        //get campaigns already assigned to agents
        $result['AGENT'] = CampaignAssignAgent::select('campaign_id')
            ->whereIn('user_id', $resultUsers->pluck('id')->toArray())
            ->whereStatus(1)
            ->get();

        if(!empty($result['AGENT'])) {
            $resultAssignedCampaigns = array_unique (array_merge ($resultAssignedCampaigns, $result['AGENT']->pluck('campaign_id')->toArray()));
        }
        */

        //$query = CampaignAssignRATL::query();
        //$query->with('campaign');

        $mainQuery->whereHas('campaign', function($children) {
            $children->whereIn('campaign_status_id', [1,5,6]);
        });

        $mainQuery->whereUserId($id);
        //query->whereNotIn('campaign_id', $resultAssignedCampaigns);
        $mainQuery->whereNotIn('id', $resultCampaignAssigned->pluck('campaign_assign_ratl_id')->toArray());
        return $mainQuery->get();
    }

    public function getCampaignToAssignForVM($id, $filters = array())
    {
        //get Vendor's member list
        $resultVendors = Vendor::whereStatus(1)->get();

        $resultAssignedCampaigns = array();
        //get campaigns already assigned to agents
        $result['VENDOR'] = CampaignAssignVendor::select('campaign_id')
            ->whereIn('campaign_assign_vm_id', $resultVendors->pluck('id')->toArray())
            ->whereStatus(1)
            ->get();

        if(!empty($result['VENDOR'])) {
            $resultAssignedCampaigns = array_unique (array_merge ($resultAssignedCampaigns, $result['VENDOR']->pluck('campaign_id')->toArray()));
        }

        $query = CampaignAssignVendorManager::query();
        $query->with('campaign');
        $query->whereUserId($id);
        $query->whereNotIn('campaign_id', $resultAssignedCampaigns);
        return $query->get();
    }

    public function getCampaignToAssign($filters = array())
    {
        $resultAssignedCampaigns = array();
        //get campaigns already assigned
        $result['RATL'] = CampaignAssignRATL::select('campaign_id')->whereStatus(1)->get();
        $result['AGENT'] = CampaignAssignAgent::select('campaign_id')->whereStatus(1)->get();
        $result['VM'] = CampaignAssignVendorManager::select('campaign_id')->whereStatus(1)->get();

        if(!empty($result['RATL'])) {
            $resultAssignedCampaigns = array_unique (array_merge ($resultAssignedCampaigns, $result['RATL']->pluck('campaign_id')->toArray()));
        }

        if(!empty($result['AGENT'])) {
            $resultAssignedCampaigns = array_unique (array_merge ($resultAssignedCampaigns, $result['AGENT']->pluck('campaign_id')->toArray()));
        }

        if(!empty($result['VM'])) {
            $resultAssignedCampaigns = array_unique (array_merge ($resultAssignedCampaigns, $result['VM']->pluck('campaign_id')->toArray()));
        }
        $resultCampaignIds = array();
        //get campaign with live incremental
        $resultCampaigns = Campaign::whereNotIn('id', $resultAssignedCampaigns)->get();

        if(!empty($resultCampaigns)) {
            foreach ($resultCampaigns as $campaign) {
                if($campaign->children->count()) {
                    foreach ($campaign->children as $incremental) {
                        if($incremental->campaign_status_id == 1) {
                            $resultCampaignIds[] = $incremental->id;
                        }
                    }
                } else {
                    $resultCampaignIds[] = $campaign->id;
                }
            }
        }

        $query = Campaign::query();
        $query->whereIn('id', $resultCampaignIds);
        return $query->get();
    }

    public function getAssignedRATL($id)
    {
        $query = CampaignAssignRATL::query();
        $query->whereCampaignId($id);

        $query->with('agents');
        $query->with('user');
        $query->with('userAssignedBy');

        return $query->get();
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $resultCampaign = Campaign::findOrFail($attributes['campaign_id']);
            $resultUsers = User::whereIn('id', array_column($attributes['users'], 'user_id'))->get()->pluck('designation_id')->toArray();
            $flag = 0;
            $user_names = '';

            if(count(array_unique($resultUsers)) == 1) {
                foreach ($attributes['users'] as $user) {
                    //Check user type
                    $resultUser = User::findOrFail($user['user_id']);

                    switch ($resultUser->designation->slug) {
                        case 'ra_team_leader' :
                        case 'ra_team_leader_business_delivery' :
                            $resultCARATL = CampaignAssignRATL::where('campaign_id', $attributes['campaign_id'])->where('user_id', $user['user_id'])->where('status', 1)->first();

                            if(isset($resultCARATL) && $resultCARATL->id) {
                                $result['status'] = TRUE;
                                $resultCARATL = CampaignAssignRATL::find($resultCARATL->id);
                                $resultCARATL->status = 1;
                                $resultCARATL->submitted_at = null;
                                $resultCARATL->allocation = $resultCARATL->allocation + max($user['allocation'], 1);
                                $resultCARATL->save();

                            } else {
                                $result = $this->RATLRepository->store(array(
                                    'campaign_id' => $attributes['campaign_id'],
                                    'user_id' => $user['user_id'],
                                    'display_date' => date('Y-m-d', strtotime($attributes['display_date'])),
                                    'allocation' => $user['allocation'] <= 0 ? 1 : $user['allocation'],
                                    'assigned_by' => Auth::id()
                                ));
                            }

                            if($result['status'] == TRUE) {
                                $flag = 1;
                                $user_names .= $resultUser->full_name.', ';
                            } else {
                                throw new \Exception('Something went wrong, please try again.', 1);
                            }
                            break;

                        case 'research_analyst' :
                            //Find RATL entry
                            $resultCARATL = CampaignAssignRATL::where('campaign_id', $attributes['campaign_id'])->where('user_id', $resultUser->reporting_user_id)->first();

                            if(!empty($resultCARATL) && $resultCARATL->id) {
                                $ca_ratl_id = $resultCARATL->id;
                            } else {
                                $result = $this->RATLRepository->store(array(
                                    'campaign_id' => $attributes['campaign_id'],
                                    'user_id' => $resultUser->reporting_user_id,
                                    'display_date' => date('Y-m-d', strtotime($attributes['display_date'])),
                                    //'allocation' => $user['allocation'],
                                    'allocation' => 0,
                                    'started_at' => date('Y-m-d H:i:s'),
                                    'assigned_by' => Auth::id()
                                ));
                                if($result['status'] == TRUE) {
                                    $ca_ratl_id = $result['details']->id;
                                } else {
                                    throw new \Exception('Something went wrong, please try again.', 1);
                                }
                            }

                            $resultCAAgent = CampaignAssignAgent::where('campaign_id', $attributes['campaign_id'])->where('user_id', $user['user_id'])->where('status', 1)->first();

                            if(isset($resultCAAgent) && $resultCAAgent->id) {
                                $result['status'] = TRUE;
                            } else {
                                $result = $this->agentRepository->store(array(
                                    'campaign_id' => $attributes['campaign_id'],
                                    'agent_work_type_id' => $attributes['agent_work_type_id'],
                                    'campaign_assign_ratl_id' => $ca_ratl_id,
                                    'user_id' => $user['user_id'],
                                    'display_date' => date('Y-m-d', strtotime($attributes['display_date'])),
                                    'allocation' => max($user['allocation'], 1),
                                    'reporting_file' => null,
                                    'assigned_by' => Auth::id()
                                ));
                            }
                            if($result['status'] == TRUE) {
                                $resultCARATL = CampaignAssignRATL::find($ca_ratl_id);
                                $resultCARATL->status = 1;
                                $resultCARATL->submitted_at = null;
                                $resultCARATL->allocation = $resultCARATL->allocation + max($user['allocation'], 1);
                                $resultCARATL->save();
                                $flag = 1;
                                $user_names .= $resultUser->full_name.', ';
                            } else {
                                throw new \Exception('Something went wrong, please try again.', 1);
                            }
                            break;

                        case 'sr_vendor_management_specialist' :

                            $resultCAVM = CampaignAssignVendorManager::where('campaign_id', $attributes['campaign_id'])->where('user_id', $user['user_id'])->where('status', 1)->first();

                            if(isset($resultCAVM) && $resultCAVM->id) {
                                $result['status'] = TRUE;
                            } else {
                                $result = $this->vendorManagerRepository->store(array(
                                    'campaign_id' => $attributes['campaign_id'],
                                    'user_id' => $user['user_id'],
                                    'display_date' => date('Y-m-d', strtotime($attributes['display_date'])),
                                    'allocation' => max($user['allocation'], 1),
                                    'assigned_by' => Auth::id()
                                ));
                            }

                            if($result['status'] == TRUE) {
                                $flag = 1;
                                $user_names .= $resultUser->full_name.', ';
                            } else {
                                throw new \Exception('Something went wrong, please try again.', 1);
                            }
                            break;
                    }
                }
            } else {
                return array('status' => FALSE, 'message' => 'Can not assign campaign, user\'s designation mismatch.');
            }

            if($flag) {
                //Add Campaign History
                add_campaign_history($resultCampaign->id, $resultCampaign->parent_id, 'Campaign assigned to - '.$user_names);
                add_history('Campaign assigned', 'Campaign assigned to - '.$user_names);

                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign assigned successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again. [EC-M-CAC-CAR-S-100]');
        }
        return $response;
    }

    public function revokeCampaign($id)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $submitted_at = date('Y-m-d H:i:s');

            $resultCARATL = CampaignAssignRATL::find($id);
            if(empty($resultCARATL)) {
                $resultCAAgent = CampaignAssignAgent::find($id);
                if(empty($resultCAAgent)) {
                    throw new \Exception('Something went wrong, please try again.', 1);
                } else {
                    $resultCAAgent->submitted_at = $submitted_at;
                    $resultCAAgent->status = 2;
                    if($resultCAAgent->save()) {
                        DB::commit();
                        $response = array('status' => TRUE, 'message' => 'Campaign revoke successfully');
                    } else {
                        throw new \Exception('Something went wrong, please try again.', 1);
                    }
                }
            } else {
                $responseCAAgents = CampaignAssignAgent::where('campaign_assign_ratl_id', $resultCARATL->id)->update(array('submitted_at' => $submitted_at, 'status' => 2));

                $resultCARATL->submitted_at = $submitted_at;
                $resultCARATL->status = 2;
                if($resultCARATL->save()) {
                    DB::commit();
                    $response = array('status' => TRUE, 'message' => 'Campaign revoke successfully');
                } else {
                    throw new \Exception('Something went wrong, please try again.', 1);
                }
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function assignCampaign($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            if(0) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign revoke successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }
}
