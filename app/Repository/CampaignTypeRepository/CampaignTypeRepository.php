<?php

namespace App\Repository\CampaignTypeRepository;

use App\Models\CampaignType;
use Illuminate\Support\Facades\DB;

class CampaignTypeRepository implements CampaignTypeInterface
{
    private $campaign_type;

    public function __construct(CampaignType $campaign_type)
    {
        $this->campaign_type = $campaign_type;
    }

    public function get($filter = array())
    {
        $query = CampaignType::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->campaign_type->findOrFail($id);
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $campaign_type = new CampaignType();
            $campaign_type->name = $attributes['name'];
            $campaign_type->full_name = $attributes['full_name'];
            $campaign_type->status = $attributes['status'];
            $campaign_type->save();
            if($campaign_type->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign type added successfully');
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
            $campaign_type = $this->find($id);
            $campaign_type->name = $attributes['name'];
            $campaign_type->full_name = $attributes['full_name'];
            $campaign_type->status = $attributes['status'];
            $campaign_type->update();
            if($campaign_type->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign type updated successfully');
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
            $campaign_type = $this->find($id);
            if($campaign_type->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign type deleted successfully');
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
