<?php

namespace App\Repository\Campaign\History;

use App\Models\CampaignHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CampaignHistoryRepository implements CampaignHistoryInterface
{
    /**
     * @var CampaignHistory
     */
    private $campaignHistory;

    public function __construct(CampaignHistory $campaignHistory)
    {
        $this->campaignHistory = $campaignHistory;
    }

    public function get($filters = array())
    {
        $limit = 10;
        $query = CampaignHistory::query();

        if(isset($filters['campaign_ids']) && !empty($filters['campaign_ids'])) {
            $query->whereIn('campaign_id', $filters['campaign_ids']);
        }

        if(isset($filters['order_by']) && !empty($filters['order_by']['column'])) {
            $query->orderBy($filters['order_by']['column'], $filters['order_by']['dir']);
        }

        $query->with('user');


        if(isset($filters['skip']) && !empty($filters['skip'])) {
            $query->limit($limit)->skip($filters['skip'] * $limit);
        } else {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $campaignHistory = new CampaignHistory();

            $campaignHistory->campaign_id = $attributes['campaign_id'];
            $campaignHistory->parent_campaign_id = $attributes['parent_campaign_id'];
            $campaignHistory->user_id = Auth::id();
            $campaignHistory->message = $attributes['message'];

            if(isset($attributes['data']) && !empty($attributes['data'])) {
                $campaignHistory->data = json_encode($attributes['data']);
            } else {
                $campaignHistory->data = '{}';
            }
            $campaignHistory->save();
            if($campaignHistory->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Campaign history added successfully');
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
