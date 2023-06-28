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
                                        
                                        <table>
                                            <tr>
                                                test
                                            </tr>
                                        
                                        </table>
                                        
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
