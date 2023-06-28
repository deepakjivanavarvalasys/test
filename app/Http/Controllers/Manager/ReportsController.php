<?php

namespace App\Http\Controllers\manager;
use App\Models\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    //
    public function __construct(Reports $reports){
        $this->Reports=$reports;

    }

    public function index(Request $req)
    { 

        $startdate=0;
        $enddate=0;
        $selteamleaderid=0;

        $this->pagination=paginationforall($req);
        $perpage=$this->pagination['perpage'];
        $page=$this->pagination['page'];
        $firstpage=$this->pagination['firstpage'];
        
        $resultteamleadernamelist=$this->Reports->teamleadernamelist();
        $resultshowmanagerreport=$this->Reports->showmanagerreport($firstpage,$perpage,$selteamleaderid,$startdate,$enddate);

$resulttotalrecords=$this->Reports->totalcountshowmanagerreport($selteamleaderid,$startdate,$enddate);
        $totalRecords=count($resulttotalrecords);
    $totalpages = ceil ($totalRecords / $perpage);  

        return view('manager.reports',['resultteamleadernamelist'=>$resultteamleadernamelist,'startdate'=>$startdate,'enddate'=>$enddate,'resultshowmanagerreport'=>$resultshowmanagerreport,'totalpages'=>$totalpages,'selteamleaderid'=>$selteamleaderid]);
    }

    public function selectdate(Request $req)
    {
           $startdate=$req->startdate;
        $enddate=$req->enddate;
        $selteamleaderid=$req->selteamleader;

         $this->pagination=paginationforall($req);
       $perpage=$this->pagination['perpage'];
        $page=$this->pagination['page'];
        $firstpage=$this->pagination['firstpage'];
    

            //  echo $selteamleaderid;
        $resultteamleadernamelist=$this->Reports->teamleadernamelist();
        $resultshowmanagerreport=$this->Reports->showmanagerreport($firstpage,$perpage,$selteamleaderid,$startdate,$enddate);
     
        $resulttotalrecords=$this->Reports->totalcountshowmanagerreport($selteamleaderid,$startdate,$enddate);
        $totalRecords=count($resulttotalrecords);
    $totalpages = ceil ($totalRecords / $perpage);  

     //   print_r($resultshowmanagerreport);
        return view('manager.reports',['resultteamleadernamelist'=>$resultteamleadernamelist,'startdate'=>$startdate,'enddate'=>$enddate,'resultshowmanagerreport'=>$resultshowmanagerreport,'totalpages'=>$totalpages,'selteamleaderid'=>$selteamleaderid]);
    }

}
