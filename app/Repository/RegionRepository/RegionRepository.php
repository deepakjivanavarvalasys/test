<?php

namespace App\Repository\RegionRepository;

use App\Models\Region;
use Illuminate\Support\Facades\DB;

class RegionRepository implements RegionInterface
{
    private $region;

    public function __construct(Region $region)
    {
        $this->region = $region;
    }

    public function get($filters = array())
    {
        $query = Region::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->region->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $region = new Region();
            $region->name = $attributes['name'];
            $region->abbreviation = $attributes['abbreviation'];
            $region->status = $attributes['status'];
            $region->save();
            if($region->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Region added successfully');
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
            $region = $this->find($id);
            $region->name = $attributes['name'];
            $region->abbreviation = $attributes['abbreviation'];
            $region->status = $attributes['status'];
            $region->update();
            if($region->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Region updated successfully');
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
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $region = $this->find($id);
            if($region->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Region deleted successfully');
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
