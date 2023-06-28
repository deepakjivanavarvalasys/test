<?php

namespace App\Repository\PacingDetailRepository;

use App\Models\Campaign;
use App\Models\PacingDetail;
use Illuminate\Support\Facades\DB;

class PacingDetailRepository implements PacingDetailInterface
{
    private $pacingDetail;

    public function __construct(PacingDetail $pacingDetail)
    {
        $this->pacingDetail = $pacingDetail;
    }

    public function get($campaign_id = null, $filters = array())
    {
        $query = PacingDetail::query();

        if(isset($campaign_id) && !empty($campaign_id)) {
            $query->where('campaign_id', $campaign_id);
        }

        if(isset($filters['month']) && !empty($filters['month'])) {
            $query->whereMonth('date', $filters['month']);
        }

        if(isset($filters['year']) && !empty($filters['year'])) {
            $query->whereYear('date', $filters['year']);
        }

        $query->orderBy('date');

        return $query->get();
    }

    public function find($id)
    {
        // TODO: Implement find() method.
    }

    public function store($campaign_id, $attributes)
    {
        // TODO: Implement store() method.
    }

    public function update($campaign_id, $attributes)
    {
        //dd($attributes);
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();

            $campaign = Campaign::findOrFail($campaign_id);

            //Save Pacing Details/Sub-Allocation
            PacingDetail::whereCampaignId($campaign->id)->delete();

            $insertPacingDetails = array();
            if(isset($attributes['sub-allocation']) && !empty($attributes['sub-allocation'])) {
                $historyMessage = '';
                foreach ($attributes['sub-allocation'] as $date => $sub_allocation) {
                    $insertPacingDetails[] = [
                        'campaign_id' => $campaign->id,
                        'date' => $date,
                        'sub_allocation' => $sub_allocation,
                        'day' => date('w', strtotime($date))
                    ];
                    $historyMessage = $historyMessage.'<br><b>'.date('d-M-Y', strtotime($date)).' : '.'</b>'.$sub_allocation;
                }

                if(PacingDetail::insert($insertPacingDetails)) {
                    DB::commit();
                    $response = array('status' => TRUE, 'message' => 'Sub allocations updated successfully');

                    //Save History
                    if(!empty($historyMessage)) {
                        add_campaign_history($campaign->id, $campaign->parent_id, 'Campaign sub-allocation updated - '.$historyMessage, array('newData' => $insertPacingDetails));
                        add_history('Campaign details updated', 'Campaign sub-allocation updated - '.$historyMessage, array('newData' => $insertPacingDetails));
                    }
                } else {
                    throw new \Exception('Something went wrong, please try again.', 1);
                }
            } else {
                throw new \Exception('Please enter valid sub-allocations', 1);
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
