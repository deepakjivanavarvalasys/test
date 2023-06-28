<?php

namespace App\Repository\TimeTracker;

use App\Models\TimeTracker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimeTrackerRepository implements TimeTrackerInterface
{
    /**
     * @var TimeTracker
     */
    private $timeTracker;

    public function __construct(
        TimeTracker $timeTracker
    )
    {
        $this->timeTracker = $timeTracker;
    }

    public function get($filters = array())
    {
        $query = TimeTracker::query();

        return $query->get();
    }

    public function find($id)
    {
        $query = TimeTracker::query();

        return $query->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();

            $timeTracker = new TimeTracker();

            if(isset($attributes['user_id']) && !empty($attributes['user_id'])) {
                $timeTracker->user_id = $attributes['user_id'];
            } else {
                $timeTracker->user_id = Auth::id();
            }

            $timeTracker->time_in = date('Y-m-d H:i:s', strtotime($attributes['time_in']));

            if(isset($attributes['time_out']) && !empty($attributes['time_out'])) {
                $timeTracker->time_out = date('Y-m-d H:i:s', strtotime($attributes['time_out']));
            }

            if(isset($attributes['reason']) && !empty($attributes['reason'])) {
                $timeTracker->reason = $attributes['reason'];
            }

            if(isset($attributes['type']) && !empty($attributes['type'])) {
                $timeTracker->type = $attributes['type'];
            }

            $timeTracker->save();

            if($timeTracker->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Time added successfully');
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

            $timeTracker = TimeTracker::findOrFail($id);

            if(isset($attributes['time_in']) && !empty($attributes['time_in'])) {
                $timeTracker->time_in = date('Y-m-d H:i:s', strtotime($attributes['time_in']));
            }

            if(isset($attributes['time_out']) && !empty($attributes['time_out'])) {
                $timeTracker->time_out = date('Y-m-d H:i:s', strtotime($attributes['time_out']));
            }

            if(isset($attributes['reason']) && !empty($attributes['reason'])) {
                $timeTracker->reason = $attributes['reason'];
            }

            if(isset($attributes['type']) && !empty($attributes['type'])) {
                $timeTracker->type = $attributes['type'];
            }

            if($timeTracker->save()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Time updated successfully');
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
