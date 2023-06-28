<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repository\DepartmentRepository\DepartmentRepository;
use App\Repository\DesignationRepository\DesignationRepository;
use App\Repository\RoleRepository\RoleRepository;
use App\Repository\UserRepository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $data;
    private $userRepository;
    private $roleRepository;
    private $departmentRepository;
    private $designationRepository;

    public function __construct(
        UserRepository $userRepository,
        RoleRepository $roleRepository,
        DepartmentRepository $departmentRepository,
        DesignationRepository $designationRepository
    )
    {
        $this->data = array();
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->departmentRepository = $departmentRepository;
        $this->designationRepository = $designationRepository;
    }

    public function index()
    {
        $this->data['resultUsers'] = $this->userRepository->get(array('status' => 1, 'role_slug' => ['admin', 'manager', 'team_leader', 'qa_team_leader']));
        $this->data['resultRoles'] = $this->roleRepository->get(array('status' => 1));
        $this->data['resultDepartments'] = $this->departmentRepository->get(array('status' => 1));
        $this->data['resultDesignations'] = $this->designationRepository->get(array('status' => 1));
        return view('admin.user.list', $this->data);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->userRepository->store($attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function edit($id): \Illuminate\Http\JsonResponse
    {
        $resultUser = $this->userRepository->find(base64_decode($id));
        if(!empty($resultUser)) {
            return response()->json(array('status' => true, 'data' => $resultUser));
        } else {
            return response()->json(array('status' => false, 'message' => 'Data not found'));
        }
    }

    public function update($id, Request $request): \Illuminate\Http\JsonResponse
    {
        $attributes = $request->all();
        $response = $this->userRepository->update(base64_decode($id),$attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $response = $this->userRepository->destroy(base64_decode($id));
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => $response['message']));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function getUsers(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = array_filter(json_decode($request->get('filters'), true));
        $search_data = $request->get('search');
        $searchValue = $search_data['value'];
        $order = $request->get('order');
        $draw = $request->get('draw');
        $limit = $request->get("length"); // Rows display per page
        $offset = $request->get("start");

        $query = User::query();
        $query->with(['role', 'department', 'designation']);
        $totalRecords = $query->count();

        //Search Data
        if(isset($searchValue) && $searchValue != "") {
            $query->where("first_name", "like", "%$searchValue%");
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
            case '0': $query->orderBy('logged_on', $orderDirection); break;
            case '1': $query->orderBy('employee_code', $orderDirection); break;
            case '2': $query->orderBy('first_name', $orderDirection); break;
            case '3': $query->orderBy('email', $orderDirection); break;
            case '4': $query->orderBy('role', $orderDirection); break;
            case '5': $query->orderBy('department', $orderDirection); break;
            case '6': $query->orderBy('designation', $orderDirection); break;
            case '7': $query->orderBy('status', $orderDirection); break;
            case '8': $query->orderBy('created_at', $orderDirection); break;
            case '9': $query->orderBy('updated_at', $orderDirection); break;
            default: $query->orderBy('created_at', 'DESC'); break;
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

    public function validateEmployeeCode(Request $request)
    {
        $user = User::query();
        $user->whereEmployeeCode(strtoupper($request->employee_code));

        if($request->has('user_id') && !empty($request->user_id)) {
            $user->where('id', '!=', base64_decode($request->user_id));
        }

        if($user->exists()) {
            return 'false';
        } else {
            return 'true';
        }
    }

    public function validateEmail(Request $request)
    {
        $user = User::query();
        $user->whereEmail($request->email);

        if($request->has('user_id') && !empty($request->user_id)) {
            $user->where('id', '!=', base64_decode($request->user_id));
        }

        if($user->exists()) {
            return 'false';
        } else {
            return 'true';
        }
    }

    public function my_profile()
    {
        $this->data['resultUser'] = $this->userRepository->find(Auth::id());
        return view('admin.user.my_profile', $this->data);
    }

    public function change_password(Request $request)
    {
        $attributes = $request->all();
        $response = $this->userRepository->update(Auth::id(), $attributes);
        if($response['status'] == TRUE) {
            return redirect()->route('logout')->with('success', ['title' => 'Request Successful', 'message' => 'Password updated successfully']);
            //return redirect()->back()->with('success', ['title' => 'Request Successful', 'message' => 'Password updated successfully']);
        } else {
            return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => $response['message']]);
        }
    }

}
