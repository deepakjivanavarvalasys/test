<?php

namespace App\Repository\AgentDataRepository;

use App\Models\AgentData;
use App\Models\CampaignAssignAgent;
use Illuminate\Support\Facades\DB;

class AgentDataRepository implements AgentDataInterface
{
    /**
     * @var AgentData
     */
    private $agentData;

    public function __construct(
        AgentData $agentData
    )
    {
        $this->agentData = $agentData;
    }

    public function get($filters = array())
    {
        $query = AgentData::query();

        if (isset($filters['ca_ratl_ids']) && !empty($filters['ca_ratl_ids'])) {
            $resultCAAgents = CampaignAssignAgent::whereIn('campaign_assign_ratl_id', $filters['ca_ratl_ids'])->get();
            if($resultCAAgents->count()) {
                $query->whereIn('ca_agent_id', $resultCAAgents->pluck('id')->toArray());
            }
        }

        if (isset($filters['ca_agent_ids']) && !empty($filters['ca_agent_ids'])) {
            $query->whereIn('ca_agent_id', $filters['ca_agent_ids']);
        }

        return $query->get();
    }

    public function find($id)
    {
        $query = AgentData::query();

        return $query->findOrFail($id);
    }

    public function store($attributes)
    {
        // TODO: Implement store() method.
    }

    public function assignData($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();

            if(!empty($attributes['data_ids'])) {
                $data = explode(',', $attributes['data_ids']);
                $resultCAAgents = CampaignAssignAgent::where('campaign_assign_ratl_id', base64_decode($attributes['ca_ratl_id']))->get();

                $chunkSize = ceil(count($data) / $resultCAAgents->count());

                $chunkedData = array_chunk($data, $chunkSize);

                $agentData = array();
                foreach ($resultCAAgents as $key => $CAAgent) {
                    if (isset($chunkedData[$key]) && !empty($chunkedData[$key])) {
                        foreach ($chunkedData[$key] as $data_id) {
                            $agentData[] = array(
                                'ca_agent_id' => $CAAgent->id,
                                'data_id' => $data_id
                            );
                        }
                    }
                }

                if(count($agentData) && DB::table('agent_data')->insert($agentData)) {
                    DB::commit();
                    $response = array('status' => TRUE, 'message' => 'Data assigned successfully');
                } else {
                    throw new \Exception('Something went wrong, please try again.', 1);
                }
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

            $agentData = $this->find($id);

            if(isset($attributes['ca_agent_id']) && !empty($attributes['ca_agent_id'])) {
                $agentData->ca_agent_id = trim($attributes['ca_agent_id']);
            }
            if(isset($attributes['data_id']) && !empty($attributes['data_id'])) {
                $agentData->data_id = trim($attributes['data_id']);
            }
            if(isset($attributes['status']) && !empty($attributes['status'])) {
                $agentData->status = trim($attributes['status']);
            }

            if($agentData->update()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Agent\'s data updated successfully');
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
