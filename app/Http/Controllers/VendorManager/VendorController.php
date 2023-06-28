<?php

namespace App\Http\Controllers\VendorManager;

use App\Http\Controllers\Controller;
use App\Repository\VendorRepository\VendorRepository;
use Illuminate\Http\Request;
use App\Models\Vendor;
use Auth;

class VendorController extends Controller
{
    private $data;
    private $vendorRepository;

    public function __construct(VendorRepository $vendorRepository)
    {
        $this->data = array();
        $this->vendorRepository = $vendorRepository;
    }

    public function index()
    {
        return view('vendor_manager.vendor.list');
    }

    public function store(Request $request)
    {
        $attributes = $request->all();
        //dd($attributes);
        $response = $this->vendorRepository->store($attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }

    }
    public function edit($id)
    {
        $resultRole = $this->vendorRepository->find(base64_decode($id));
        if(!empty($resultRole)) {
            return response()->json(array('status' => true, 'data' => $resultRole));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function update($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->vendorRepository->update(base64_decode($id),$attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function destroy($id)
    {
        $response = $this->vendorRepository->destroy(base64_decode($id));
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function getVendors(Request $request)
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = Vendor::query();
        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->where("vendor_id", "like", "%$searchValue%");
            $query->orWhere("name", "like", "%$searchValue%");
            $query->orWhere("email", "like", "%$searchValue%");
            $query->orWhere("designation", "like", "%$searchValue%");
        }
        //Filters
        if(!empty($filters)) { }

        //Order By
        $orderColumn = null;
        if ($request->has('order')){
            $order = $request->get('order');
            $orderColumn = $order[0]['column'];
            $orderDirection = $order[0]['dir'];
        }

        switch ($orderColumn) {
            case '0': $query->orderBy('vendor_id', $orderDirection); break;
            case '1': $query->orderBy('name', $orderDirection); break;
            case '2': $query->orderBy('email', $orderDirection); break;
            case '3': $query->orderBy('designation', $orderDirection); break;
            case '4': $query->orderBy('status', $orderDirection); break;
            default: $query->orderBy('created_at', 'desc'); break;
        }

        $totalFilterRecords = $query->count();

        if($limit > 0) {
            $query->offset($offset);
            $query->limit($limit);
        }

        $result = $query->get();

        $ajaxData = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalFilterRecords,
            "aaData" => $result
        );

        return response()->json($ajaxData);
    }
}
