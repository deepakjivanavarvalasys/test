<?php

namespace App\Repository\Tutorial;

use App\Models\Tutorial;
use Illuminate\Support\Facades\DB;

class TutorialRepository implements TutorialInterface
{
    /**
     * @var Tutorial
     */
    private $tutorial;

    public function __construct(
        Tutorial $tutorial
    )
    {
        $this->tutorial = $tutorial;
    }

    public function get($filters = array())
    {
        $query = Tutorial::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        if(isset($filters['role_id']) && !empty($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->tutorial->findOrFail($id);
    }

    public function store($attributes)
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $tutorial = new Tutorial();
            $tutorial->role_id = $attributes['role_id'];
            $tutorial->title = $attributes['title'];

            if(isset($attributes['description']) && $attributes['description']) {
                $tutorial->description = $attributes['description'];
            }

            if(isset($attributes['link']) && $attributes['link']) {
                $tutorial->link = $attributes['link'];
            }

            $tutorial->save();
            if($tutorial->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Tutorial link added successfully');
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
            $tutorial = $this->find($id);

            if(isset($attributes['role_id']) && !empty($attributes['role_id'])) {
                $tutorial->role_id = $attributes['role_id'];
            }

            if(isset($attributes['title']) && !empty($attributes['title'])) {
                $tutorial->title = $attributes['title'];
            }

            if(array_key_exists('description', $attributes)) {
                $tutorial->description = $attributes['description'];
            }

            if(array_key_exists('link', $attributes)) {
                $tutorial->link = $attributes['link'];
            }

            if(array_key_exists('status', $attributes)) {
                $tutorial->status = $attributes['status'];
            }

            $tutorial->save();
            if($tutorial->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Tutorial link added successfully');
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
            $tutorial = $this->find($id);
            if($tutorial->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Tutorial link deleted successfully');
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
