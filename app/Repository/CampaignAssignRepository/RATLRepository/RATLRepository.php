<?php

namespace App\Repository\CampaignAssignRepository\RATLRepository;

use App\Models\Campaign;
use App\Models\CampaignAssignRATL;
use App\Repository\Notification\RATL\RATLNotificationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RATLRepository implements RATLInterface
{
    /**
     * @var RATLNotificationRepository
     */
    private $RATLNotificationRepository;

    public function __construct(
        RATLNotificationRepository $RATLNotificationRepository
    )
    {
        $this->RATLNotificationRepository = $RATLNotificationRepository;
    }

    public function get($filters = array())
    {
        $query = CampaignAssignRATL::query();

        if(isset($filters['user_id']) && !empty($filters['user_id'])) {
            $query->whereUserId($filters['user_id']);
        }

        if(isset($filters['user_ids']) && !empty($filters['user_ids'])) {
            $query->whereIn('user_id', $filters['user_ids']);
        }

        return $query->get();
    }

    public function getAssignedAgentsByTL()
    {

    }

    public function find($id)
    {
        $query = CampaignAssignRATL::query();
        $query->with('campaign');
        $query->with('agents');
        return $query->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $resultCampaign = Campaign::findOrFail($attributes['campaign_id']);
            $ca_ratl = new CampaignAssignRATL();

            $ca_ratl->campaign_id = $attributes['campaign_id'];
            $ca_ratl->user_id = $attributes['user_id'];
            $ca_ratl->display_date = $attributes['display_date'];

            $ca_ratl->allocation = $attributes['allocation'];

            if(isset($attributes['started_at']) && !empty($attributes['started_at'])) {
                $ca_ratl->started_at = date('Y-m-d H:i:s', strtotime($attributes['started_at']));
            }

            if(array_key_exists('submitted_at', $attributes)) {
                $ca_ratl->submitted_at = date('Y-m-d H:i:s', strtotime($attributes['submitted_at']));
            }

            $ca_ratl->assigned_by = $attributes['assigned_by'];

            if(array_key_exists('status', $attributes)) {
                $ca_ratl->status = $attributes['status'];
            }
            $ca_ratl->save();

            if($ca_ratl->id) {
                //Send Notification
                $this->RATLNotificationRepository->store(array(
                    'sender_id' => $attributes['assigned_by'],
                    'recipient_id' => $attributes['user_id'],
                    'message' => 'New campaign assigned - '.$resultCampaign->name,
                    'url' => route('team_leader.campaign.show', base64_encode($ca_ratl->id))
                ));
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign assigned successfully', 'details' => $ca_ratl );
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function update($id, $attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            //dd($attributes);
            DB::beginTransaction();

            $ca_ratl = CampaignAssignRATL::findOrFail($id);
            $resultCampaign = Campaign::findOrFail($ca_ratl->campaign_id);

            if(isset($attributes['campaign_id']) && $attributes['campaign_id']) {
                $ca_ratl->campaign_id = $attributes['campaign_id'];
            }

            if(isset($attributes['user_id']) && $attributes['user_id']) {
                $ca_ratl->user_id = $attributes['user_id'];
            }

            if(isset($attributes['display_date']) && $attributes['display_date']) {
                $ca_ratl->display_date = $attributes['display_date'];
            }
            if(isset($attributes['allocation']) && $attributes['allocation']) {
                $ca_ratl->allocation = $attributes['allocation'];
            }

            if(isset($attributes['started_at']) && $attributes['started_at']) {
                $ca_ratl->started_at = date('Y-m-d H:i:s', strtotime($attributes['started_at']));
            }
            if(array_key_exists('submitted_at', $attributes)) {
                $ca_ratl->submitted_at = $attributes['submitted_at'];
            }

            if(isset($attributes['assigned_by']) && $attributes['assigned_by']) {
                $ca_ratl->assigned_by = $attributes['assigned_by'];
            }

            if(array_key_exists('status', $attributes)) {
                $ca_ratl->status = $attributes['status'];
            }

            if($ca_ratl->save()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Details updated successfully', 'details' => $ca_ratl);
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
    }
}
