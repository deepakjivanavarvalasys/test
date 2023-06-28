<?php

namespace App\Repository\DesignationRepository;

use App\Models\Designation;
use Illuminate\Support\Facades\DB;

class DesignationRepository implements DesignationInterface
{
    private $designation;

    public function __construct(Designation $designation)
    {
        $this->designation = $designation;
    }

    public function get($filters = array())
    {
        $query = Designation::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->designation->findOrFail($id);
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $designation = new Designation();
            $designation->name = $attributes['name'];
            $designation->slug = str_replace(' ', '_', trim(strtolower($attributes['name'])));
            $designation->status = $attributes['status'];
            $designation->save();
            if($designation->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Designation added successfully');
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
            $designation = $this->find($id);
            $designation->name = $attributes['name'];
            $designation->status = $attributes['status'];
            $designation->update();
            if($designation->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Designation updated successfully');
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
            $designation = $this->find($id);
            if($designation->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Designation deleted successfully');
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
