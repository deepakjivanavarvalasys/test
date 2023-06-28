<?php

namespace App\Repository\CampaignStatusRepository;

use App\Models\CampaignStatus;
use Illuminate\Support\Facades\DB;

class CampaignStatusRepository implements CampaignStatusInterface
{
    private $campaignStatus;

    public function __construct(CampaignStatus $campaignStatus)
    {
        $this->campaignStatus = $campaignStatus;
    }

    public function get($filters = array())
    {
        $query = CampaignStatus::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->campaignStatus->findOrFail($id);
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $campaign_status = new CampaignStatus();
            $campaign_status->name = $attributes['name'];
            $campaign_status->status = $attributes['status'];
            $campaign_status->save();
            if($campaign_status->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign status added successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function update($id, $attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $campaign_status = $this->find($id);
            $campaign_status->name = $attributes['name'];
            $campaign_status->status = $attributes['status'];
            $campaign_status->update();
            if($campaign_status->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign status updated successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function destroy($id): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $campaign_status = $this->find($id);
            if($campaign_status->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign status deleted successfully');
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
