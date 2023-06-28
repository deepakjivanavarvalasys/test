<?php

namespace App\Http\Controllers\TeamLeader;

use App\Http\Controllers\Controller;
use App\Repository\UserRepository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $data;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    )
    {
        $this->data = array();
        $this->userRepository = $userRepository;
    }

    public function my_profile()
    {
        $this->data['resultUser'] = $this->userRepository->find(Auth::id());
        return view('team_leader.user.my_profile', $this->data);
    }

    public function update(Request $request)
    {
        $attributes = $request->all();
        $response = $this->userRepository->update(Auth::id(), $attributes);
        if($response['status'] == TRUE) {
            return response()->json(array('status' => true, 'message' => 'Details updated successfully'));
        } else {
            return response()->json(array('status' => false, 'message' => $response['message']));
        }
    }

    public function change_password(Request $request)
    {
        $attributes = $request->all();
        $response = $this->userRepository->update(Auth::id(), $attributes);
        if($response['status'] == TRUE) {
            //return redirect()->route('agent.user.my_profile')->with('success', 'Password updated successfully');
            return redirect()->back()->with('success', ['title' => 'Request Successful', 'message' => 'Password updated successfully']);
        } else {
            return back()->withInput()->with('error', ['title' => 'Error while processing request', 'message' => $response['message']]);
        }
    }
}
