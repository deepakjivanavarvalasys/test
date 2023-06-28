<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Data;
use App\Repository\DataRepository\DataRepository;
use Illuminate\Http\Request;

class DataController extends Controller
{
    private $data;
    /**
     * @var DataRepository
     */
    private $dataRepository;

    public function __construct(
        DataRepository $dataRepository
    )
    {
        $this->data = array();
        $this->dataRepository = $dataRepository;
    }

    public function index()
    {
        return view('manager.data.list', $this->data);
    }

    public function store(Request $request)
    {
        $attributes = $request->all();
        $response = $this->dataRepository->store($attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function import(Request $request)
    {
        $attributes = $request->all();
        $response = $this->dataRepository->import($attributes['data_file']);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message'], 'data' => $response['data']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function getData(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = Data::query();
        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->where("first_name", "like", "%$searchValue%");
        }
        //Filters
        if(!empty($filters)) {

        }


        //Order By
        $orderColumn = $order[0]['column'];
        $orderDirection = $order[0]['dir'];
        switch ($orderColumn) {
            case '0': $query->orderBy('first_name', $orderDirection); break;
            case '1': $query->orderBy('first_name', $orderDirection); break;
            case '2': $query->orderBy('first_name', $orderDirection); break;
            case '3': $query->orderBy('first_name', $orderDirection); break;
            case '4': $query->orderBy('first_name', $orderDirection); break;
            default: $query->orderBy('first_name'); break;
        }

        $totalFilterRecords = $query->count();
        if($limit > 0) {
            $query->offset($offset);
            $query->limit($limit);
        }

        $result = $query->get();

        //dd($result->toArray());

        $ajaxData = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalFilterRecords,
            "aaData" => $result
        );

        return response()->json($ajaxData);
    }
}
