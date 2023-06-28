<?php

namespace App\Repository\DepartmentRepository;

use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentRepository implements DepartmentInterface
{
    private $department;

    public function __construct(Department $department)
    {
        $this->department = $department;
    }

    public function get($filters = array())
    {
        $query = Department::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->department->findOrFail($id);
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $department = new Department();
            $department->name = $attributes['name'];
            $department->slug = str_replace(' ', '_', trim(strtolower($attributes['name'])));
            $department->status = $attributes['status'];
            $department->save();
            if($department->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Department added successfully');
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
            $department = $this->find($id);
            $department->name = $attributes['name'];
            $department->status = $attributes['status'];
            $department->update();
            if($department->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Department updated successfully');
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
            $department = $this->find($id);
            if($department->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Department deleted successfully');
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
