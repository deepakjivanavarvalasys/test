<?php

namespace App\Http\Controllers\Agent;
use App\Models\AgentLead;
use App\Models\Reports;
use App\Models\Campaign;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Pagination\Paginator;
use App\Repository\ReportsRepository\ReportsRepository;

 class ReportsController extends Controller
{
    public function __construct(Reports $reports,ReportsRepository $rr,Request $req)
    {
        $this->data = array();
        $this->pagination = array();

            $this->Reports=$reports;
            $this->ReportsRepository=$rr;
             // $this->firstpage=20;
            
    }

    
    public function index(Request $req)
    {
        // print_r(paginationforall($req));
        $this->pagination=paginationforall($req);
        // echo $this->pagination['pagination1'];
        // echo $this->pagination['pagination2'];
    // $firstpage=paginationforall($req,$perpage,$page);
    
          $startdate=0;
        $enddate=0;
        $perpage=$this->pagination['perpage'];
        $page=$this->pagination['page'];
        $firstpage=$this->pagination['firstpage'];
        

       // echo $page;
        // $queryagentlead = AgentLead::query()->where('agent_id','=',Auth::user()->id);
        
           

        //   $resultalead = $queryagentlead->get();

    //      foreach($resultalead as $rowalead)
    //      {
     
    //    $resultid=$rowalead->campaign_id;   
    //      }
       //     $resultid = $queryagentlead->get('campaign_id');


    //    $resultalead = DB::select("select * from agent_leads where agent_id=".Auth::user()->id);

    //    $resultalead = DB::select("select * from agent_leads inner join campaigns on agent_leads.campaign_id=campaigns.id where agent_id=".Auth::user()->id);
    $resultaleadcount = $this->Reports->agentLeadscounocc($startdate,$enddate);

    $resultalead = $this->Reports->showAgentdata($firstpage,$perpage);

    $resulttotalrecords = $this->Reports->totalrecordsAgentdata();
    /* IMPORTANT - Comes from repository*/
    // $this->data['result']= $this->ReportsRepository->show();

    // $resultalead=$this->data['result'];
    
    
         //  $queryCampaign = Campaign::query();
 
        //  $queryCampaign->where('id','=',$resultid);

        //  $result = $queryCampaign->get('name');

//          $totalRecords = $resultalead->count();
$totalRecords=count($resulttotalrecords);

$totalpages = ceil ($totalRecords / $perpage);  


        return view('agent.reports',['resultalead'=>$resultalead,'resultaleadcount'=>$resultaleadcount,'totalRecords'=>$totalRecords,'startdate'=>$startdate,'enddate'=>$enddate,'totalpages'=>$totalpages]);
    }



public function selectdate(Request $req)
{
 //   echo $req->startdate;
   // echo $req->enddate;
   $this->pagination=paginationforall($req);
   $perpage=$this->pagination['perpage'];
    $page=$this->pagination['page'];
    $firstpage=$this->pagination['firstpage'];
    $resulttotalrecords = $this->Reports->totalrecordsAgentdata();
    // $resultalead = $this->Reports->showAgentdata();
    $totalRecords=count($resulttotalrecords);
    $totalpages = ceil ($totalRecords / $perpage);  


    $startdate= $req->startdate;
    $enddate= $req->enddate;
    $objreports=new reports;
$objreports->agentLeadscounocc($startdate,$enddate);

$resultaleadcount = $this->Reports->agentLeadscounocc($startdate,$enddate);

$resultalead = $this->Reports->showAgentdata($firstpage,$perpage);
    
return view('agent.reports',['resultalead'=>$resultalead,'resultaleadcount'=>$resultaleadcount,'totalRecords'=>$totalRecords,'startdate'=>$startdate,'enddate'=>$enddate,'totalpages'=>$totalpages]);

}
}