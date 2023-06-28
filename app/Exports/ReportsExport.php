<?php

namespace App\Exports;

use App\Models\Reports;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
 
use Illuminate\Http\Request;

class ReportsExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
   
    public function __construct(Reports $reports,Request $req)
    {
        $this->Reports=$reports;
        $this->Request=$req;
    }
    
    public function headings():array{
        return[
            'Id',
            'First Name',
            'Last Name',
            'Qualified Leads',
            'Rejected Leads',
            'Total Leads',
            'Individual Rejected %',
            'Individual Quality %',
           
        ];
    }
    public function collection()
    {
        $sumcompleted=$this->Request->sumcompleted;
        $sumrejected= $this->Request->sumrejected;
        $sumtotleads=$this->Request->sumtotleads;
        $startdate= $this->Request->startdate;
        $totalteamqulitypercentage=($sumtotleads==0 ? 0 : $sumcompleted / $sumtotleads*100);
        $totalteamrejectedpercentage=($sumtotleads==0 ? 0 : $sumrejected / $sumtotleads*100);

        // return Reports::all();
        //   return collect(DB::select("select * from agent_leads"));  
        //  $startdate=0;
        $enddate=$this->Request->enddate;
        $resultteamleader=$this->Reports->showTeamleader($startdate,$enddate);
        
         return collect($resultteamleader)->merge([['','','Total Sum of Column',$sumcompleted,$sumrejected,$sumtotleads]])
         ->merge([['','','Total Team Quality (%)',$totalteamqulitypercentage]])
         ->merge([['','','Total Team Rejected (%)',$totalteamrejectedpercentage]]);
    }
    
}
