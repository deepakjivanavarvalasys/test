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
                                        
                                        
                                        <form action="reportshow" method="POST">
                                            @csrf
                                    <input type="date" name="startdate" value="{{$startdate}}">
                                        <input type="date" name="enddate" value="{{$enddate}}">
                                        <input type="submit" value="submit">
                                    </form>
                                    
                                       <div class="card-block" style="font-size: 13px;padding: 10px 10px 0 10px;">
                                        <div class="table-responsive">
                                       <h4> Team Leader : {{Auth::User()->id}} {{Auth::User()->full_name}} </h4>                                  
                                       <table id="table-campaigns" class="display table nowrap table-striped table-hover">                                   <tr> 
                                        <thead>
                                        <th>
                                         ID
                                        </th>
                                      
                                         
                                         <th>
                                             Full Name
                                         </th>
                                         <th>
                                            Qualified Leads
                                        </th>
                                         <th>
                                            Rejeted Leads
                                        </th>
                                        <th>
                                            Total Leads
                                        </th>
                                        <th>
                                           Individual Rejected (%)
                                        </th>
                                        <th>
                                            Individual Quality (%)
                                        </th>
                                         {{-- <th>
                                            Status
                                        </th> --}}
                                        
                                         </thead>
                                         </tr>
                                         @php $sumcompleted=0; $sumtotleads=0; $sumrejected=0;@endphp
                                            @foreach($resultteamleader as $leadsrow)
                                                 
                                             <tr>
                                        
                                             <tbody>
                                         
                                                
                                              <td>{{$leadsrow->agent_id}}</td>    
                                            

                                             
                                             
                                             <td>{{$leadsrow->first_name}}
                                                {{$leadsrow->last_name}}</td>
                                                 
                                            <td>{{$leadsrow->completed}}</td>
                                            
                                            <td>{{$leadsrow->rejected}}</td>
                                            <td>{{$leadsrow->total}}</td>
                                            <td>{{$leadsrow->totrejected}}%</td>
                                            <td>{{$leadsrow->totquality}}%</td>
                                                 
                                            {{-- <td>{{$leadsrow->rejected/$leadsrow->total*100}}%</td> --}}
                                            {{-- <td>{{$leadsrow->completed/$leadsrow->total*100}}%</td> --}}
                                            @php 
                                                 $sumcompleted+=$leadsrow->completed;
                                                 $sumtotleads+=$leadsrow->total;
                                                 $sumrejected+=$leadsrow->rejected;
                                                 @endphp   
                                             </tbody>    
                                         </tr>
                                            @endforeach
                                        <tr>
                                            <td>
                                                
                                            </td>
                                            <td>
                                                <b>Total Sum of Column</b>
                                            </td>
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
                                    <tr>
                                        <a href="{{ route('team_leader.reportsdownload') }}?startdate={{$startdate}}&enddate={{$enddate}}&sumcompleted={{$sumcompleted}}&sumrejected={{$sumrejected}}&sumtotleads={{$sumtotleads}}" class="btn btn-primary">
                                            Export Data
                                       </a>
                                    </tr>
                                         </table>
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
