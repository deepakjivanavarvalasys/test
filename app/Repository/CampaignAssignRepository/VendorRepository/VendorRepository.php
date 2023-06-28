<?php

namespace App\Repository\CampaignAssignRepository\VendorRepository;

use App\Models\Campaign;
use App\Models\CampaignAssignVendor;
use App\Models\CampaignAssignVendorManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorRepository implements VendorInterface
{

    public function get($filters = array())
    {
        $query = CampaignAssignVendor::query();

        if(isset($filters) && !empty($filters)) {
            if(isset($filters['cavm_id']) && !empty($filters['cavm_id'])) {
                $query->where('campaign_assign_vm_id',$filters['cavm_id']);
            }
        }

        $query->with('user');
        $query->with('userAssignedBy');

        return $query->get();
    }

    public function find($id)
    {
        $query = CampaignAssignVendor::query();

        return $query->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();

            $ca_vendor = new CampaignAssignVendor();

            $ca_vendor->campaign_id = $attributes['campaign_id'];
            $ca_vendor->campaign_assign_vm_id = $attributes['campaign_assign_vm_id'];
            $ca_vendor->user_id = $attributes['vendor_id'];
            $ca_vendor->display_date = date('Y-m-d', strtotime($attributes['display_date']));
            $ca_vendor->allocation = $attributes['allocation'];

            if(isset($attributes['started_at']) && !empty($attributes['started_at'])) {
                $ca_vendor->started_at = date('Y-m-d', strtotime($attributes['started_at']));
            } else {
                $ca_vendor->started_at = date('Y-m-d');
            }
            $ca_vendor->assigned_by = $attributes['assigned_by'];

            $ca_vendor->save();
            if($ca_vendor->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign assigned successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }

        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => $exception->getMessage());
        }
        return $response;
    }
    public function update($id, $attributes)
    {
        // TODO: Implement update() method.
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
    }
}
