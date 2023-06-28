<?php

namespace App\Repository\VendorRepository;

use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorRepository implements VendorInterface
{
    private $vendor;

    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
    }

    public function get($filters = array())
    {
        $query = Vendor::query();

        if(isset($filters['status'])) {
            $query->whereStatus($filters['status']);
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->vendor->findOrFail($id);
    }

    public function store($attributes): array
    {
        $response = array('status' => FALSE, 'message' => 'Something went wrong, please try again.');
        try {
            DB::beginTransaction();
            $vendor = new Vendor();
            $vendor->vendor_id = $attributes['vendor_id'];
            $vendor->name = $attributes['name'];
            $vendor->email = $attributes['email'];
            $vendor->designation = $attributes['designation'];
            $vendor->status = $attributes['status'];
            $vendor->save();
            if($vendor->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Vendor added successfully');
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
            $vendor = $this->find($id);
            $vendor->vendor_id = $attributes['vendor_id'];
            $vendor->name = $attributes['name'];
            $vendor->email = $attributes['email'];
            $vendor->designation = $attributes['designation'];
            $vendor->status = $attributes['status'];
            $vendor->update();
            if($vendor->id) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Vendor details updated successfully');
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
            $vendor = $this->find($id);
            if($vendor->delete()) {
                DB::commit();
                $response = array('status' => TRUE, 'message' => 'Vendor deleted successfully');
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
