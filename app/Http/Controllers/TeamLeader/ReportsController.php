<?php

namespace App\Http\Controllers\TeamLeader;
use App\Models\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportsExport;

class ReportsController extends Controller
{
   
   
    public function __construct(Reports $reports)
    {
        $this->Reports=$reports;
    }


    public function index()
    {
        $startdate=0;
        $enddate=0;
        $resultteamleader=$this->Reports->showTeamleader($startdate,$enddate);
        return view('team_leader.reports',['resultteamleader'=>$resultteamleader,'startdate'=>$startdate,'enddate'=>$enddate]);
    }

    public function selectdate(Request $req)
{
 //   echo $req->startdate;
   // echo $req->enddate;
  
    $startdate= $req->startdate;
    $enddate= $req->enddate;
    $objreports=new reports;
$objreports->showTeamleader($startdate,$enddate);

$resultteamleader = $this->Reports->showTeamleader($startdate,$enddate);


    // $resultalead = $this->Reports->showAgentdata();
    // $totalRecords=count($resultalead);

return view('team_leader.reports',['resultteamleader'=>$resultteamleader,'startdate'=>$startdate,'enddate'=>$enddate]);

}

public function reportsdownload(Request $req){

         return Excel::download(new ReportsExport($this->Reports,$req), 'reports.xlsx');
    
}

}


