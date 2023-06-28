<?php

namespace App\Repository\CountryRepository;

use App\Models\Country;
use Illuminate\Support\Facades\DB;

class CountryRepository implements CountryInterface
{
    private $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function get($filters = array())
    {
        $query = Country::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->country->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $country = new Country();
            $country->name = $attributes['name'];
            $country->region_id = $attributes['region_id'];
            $country->status = $attributes['status'];
            $country->save();
            if($country->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Country added successfully');
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
            $country = $this->find($id);
            $country->name = $attributes['name'];
            $country->region_id = $attributes['region_id'];
            $country->status = $attributes['status'];
            $country->update();
            if($country->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Country updated successfully');
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
            $country = $this->find($id);
            if($country->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Country deleted successfully');
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
