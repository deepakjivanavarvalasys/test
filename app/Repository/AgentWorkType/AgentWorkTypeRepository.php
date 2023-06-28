<?php

namespace App\Repository\AgentWorkType;

use App\Models\AgentWorkType;
use Illuminate\Support\Facades\DB;

class AgentWorkTypeRepository implements AgentWorkTypeInterface
{
    /**
     * @var AgentWorkType
     */
    private $agentWorkType;

    public function __construct(AgentWorkType $agentWorkType)
    {
        $this->agentWorkType = $agentWorkType;
    }

    public function get($filters = array())
    {
        $query = AgentWorkType::query();

        if(isset($filters) && !empty($filters)) {
            if(array_key_exists('status', $filters)) {
                $query->whereStatus($filters['status']);
            }
        }

        return $query->get();
    }

    public function find($id)
    {
        $query = AgentWorkType::query();

        return $query->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $agent_work_type = new AgentWorkType();
            $agent_work_type->name = $attributes['name'];
            $agent_work_type->slug = str_replace(' ', '_', trim(strtolower($attributes['name'])));
            $agent_work_type->status = $attributes['status'];
            $agent_work_type->save();
            if($agent_work_type->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Agent work type added successfully');
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
            DB::beginTransaction();
            $agent_work_type = AgentWorkType::findOrFail($id);
            if(isset($attributes['name']) && !empty($attributes['name'])) {
                $agent_work_type->name = $attributes['name'];
            }
            //$agent_work_type->slug = str_replace(' ', '_', trim(strtolower($attributes['name'])));

            if(array_key_exists('status', $attributes)) {
                $agent_work_type->status = $attributes['status'];
            }
            if($agent_work_type->save()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Agent work type updated successfully');
            } else {
                throw new \Exception('Something went wrong, please try again.', 1);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        }
        return $response;
    }

    public function destory($id)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $agent_work_type = $this->find($id);
            if($agent_work_type->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Agent work type deleted successfully');
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
