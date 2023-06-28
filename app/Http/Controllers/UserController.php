<?php

namespace App\Http\Controllers;

use App\Repository\UserRepository\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function uploadProfile(Request $request)
    {
        try {
            $attributes = $request->all();
            $user_id = Auth::id();
            $response = $this->userRepository->update($user_id, $attributes);

            if($response['status'] == TRUE) {
                return response()->json(array('status' => true, 'message' => 'Profile picture updated successfully'));
            } else {
                throw new \Exception('Something went wrong, please try again.');
            }
        } catch (\Exception $exception) {
            return response()->json(array('status' => false, 'message' => 'Something went wrong, please try again.'));
        }
    }

}
