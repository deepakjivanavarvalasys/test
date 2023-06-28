<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tutorial;
use App\Repository\Tutorial\TutorialRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->data['resultTutorials'] = $this->tutorialRepository->get(array('role_id' => Auth::user()->role_id));
        return view('pages.tutorial.list', $this->data);
    }

}
