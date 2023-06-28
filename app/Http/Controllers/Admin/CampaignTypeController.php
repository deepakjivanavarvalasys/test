<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignType;
use App\Repository\CampaignTypeRepository\CampaignTypeRepository;
use Illuminate\Http\Request;

class CampaignTypeController extends Controller
{
    private $data;
    private $campaignTypeRepository;

    public function __construct(CampaignTypeRepository $campaignTypeRepository)
    {
        $this->data = array();
        $this->campaignTypeRepository = $campaignTypeRepository;
    }

    public function index()
    {
        return view('admin.campaign_type.list');
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->campaignTypeRepository->store($attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        $resultDepartment = $this->campaignTypeRepository->find(base64_decode($id));
        if(!empty($resultDepartment)) {
            return response()->json(array('status' => true, 'data' => $resultDepartment));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function update($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->campaignTypeRepository->update(base64_decode($id),$attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $response = $this->campaignTypeRepository->destroy(base64_decode($id));
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function getCampaignTypes(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = CampaignType::query();
        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->where("name", "like", "%$searchValue%");
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
            case '0': $query->orderBy('name', $orderDirection); break;
            case '1': $query->orderBy('full_name', $orderDirection); break;
            case '2': $query->orderBy('status', $orderDirection); break;
            case '3': $query->orderBy('created_at', $orderDirection); break;
            case '4': $query->orderBy('updated_at', $orderDirection); break;
            default: $query->orderBy('name'); break;
        }

        $totalFilterRecords = $query->count();
        $query->offset($offset);
        $query->limit($limit);
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
