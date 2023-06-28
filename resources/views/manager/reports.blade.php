@extends('layouts.master')

@section('stylesheet')
    @parent
    <!-- data tables css -->
    <link rel="stylesheet" href="{{asset('public/template/assets/plugins/data-tables/css/datatables.min.css')}}">
    <!-- custom campaign table css -->
    <link rel="stylesheet" href="{{asset('public/css/campaign_table_custom.css')}}">

    <style>
        .dataTables_length select {
            height: 32px !important;
            padding: 0 20px;
        }
        .dataTables_filter input {
            height: 32px !important;
            /*padding: 0 20px;*/
        }
        .table {
            margin-top: 0 !important;
            width: 100% !important;
        }
        .table thead th {
            vertical-align: middle !important;
            padding: 10px 10px !important;
        }
        .table tbody {
            color: #0d0e0f;
        }
        .table .font-size-11 {
            font-size: 11px !important;
        }
    </style>

@append

@section('content')
    <section class="pcoded-main-container">
        <div class="pcoded-wrapper">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <!-- [ breadcrumb ] start -->
                    <div class="page-header">
                        <div class="page-block">
                            <div class="row align-items-center">
                                <div class="col-md-12">
                                    <div class="page-header-title">
                                        <h5 class="m-b-10">Reports</h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [ breadcrumb ] end -->
                    <div class="main-body">
                        <div class="page-wrapper">
                            <!-- [ Main Content ] start -->
                            <div class="row">
                                <!-- [ configuration table ] start -->
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Reports</h5>
                                            
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                        Team 
                                        
                                         <form action="reportshow?page=1&selteamleader={{$selteamleaderid}}" method="POST">
                                            @csrf

                                        <select name="selteamleader" id="sort-item">
                                            
                                            <option {{$selteamleaderid==0 ? 'selected' : $selteamleaderid}} disabled>Select Team Leader</option>

                                        @foreach($resultteamleadernamelist as $rowmanager)
                                        <option value="{{$rowmanager->id}}" {{$selteamleaderid==$rowmanager->id ? 'selected' : $selteamleaderid }}> 
                                        {{$rowmanager->first_name}}
                                        {{$rowmanager->last_name}} </option>

                                        @endforeach
                                        </select>
                                        
                                    <input type="date" name="startdate" value="{{$startdate}}">
                                        <input type="date" name="enddate" value="{{$enddate}}">
                                        <input type="submit">
                                    </form>
                                    
                                    <table id="table-campaigns" class="display table nowrap table-striped table-hover">                                   <tr> 
                                        <thead>
                                        <th>
                                         RA ID
                                        </th>
                                      
                                        <th>Campaign Name</th>
                                        <th>Delivery Count</th> 
                                        <th>RA(agent) Full Name</th>
                                         
                                         <th>Qualified Leads</th>
                                         <th>Rejeted Leads</th>
                                        <th>Total Leads</th>
                                        <th>Individual Rejected (%)                                     </th>
                                        <th>
                                            Individual Quality (%)
                                        </th>
                                         {{-- <th>
                                            Status
                                        </th> --}}
                                        
                                         </thead>
                                         </tr>
                                         @php $sumcompleted=0; $sumtotleads=0; $sumrejected=0;@endphp
                                            @foreach($resultshowmanagerreport as $rowmanager)
                                                 
                                             <tr>
                                        
                                             <tbody>
                                         
                                                
                                              <td>{{$rowmanager->agent_id}}</td>    
                                            

                                             
                                              <td>{{$rowmanager->name}}</td>     
                                              <td>{{$rowmanager->deliver_count}}</td>
                                             <td>{{$rowmanager->first_name}}
                                                {{$rowmanager->last_name}}</td>
                                                  
                                            <td>{{$rowmanager->completed}}</td>
                                            
                                            <td>{{$rowmanager->rejected}}</td>
                                            <td>{{$rowmanager->total}}</td>
                                            <td>{{$rowmanager->totrejected}}%</td>
                                            <td>{{$rowmanager->totquality}}%</td>
                                                 
                                            {{-- <td>{{$rowmanager->rejected/$rowmanager->total*100}}%</td> --}}
                                            {{-- <td>{{$rowmanager->completed/$rowmanager->total*100}}%</td> --}}
                                            @php 
                                                 $sumcompleted+=$rowmanager->completed;
                                                 $sumtotleads+=$rowmanager->total;
                                                 $sumrejected+=$rowmanager->rejected;
                                                 @endphp   
                                             </tbody>    
                                         </tr>
                                            @endforeach
                                        <tr>
                                            <td></td>
                                            
                                            <td>
                                                <b>Total Sum of Column</b>
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                            {{$sumcompleted}}
                                        </td>
                                        <td>{{$sumrejected}}</td>
                                        <td>{{$sumtotleads}}</td>

                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                        <b>Total Team Quality (%)</b>
                                        </td>
                                        <td>{{($sumtotleads==0 ? 0 : $sumcompleted / $sumtotleads*100)}}%</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>
                                        <b>Total Team Rejected (%)</b>
                                        </td>
                                        <td>{{($sumtotleads==0 ? 0 : $sumrejected / $sumtotleads*100)}}%</td>
                                    </tr>
                                    
                                         </table>
                                         
                                         @php 
                                         
                                            
                                         
                                         for($page = 1; $page<= $totalpages; $page++) 
                                         echo '<a name="position" href = "?page=' . $page . '&selteamleader='.$selteamleaderid.'#position">' . $page . ' </a>';  
                                         $currentpage=Request()->get('page');
                                         if($currentpage>=$totalpages)
                                         {
                                         $next=1;
                                         }else{
                                            $next=$currentpage+1;
                                         }

                                         if($currentpage==1)
                                         {
                                            $previous=$totalpages;  
                                         
                                         }else {
                                            $previous=$currentpage-1;   
                                         }
                                         echo '<a name="position" href = "?page=' . $next . '&selteamleader='.$selteamleaderid.'#position"> Next </a>';  
                                         echo '<a name="position" href = "?page=' . $previous . '&selteamleader='.$selteamleaderid.'#position"> Previuos </a>';  
                                        @endphp
                                    </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <!-- [ configuration table ] end -->
                        </div>
                        <!-- [ Main Content ] end -->
                    
            </div>
        </div>
    </div>
</section>
@endsection

<!--script>
    window.onload = function() {
    var selItem = sessionStorage.getItem("SelItem");  
    $('#sort-item').val(selItem);
    }
    $('#sort-item').change(function() { 
        var selVal = $(this).val();
        sessionStorage.setItem("SelItem", selVal);
    });

    </script-->