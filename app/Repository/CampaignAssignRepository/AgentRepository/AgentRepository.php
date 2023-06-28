<?php

namespace App\Repository\CampaignAssignRepository\AgentRepository;

use App\Models\Campaign;
use App\Models\CampaignAssignAgent;
use App\Models\CampaignAssignRATL;
use App\Models\CampaignAssignVendorManager;
use App\Models\CampaignDeliveryDetail;
use App\Models\User;
use App\Repository\Notification\EME\EMENotificationRepository;
use App\Repository\Notification\RA\RANotificationRepository;
use App\Repository\Notification\VM\VMNotificationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentRepository implements AgentInterface
{
    /**
     * @var RANotificationRepository
     */
    private $RANotificationRepository;
    /**
     * @var EMENotificationRepository
     */
    private $EMENotificationRepository;

    public function __construct(
        RANotificationRepository $RANotificationRepository,
        EMENotificationRepository $EMENotificationRepository
    )
    {
        $this->RANotificationRepository = $RANotificationRepository;
        $this->EMENotificationRepository = $EMENotificationRepository;
    }

    public function get($filters = array())
    {
        $query = CampaignAssignAgent::query();

        if(isset($filters['caratl_id']) && !empty($filters['caratl_id'])) {
            $query->where('campaign_assign_ratl_id', $filters['caratl_id']);
        }

        $query->with('user');
        $query->with('userAssignedBy');

        return $query->get();
    }

    public function find($id)
    {
        $query = CampaignAssignAgent::query();
        $query->with('caratl');
        return $query->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();

            $resultCAAgent = CampaignAssignAgent::where('campaign_id', $attributes['campaign_id'])
                ->where('user_id', $attributes['user_id'])->orderBy('created_at', 'desc')->first();

            if(empty($resultCAAgent->id)) {
                $resultUser = User::findOrFail($attributes['user_id']);
                $resultCampaign = Campaign::findOrFail($attributes['campaign_id']);

                $ca_agent = new CampaignAssignAgent();

                $ca_agent->campaign_assign_ratl_id = $attributes['campaign_assign_ratl_id'];
                $ca_agent->campaign_id = $attributes['campaign_id'];

                $ca_agent->agent_work_type_id = $attributes['agent_work_type_id'];

                $ca_agent->user_id = $attributes['user_id'];
                $ca_agent->accounts_utilized = 0;
                $ca_agent->display_date = $attributes['display_date'];
                $ca_agent->allocation = $attributes['allocation'] <= 0 ? 1 : $attributes['allocation'];

                //Save reporting file to storage
                $filename = null;
                if(isset($attributes['reporting_file']) && !empty($attributes['reporting_file'])) {
                    $path = 'public/campaigns/'.$resultCampaign->campaign_id.'/reporting_file';
                    $file = $attributes['reporting_file'];
                    $extension = $file->getClientOriginalExtension();
                    $filenameOriginal  = $file->getClientOriginalName();
                    $filename  = $attributes['campaign_assign_ratl_id'].'-' . $filenameOriginal . '.' . $extension;
                    $resultFile  = $file->storeAs($path, $filename);
                    $ca_agent->reporting_file = $filename;
                }

                if(isset($attributes['started_at']) && !empty($attributes['started_at'])) {
                    $ca_agent->started_at = date('Y-m-d H:i:s', strtotime($attributes['started_at']));
                }
                if(isset($attributes['submitted_at']) && !empty($attributes['submitted_at'])) {
                    $ca_agent->submitted_at = date('Y-m-d H:i:s', strtotime($attributes['submitted_at']));
                }
                $ca_agent->assigned_by = $attributes['assigned_by'];
                if(array_key_exists('status', $attributes)) {
                    $ca_agent->status = $attributes['status'];
                }

                $ca_agent->save();
                if($ca_agent->id) {
                    CampaignDeliveryDetail::where('campaign_id', $attributes['campaign_id'])->update(array('campaign_progress' => 'Agents Working', 'updated_by' => Auth::id()));
                    $notificationURL = '';
                    switch ($resultUser->designation->slug) {
                        case 'research_analyst':
                            $notificationURL = route('agent.campaign.show', base64_encode($ca_agent->id));
                            break;
                        case 'email_marketing_executive':
                            $notificationURL = route('email_marketing_executive.campaign.show', base64_encode($ca_agent->id));
                            break;
                        case 'sr_vendor_management_specialist':
                            $notificationURL = route('vendor_manager.ra.campaign.show', base64_encode($ca_agent->id));
                            break;
                    }
                    //Send Notification
                    switch ($resultUser->designation->slug) {
                        case 'research_analyst':
                            $this->RANotificationRepository->store(array(
                                'sender_id' => $attributes['assigned_by'],
                                'recipient_id' => $attributes['user_id'],
                                'message' => 'New campaign assigned - '.$resultCampaign->name,
                                'url' => $notificationURL
                            ));
                            break;
                        case 'email_marketing_executive':
                            $this->EMENotificationRepository->store(array(
                                'sender_id' => $attributes['assigned_by'],
                                'recipient_id' => $attributes['user_id'],
                                'message' => 'New campaign assigned - '.$resultCampaign->name,
                                'url' => $notificationURL
                            ));
                            break;
                        case 'sr_vendor_management_specialist':
                            VMNotificationRepository::store(array(
                                'sender_id' => $attributes['assigned_by'],
                                'recipient_id' => $attributes['user_id'],
                                'message' => 'New campaign(RA) assigned - '.$resultCampaign->name,
                                'url' => $notificationURL
                            ));
                            break;
                    }

                    DB::commit();
                    $response = array('status' => TRUE, 'message' => 'Campaign assigned successfully', 'details' => $ca_agent);
                } else {
                    throw new \Exception('Something went wrong, please try again.', 1);
                }

            } else {
                throw new \Exception('Campaign already assigned, please try different user.', 1);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => $exception->getMessage());
        }
        return $response;
    }

    public function update($id, $attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            //dd($attributes);
            DB::beginTransaction();

            $campaign_assign_agent = CampaignAssignAgent::findOrFail($id);
            $resultCampaign = Campaign::findOrFail($campaign_assign_agent->campaign_id);

            if(isset($attributes['campaign_assign_ratl_id']) && $attributes['campaign_assign_ratl_id']) {
                $campaign_assign_agent->campaign_assign_ratl_id = $attributes['campaign_assign_ratl_id'];
            }

            if(isset($attributes['agent_work_type_id']) && $attributes['agent_work_type_id']) {
                $campaign_assign_agent->agent_work_type_id = $attributes['agent_work_type_id'];
            }

            if(isset($attributes['campaign_id']) && $attributes['campaign_id']) {
                $campaign_assign_agent->campaign_id = $attributes['campaign_id'];
            }

            if(isset($attributes['user_id']) && $attributes['user_id']) {
                $campaign_assign_agent->user_id = $attributes['user_id'];
            }

            if(array_key_exists('accounts_utilized', $attributes) ) {
                $campaign_assign_agent->accounts_utilized = $attributes['accounts_utilized'];
            }

            if(isset($attributes['display_date']) && $attributes['display_date']) {
                $campaign_assign_agent->display_date = $attributes['display_date'];
            }
            if(isset($attributes['allocation']) && $attributes['allocation']) {
                $campaign_assign_agent->allocation = $attributes['allocation'];
            }

            if(isset($attributes['reporting_file']) && $attributes['reporting_file']) {
                //Save reporting file to storage
                $filename = null;
                if(!empty($campaign['reporting_file'])) {
                    $path = 'public/campaigns/'.$resultCampaign->campaign_id.'/reporting_file';
                    $file = $attributes['reporting_file'];
                    $extension = $file->getClientOriginalExtension();
                    $filenameOriginal  = $file->getClientOriginalName();
                    $filename  = $campaign_assign_agent->campaign_assign_ratl_id.'-' . $filenameOriginal . '.' . $extension;
                    $resultFile  = $file->storeAs($path, $filename);
                }
                $campaign_assign_agent->reporting_file = $filename;
            }

            if(isset($attributes['started_at']) && $attributes['started_at']) {
                $campaign_assign_agent->started_at = $attributes['started_at'];
            }

            if(array_key_exists('submitted_at', $attributes)) {
                $campaign_assign_agent->submitted_at = $attributes['submitted_at'];
            }

            if(isset($attributes['assigned_by']) && $attributes['assigned_by']) {
                $campaign_assign_agent->assigned_by = $attributes['assigned_by'];
            }

            if(array_key_exists('status', $attributes)) {
                $campaign_assign_agent->status = $attributes['status'];
            }

            if($campaign_assign_agent->save()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Details updated successfully', 'details' => $campaign_assign_agent);
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
    }
}
