<?php

namespace App\Repository\CampaignAssignRepository\QATLRepository;

use App\Models\Campaign;
use App\Models\CampaignAssignQATL;
use App\Models\CampaignDeliveryDetail;
use App\Models\User;
use App\Repository\Notification\QATL\QATLNotificationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QATLRepository implements QATLInterface
{

    public function get($filters = array())
    {
        $query = CampaignAssignQATL::query();

        if(array_key_exists('status', $filters)) {
            $query->where('status', $filters['status']);
        }

        if(array_key_exists('started_at', $filters)) {
            $query->where('started_at', $filters['started_at']);
        }


        return $query->get();
    }

    public function find($id)
    {
        $query = CampaignAssignQATL::query();

        return $query->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            //dd($attributes);
            DB::beginTransaction();

            $ca_qatl = new CampaignAssignQATL();
            $ca_qatl->campaign_id = $attributes['campaign_id'];
            $ca_qatl->user_id = $attributes['user_id'];
            $ca_qatl->display_date = $attributes['display_date'];
            if(isset($attributes['started_at']) && !empty($attributes['started_at'])) {
                $ca_qatl->started_at = date('Y-m-d', strtotime($attributes['started_at']));
            }
            if(isset($attributes['submitted_at']) && !empty($attributes['submitted_at'])) {
                $ca_qatl->submitted_at = date('Y-m-d', strtotime($attributes['submitted_at']));
            }
            $ca_qatl->assigned_by = $attributes['assigned_by'];

            if(isset($attributes['status']) && !empty($attributes['status'])) {
                $ca_qatl->status = $attributes['status'];
            }
            $ca_qatl->save();
            if($ca_qatl->id) {
                //Add Campaign History
                $resultCampaign = Campaign::findOrFail($ca_qatl->campaign_id);
                $resultUser = User::findOrFail($ca_qatl->user_id);
                add_campaign_history($resultCampaign->id, $resultCampaign->parent_id, 'Campaign assigned to QATL -'.$resultUser->full_name);
                add_history('Campaign assigned to QATL', 'Campaign assigned to QATL -'.$resultUser->full_name);

                CampaignDeliveryDetail::where('campaign_id', $ca_qatl->campaign_id)->update(array('campaign_progress' => 'In Quality Check', 'updated_by' => Auth::id()));

                //Send notification to QATL
                QATLNotificationRepository::store(array(
                    'sender_id' => $attributes['assigned_by'],
                    'recipient_id' => $attributes['user_id'],
                    'message' => 'New campaign assigned - '.$resultCampaign->name,
                    'url' => route('qa_team_leader.campaign.show', base64_encode($ca_qatl->id))
                ));

                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign sent to quality team, successfully');
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

            $ca_qatl = CampaignAssignQATL::findOrFail($id);
            $resultCampaign = Campaign::findOrFail($ca_qatl->campaign_id);

            if(isset($attributes['ca_ratl_id']) && $attributes['ca_ratl_id']) {
                $ca_qatl->ca_ratl_id = $attributes['ca_ratl_id'];
            }

            if(isset($attributes['campaign_id']) && $attributes['campaign_id']) {
                $ca_qatl->campaign_id = $attributes['campaign_id'];
            }

            if(isset($attributes['user_id']) && $attributes['user_id']) {
                $ca_qatl->user_id = $attributes['user_id'];
            }

            if(isset($attributes['display_date']) && $attributes['display_date']) {
                $ca_qatl->display_date = $attributes['display_date'];
            }
            if(isset($attributes['allocation']) && $attributes['allocation']) {
                $ca_qatl->allocation = $attributes['allocation'];
            }

            if(isset($attributes['started_at']) && $attributes['started_at']) {
                $ca_qatl->started_at = date('Y-m-d H:i:s', strtotime($attributes['started_at']));
            }

            if(array_key_exists('submitted_at', $attributes)) {
                $ca_qatl->submitted_at = date('Y-m-d H:i:s', strtotime($attributes['submitted_at']));
            }

            if(isset($attributes['delivery_file']) && !empty($attributes['delivery_file'])) {
                $file = $attributes['delivery_file'];
                $resultCampaign = Campaign::findOrFail($ca_qatl->campaign_id);
                $path = 'public/campaigns/'.$resultCampaign->campaign_id.'/quality/delivery';
                $extension = $file->getClientOriginalExtension();
                $filename  = $file->getClientOriginalName();
                if($file->storeAs($path, $filename)) {
                    $ca_qatl->file_name = $filename;
                } else {
                    throw new \Exception('Please check file and try again.', 1);
                }
            }

            if(isset($attributes['assigned_by']) && $attributes['assigned_by']) {
                $ca_qatl->assigned_by = $attributes['assigned_by'];
            }

            if(array_key_exists('status', $attributes)) {
                $ca_qatl->status = $attributes['status'];
            }

            if($ca_qatl->save()) {

                //Send notification to QATL
                QATLNotificationRepository::store(array(
                    'sender_id' => $ca_qatl->assigned_by,
                    'recipient_id' => $ca_qatl->user_id,
                    'message' => 'Campaign/Lead details updated - '.$resultCampaign->name,
                    'url' => route('qa_team_leader.campaign.show', base64_encode($resultCampaign->id))
                ));

                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Details updated successfully', 'details' => $ca_qatl);
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
