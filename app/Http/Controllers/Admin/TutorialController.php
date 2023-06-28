<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use App\Repository\RoleRepository\RoleRepository;
use App\Repository\Tutorial\TutorialRepository;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    private $data;
    /**
     * @var TutorialRepository
     */
    private $tutorialRepository;

    public function __construct(TutorialRepository $tutorialRepository)
    {
        $this->data = array();
        $this->tutorialRepository = $tutorialRepository;
    }

    public function index()
    {
        $this->data['resultRoles'] = RoleRepository::get(array('status' => 1));
        return view('admin.tutorial.list', $this->data);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->tutorialRepository->store($attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        $result = $this->tutorialRepository->find(base64_decode($id));
        if(!empty($result)) {
            return response()->json(array('status' => true, 'data' => $result));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function update($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->tutorialRepository->update(base64_decode($id),$attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $response = $this->tutorialRepository->destroy(base64_decode($id));
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function getTutorials(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = Tutorial::query();
        $query->with('role');
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
            case '0': break;
            case '1': $query->orderBy('title', $orderDirection); break;
            case '2': $query->orderBy('role_id', $orderDirection); break;
            case '3': $query->orderBy('description', $orderDirection); break;
            case '4': $query->orderBy('link', $orderDirection); break;
            case '5': $query->orderBy('status', $orderDirection); break;
            case '6': $query->orderBy('created_at', $orderDirection); break;
            case '7': $query->orderBy('updated_at', $orderDirection); break;
            default: $query->orderBy('created_at', 'desc'); break;
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
