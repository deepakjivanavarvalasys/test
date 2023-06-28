@extends('layouts.master')

@section('stylesheet')
    @parent
    <!-- data tables css -->
    <link rel="stylesheet" href="{{asset('public/template/assets/plugins/data-tables/css/datatables.min.css')}}">
    <!-- custom campaign table css -->
    <link rel="stylesheet" href="{{asset('public/css/campaign_table_custom.css')}}">

    <meta name="ca-ratl-id" content="{{ base64_encode($resultCARATL->id) }}">

    <style>
        .table td{
            padding: 5px 10px !important;
        }
        .form-control-small {
            line-height: 1;
            padding: 1px 20px;
            font-size: 12px;
        }
        .form-control-small-select {
            height: 2.1rem !important;
            padding: 5px 10px !important;
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
                                        <h5 class="m-b-10">{{ $resultCARATL->campaign->name }}</h5>
                                        <div class="card-header-right mb-1" style="float: right;">
                                            <a href="{{ route('team_leader.campaign_assign.show', base64_encode($resultCARATL->id)) }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('team_leader.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('team_leader.campaign_assign.list') }}">Campaign Assign</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('team_leader.campaign_assign.show', base64_encode($resultCARATL->id)) }}">Campaign Details</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Manage Leads</a></li>
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
                                            <h5>Lead Details</h5>
                                            <div class="float-right">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-dark btn-square btn-sm" onclick="export_file('{{ base64_encode($resultCARATL->id) }}', 'all');"><i class="feather icon-download"></i>Export</button>
                                                    <button type="button" class="btn btn-dark btn-square btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="sr-only">Toggle Dropdown</span></button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="export_file('{{ base64_encode($resultCARATL->id) }}', 'all');">All</a>
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="export_file('{{ base64_encode($resultCARATL->id) }}', 'sent');">Sent Leads</a>
                                                        <a class="dropdown-item" href="javascript:void(0);" onclick="export_file('{{ base64_encode($resultCARATL->id) }}', 'un_send');">Un-Send Leads</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-block">
                                            <div class="table-responsive">
                                                <table id="table-leads" class="display table nowrap table-striped table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="2" class="text-center">Agent Details</th>
                                                        <th colspan="18">Lead Details</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Action</th>
                                                        <th>Agent</th>
                                                        <th>Date</th>
                                                        <th>First Name</th>
                                                        <th>Last Name</th>
                                                        <th>Company Name</th>
                                                        <th>Email Address</th>
                                                        <th>Specific Title</th>
                                                        <th>Job Level</th>
                                                        <th>Job Role</th>
                                                        <th>Phone Number</th>
                                                        <th>Address 1</th>
                                                        <th>Address 2</th>
                                                        <th>City</th>
                                                        <th>State</th>
                                                        <th>Zipcode</th>
                                                        <th>Country</th>
                                                        <th>Industry</th>
                                                        <th>Employee Size</th>
                                                        <th>Employee Size 2</th>
                                                        <th>Revenue</th>
                                                        <th>Company Domain</th>
                                                        <th>Website</th>
                                                        <th>Company LinkedIn URL</th>
                                                        <th>LinkedIn Profile URL</th>
                                                        <th>LinkedIn Profile SN URL</th>
                                                        <th>Comment</th>
                                                        <th>TL Comment</th>
                                                        <th>QC Comment</th>
                                                        <th>Status</th>
                                                        <th>Send For QC<br>Date</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
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

    <div id="modal-reject-lead" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form-reject-lead" method="post">
                    <input type="hidden" name="agent_lead_id"  value="">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject agent's lead</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="">
                            <textarea class="form-control" name="comment_2" id="comment_2" rows="3" placeholder="Please specify reason..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="form-reject-lead-submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    @parent
    <!-- datatable Js -->
    <script src="{{ asset('public/template/assets/plugins/data-tables/js/datatables.min.js') }}"></script>
    <!-- custom Js -->
    <script src="{{ asset('public/js/team_leader/lead.js?='.time()) }}"></script>
@append


