<?php

namespace App\Repository\HolidayRepository;

use App\Models\Holiday;
use Illuminate\Support\Facades\DB;

class HolidayRepository implements HolidayInterface
{
    private $holiday;

    public function __construct(Holiday $holiday)
    {
        $this->holiday = $holiday;
    }

    public function get($filters = array())
    {
        $query = Holiday::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->holiday->findOrFail($id);
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $holiday = new Holiday();
            $holiday->title = $attributes['title'];
            $holiday->date = date('Y-m-d', strtotime($attributes['date']));
            $holiday->day = date('w', strtotime($attributes['date']));
            $holiday->status = $attributes['status'];
            $holiday->save();
            if($holiday->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Holiday added successfully');
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
            $holiday = $this->find($id);
            $holiday->title = $attributes['title'];
            $holiday->date = date('Y-m-d', strtotime($attributes['date']));
            $holiday->day = date('w', strtotime($attributes['date']));
            $holiday->status = $attributes['status'];
            $holiday->update();
            if($holiday->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Holiday updated successfully');
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
            $holiday = $this->find($id);
            if($holiday->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Holiday deleted successfully');
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
