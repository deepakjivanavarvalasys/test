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
                                            

                                        <table id="table-campaigns" class="display table nowrap table-striped table-hover">                                  
                                        <div class="col-lg-12 col-md-12">
                                            <form action="reportshow" method="POST">
                                                @csrf
                                        <input type="date" name="startdate" value="{{$startdate}}">
                                            <input type="date" name="enddate" value="{{$enddate}}">
                                            <input type="submit">
                                        </form>
                                        
                                        </div>
                                            <tr>
                                           
                                                <th>
                                                    Agent ID
                                                </th>
                                                <th>
                                                    Agent Name
                                                </th>
                                                <th>
                                                Top 10 Qualified Leads
                                                                                                  
                                                </th>

                                                <th>
                                                    Number of Rejected Leads
                                                                                                      
                                                    </th>

                                                    <th>
                                                        Total Generate Leads
                                                                                                          
                                                        </th>
                                                        <th>
                                                            Individual Quality %
                                                                                                              
                                                            </th>
                                                            <th>
                                                                Individual Rejeted %
                                                                                                                  
                                                                </th>
                                            </tr>

                                        @foreach($resultaleadcount as $leadsrow)
                                            
                                            <tr>
                                       
                                            <tbody>
                                            <td>{{$leadsrow->agent_id}}</td>
                                            <td>{{$leadsrow->first_name}} {{$leadsrow->last_name}}</td>
                                            <td>{{$leadsrow->completed}}</td>
                                            <td>{{$leadsrow->rejected}}</td>
                                            <td>{{$leadsrow->total}}</td>
                                            <td>{{$leadsrow->completed/$leadsrow->total*100}}%</td>
                                            <td>{{$leadsrow->rejected/$leadsrow->total*100}}%</td>

                                            </tbody>    
                                        </tr>
                                           @endforeach
    </table>
                                       WElcome
                                       <br>Total Records : {{$totalRecords}}    

                                       <br>
                                       <div class="card-block" style="font-size: 13px;padding: 10px 10px 0 10px;">
                                            <div class="table-responsive">
                                       <h4> Research Analyst : {{Auth::User()->full_name}} </h4>                                  
                                   
                                   <table id="table-campaigns" class="display table nowrap table-striped table-hover">                                   <tr> 
                                   <thead>
                                   <th>
                                    Seq. No.
                                   </th>
                                   <th>
                                    Agent ID
                                    </th>
                                    <th>
                                    Agent Name
                                    </th>
                                    <th>
                                    Campaign ID
                                    </th>
                                    <th>
                                    Campaign Name
                                    </th>
                                    <th>
                                    Transaction Time
                                    </th>
                                    <th>
                                        First Name
                                    </th>
                                    <th>
                                        Last Name
                                    </th>
                                    <th>
                                        Company Name
                                    </th>
                                    <th>Campaign Allocation</th>
                                    </thead>
                                    </tr>
                                       @foreach($resultalead as $leadsrow)
                                            
                                        <tr>
                                   
                                        <tbody>
                                    
                                        
                                        <td>{{$leadsrow->userid}}</td>
                                        <td>{{$leadsrow->agent_id}}</td>    
                                       
                                        <td><a href="/testing-valasys-media/agent/user/my-profile">{{$leadsrow->userfname}} {{$leadsrow->userlname}}</a></td>
                                        <td>{{$leadsrow->campaign_id}}</td>
                                        <td>{{$leadsrow->name}}</td>
                                        <td>{{$leadsrow->transaction_time}}</td>
                                        <td>{{$leadsrow->first_name}}</td>
                                        <td>{{$leadsrow->last_name}}</td>
                                        <td>{{$leadsrow->company_name}}</td>
                                        <td>{{$leadsrow->allocation}}</td>

                                        </tbody>    
                                    </tr>
                                       @endforeach

                                   
                                    
                                    </table>
                                        @php 
                                        
                                        for($page = 1; $page<= $totalpages; $page++) 
                                        echo '<a name="position" href = "reports?page=' . $page . '#position">' . $page . ' </a>';  
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

                                        echo '<a name="position" href = "?page=' . $next . '#position"> Next </a>';  
                                         echo '<a name="position" href = "?page=' . $previous . '#position"> Previuos </a>';  
                                       @endphp
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
            </div>
        </div>
    </section>
@endsection