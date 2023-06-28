<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    private $data;

    public function __construct()
    {
        $this->data = array();
    }

    public function index()
    {
        return view('manager.holiday.list');
    }

    public function getHolidayList(): \Illuminate\Http\JsonResponse
    {
        $query = Holiday::query();
        $query->whereStatus(1);
        $query->whereYear('date', date('Y'));
        $query->orderBy('date');
        $result = $query->get();
        return response()->json(array('status' => true, 'data' => $result));
    }

}
