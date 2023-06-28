<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Country;
use App\Repository\RegionRepository\RegionRepository;
use App\Repository\CountryRepository\CountryRepository;
use Illuminate\Http\Request;

class GeoController extends Controller
{
    private $data;
    private $regionRepository;
    private $countryRepository;


    public function __construct(
        RegionRepository $regionRepository,
        CountryRepository $countryRepository
        )
    {
        $this->data = array();
        $this->regionRepository = $regionRepository;
        $this->countryRepository = $countryRepository;
    }

    //Region function
    public function regionIndex ()
    {
        return view('admin.geo.region.list');
    }

    public function getRegions(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];

        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = Region::query();
        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->where("name", "like", "%$searchValue%");
            $query->orWhere("abbreviation", "like", "%$searchValue%");
        }

        //Filters
        if(!empty($filters)) {

        }

        $totalFilterRecords = $query->count();

        //Order By
        $orderColumn = null;
        if ($request->has('order')){
            $order = $request->get('order');
            $orderColumn = $order[0]['column'];
            $orderDirection = $order[0]['dir'];
        }

        switch ($orderColumn) {
            case '0': $query->orderBy('abbreviation', $orderDirection); break;
            case '1': $query->orderBy('name', $orderDirection); break;
            case '2': $query->orderBy('status', $orderDirection); break;
            default: $query->orderBy('name'); break;
        }

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

    public function regionStore(Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        //dd($attributes);
        $response = $this->regionRepository->store($attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function regionEdit($id): \Illuminate\Http\JsonResponse
    {
        $resultRole = $this->regionRepository->find(base64_decode($id));
        if(!empty($resultRole)) {
            return response()->json(array('status' => true, 'data' => $resultRole));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function regionUpdate($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->regionRepository->update(base64_decode($id),$attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }
    public function regionDestroy($id): \Illuminate\Http\JsonResponse
    {
        $response = $this->regionRepository->destroy(base64_decode($id));
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    //Country function
    public function countryIndex ()
    {
        $this->data['resultRegions'] =  $this->regionRepository->get();
        return view('admin.geo.country.list',$this->data);
    }

    public function getCountries(Request $request): \Illuminate\Http\JsonResponse
    {

        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];

        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = Country::query();
        $query->with('region');
        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->where("name", "like", "%$searchValue%");
            //$query->orWhere("abbreviation", "like", "%$searchValue%");
        }

        //Filters
        if(!empty($filters)) {

        }

        $totalFilterRecords = $query->count();

        //Order By
        $orderColumn = $order[0]['column'];
        $orderDirection = $order[0]['dir'];

        switch ($orderColumn) {
            case '0': $query->orderBy('name', $orderDirection); break;
            case '1': $query->orderBy('region_id', $orderDirection); break;
            case '2': $query->orderBy('status', $orderDirection); break;
            default: $query->orderBy('name'); break;
        }

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

    public function countryStore(Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->countryRepository->store($attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function countryEdit($id): \Illuminate\Http\JsonResponse
    {
        $resultRole = $this->countryRepository->find(base64_decode($id));
        if(!empty($resultRole)) {
            return response()->json(array('status' => true, 'data' => $resultRole));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function countryUpdate($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->countryRepository->update(base64_decode($id),$attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }
    public function countryDestroy($id): \Illuminate\Http\JsonResponse
    {
        $response = $this->countryRepository->destroy(base64_decode($id));
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

}
