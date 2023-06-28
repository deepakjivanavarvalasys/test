<?php

namespace App\Repository\CampaignFilterRepository;

use App\Models\CampaignFilter;
use Illuminate\Support\Facades\DB;

class CampaignFilterRepository implements CampaignFilterInterface
{
    private $campaign_filter;

    public function __construct(CampaignFilter $campaign_filter)
    {
        $this->campaign_filter = $campaign_filter;
    }

    public function get($filter = array())
    {
        $query = CampaignFilter::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->campaign_filter->findOrFail($id);
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $campaign_filter = new CampaignFilter();
            $campaign_filter->name = $attributes['name'];
            $campaign_filter->full_name = $attributes['full_name'];
            $campaign_filter->status = $attributes['status'];
            $campaign_filter->save();
            if($campaign_filter->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign filter added successfully');
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
            $campaign_filter = $this->find($id);
            $campaign_filter->name = $attributes['name'];
            $campaign_filter->full_name = $attributes['full_name'];
            $campaign_filter->status = $attributes['status'];
            $campaign_filter->update();
            if($campaign_filter->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign filter updated successfully');
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
            $campaign_filter = $this->find($id);
            if($campaign_filter->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign filter deleted successfully');
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
