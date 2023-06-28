@extends('layouts.master')

@section('stylesheet')
    @parent
    <!-- data tables css -->
    <link rel="stylesheet" href="{{asset('public/template/assets/plugins/data-tables/css/datatables.min.css')}}">
    <!-- custom campaign table css -->
    <link rel="stylesheet" href="{{asset('public/css/campaign_table_custom.css')}}">

    <meta name="ca-agent-id" content="{{ base64_encode($resultCAAgent->id) }}">

    <style>
        .table td{
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
                                        <h5 class="m-b-10">{{ $resultCAAgent->campaign->name }}</h5>
                                        <div class="card-header-right mb-1" style="float: right;">
                                            <a href="{{ route('agent.campaign.show', base64_encode($resultCAAgent->id)) }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('agent.campaign.list') }}">My Campaigns</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('agent.campaign.show', base64_encode($resultCAAgent->id)) }}">Campaign Details</a></li>
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
                                                {{--  }}
                                                <button type="button" class="btn btn-dark btn-square btn-sm" data-toggle="modal" data-target="#modal-import-leads" title="Bulk Import"><i class="feather icon-upload mr-0"></i></button>
                                                {{  --}}
                                                <a href="{{ route('agent.lead.create', base64_encode($resultCAAgent->id)) }}">
                                                    <button autofocus type="button" class="btn btn-primary btn-square btn-sm"><i class="feather icon-plus"></i>Add New Lead</button>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-block">
                                            <div class="table-responsive">
                                                <table id="table-leads" class="display table nowrap table-striped table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>Action</th>
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
                                                        <th>Created At</th>
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

    <div id="modal-import-leads" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="form-import-leads" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="ca_agent_id" value="{{ base64_encode($resultCAAgent->id) }}">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="data_file">Upload Leads File <span class="text-danger">*</span></label>
                                    <div class="float-right">
                                        <button type="button" class="btn btn-outline-dark btn-square btn-sm p-1 pl-2 pr-2" style="font-size: 11px" onclick="downloadSampleFile('import-leads.xlsx');" download><i class="feather icon-download"></i>Download Sample</button>
                                    </div>
                                    <br><br>
                                    <input type="file" class="form-control-file" id="leads_file" name="leads_file" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="col-md-12">
                            <button id="form-import-leads-submit" type="button" class="btn btn-primary btn-square float-right btn-sm">Upload</button>
                            <button type="reset" class="btn btn-secondary btn-square float-right btn-sm" data-dismiss="modal" aria-label="Close">Close</button>
                        </div>
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
    <script src="{{ asset('public/js/agent/lead.js?='.time()) }}"></script>
@append


