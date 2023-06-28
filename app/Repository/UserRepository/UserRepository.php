<?php

namespace App\Repository\UserRepository;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserInterface
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function get($filters = array())
    {
        $query = User::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        if(isset($filters['designation_slug']) && !empty($filters['designation_slug'])) {
            $query->whereHas('designation', function ($designation) use ($filters){
                $designation->whereIn('slug', $filters['designation_slug']);
            });
        }

        if(isset($filters['role_slug']) && !empty($filters['role_slug'])) {
            $query->whereHas('role', function ($role) use ($filters){
                $role->whereIn('slug', $filters['role_slug']);
            });
        }

        if(isset($filters['reporting_to']) && !empty($filters['reporting_to'])) {
            $query->whereIn('reporting_user_id', $filters['reporting_to']);
        }

        $query->with(['role', 'department', 'designation']);

        if(isset($filters['order_by']) && !empty($filters['order_by'])) {
            $query->orderBy($filters['order_by']['value'], $filters['order_by']['order']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->user->with(['role', 'department', 'designation'])->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $user = new User();

            $user->role_id = $attributes['role_id'];
            $user->department_id = $attributes['department_id'];
            $user->designation_id = $attributes['designation_id'];
            $user->employee_code = $attributes['employee_code'];
            $user->first_name = $attributes['first_name'];
            $user->middle_name = $attributes['middle_name'];
            $user->last_name = $attributes['last_name'];
            $user->email = $attributes['email'];
            if(isset($attributes['password']) && !empty($attributes['password'])) {
                $user->password = $attributes['password'];
            } else {
                $user->password = Hash::make('Valasys@#'.date('Y'));
            }
            $user->status = $attributes['status'];
            $user->reporting_user_id = $attributes['reporting_user_id'];

            $user->save();
            if($user->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'User added successfully');
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
            $user = $this->find($id);

            if(isset($attributes['role_id']) && !empty($attributes['role_id'])) {
                $user->role_id = $attributes['role_id'];
            }
            if(isset($attributes['department_id']) && !empty($attributes['department_id'])) {
                $user->department_id = $attributes['department_id'];
            }
            if(isset($attributes['designation_id']) && !empty($attributes['designation_id'])) {
                $user->designation_id = $attributes['designation_id'];
            }
            if(isset($attributes['employee_code']) && !empty($attributes['employee_code'])) {
                $user->employee_code = $attributes['employee_code'];
            }
            if(isset($attributes['first_name']) && !empty($attributes['first_name'])) {
                $user->first_name = $attributes['first_name'];
            }
            if(isset($attributes['middle_name']) && !empty($attributes['middle_name'])) {
                $user->middle_name = $attributes['middle_name'];
            }
            if(isset($attributes['last_name']) && !empty($attributes['last_name'])) {
                $user->last_name = $attributes['last_name'];
            }
            if(isset($attributes['email']) && !empty($attributes['email'])) {
                $user->email = $attributes['email'];
            }
            if(isset($attributes['password']) && !empty($attributes['password'])) {
                $user->password = Hash::make($attributes['password']);
            }
            if(isset($attributes['status'])) {
                $user->status = $attributes['status'];
            }
            if(isset($attributes['reporting_user_id']) && !empty($attributes['reporting_user_id'])) {
                $user->reporting_user_id = $attributes['reporting_user_id'];
            }

            if(isset($attributes['profile']) && !empty($attributes['profile'])) {
                $file = $attributes['profile'];
                $path = 'public/user/'.$user->employee_code.'/profile';
                $extension = $file->getClientOriginalExtension();
                $filename  = $file->getClientOriginalName();
                if($file->storeAs($path, $filename)) {
                    $user->profile = $filename;
                } else {
                    throw new \Exception('Please check file and try again.', 1);
                }
            }

            if($user->save()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'User details updated successfully');
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
            $user = $this->find($id);
            if($user->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'User deleted successfully');
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
