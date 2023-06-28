<?php

namespace App\Repository\RoleRepository;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleRepository implements RoleInterface
{
    private $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function get($filters = array())
    {
        $query = Role::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->role->findOrFail($id);
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $role = new Role();
            $role->name = $attributes['name'];
            $role->slug = str_replace(' ', '_', trim(strtolower($attributes['name'])));
            $role->status = $attributes['status'];
            $role->save();
            if($role->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Role added successfully');
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
            $role = $this->find($id);
            $role->name = $attributes['name'];
            $role->status = $attributes['status'];
            $role->update();
            if($role->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Role updated successfully');
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
            $role = $this->find($id);
            if($role->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Role deleted successfully');
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
