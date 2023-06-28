@extends('layouts.master')

@section('stylesheet')
    @parent
    <!-- data tables css -->
    <link rel="stylesheet" href="{{asset('public/template/assets/plugins/data-tables/css/datatables.min.css')}}">
    <!-- custom campaign table css -->
    <link rel="stylesheet" href="{{asset('public/css/campaign_table_custom.css')}}">

    <meta name="ca-vm-id" content="{{ base64_encode($resultCAVM->id) }}">

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
                                        <h5 class="m-b-10">{{ $resultCAVM->campaign->name }}</h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('vendor_manager.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('vendor_manager.campaign_assign.list') }}">My Campaigns</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('vendor_manager.campaign_assign.show', base64_encode($resultCAVM->id)) }}">Campaign Details</a></li>
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
                                                <button autofocus type="button" class="btn btn-primary btn-square btn-sm" data-toggle="modal" data-target="#modal-import-leads"><i class="feather icon-upload"></i>Upload Leads</button>
                                            </div>
                                        </div>
                                        <div class="card-block">
                                            <div class="table-responsive">
                                                <table id="table-leads" class="display table nowrap table-striped table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th colspan="2" class="text-center">Vendor Details</th>
                                                        <th colspan="18">Lead Details</th>
                                                    </tr>
                                                    <tr>
<!--                                                        <th>Action</th>-->
                                                        <th>Vendor</th>
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
                                                        <th>Revenue</th>
                                                        <th>Company Domain</th>
                                                        <th>Website</th>
                                                        <th>Company LinkedIn URL</th>
                                                        <th>LinkedIn Profile URL</th>
                                                        <th>LinkedIn Profile SN URL</th>
                                                        <th>Comment</th>
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

    <div id="modal-import-leads" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form-upload-leads" method="post" action="" enctype="multipart/form-data">

                    <div class="modal-header">
                        <h5 class="modal-title">Upload Leads</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Select Vendor</label>
                                <div class="float-right">
                                    <button type="button" class="btn btn-outline-dark btn-square btn-sm p-1 pl-2 pr-2" style="font-size: 11px" onclick="downloadSampleFile('vm-lead-upload.xlsx');" download><i class="feather icon-download"></i>Download Sample</button>
                                </div>
                                <br><br>
                                <select class="form-control" id="vendor_id" name="vendor_id" required>
                                    @foreach($resultCAVM->vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 form-group">
                                <label>Select lead file <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="lead_file" name="lead_file" required>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button id="form-upload-leads-submit" type="button" class="btn btn-primary">Upload</button>
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
    <script src="{{ asset('public/js/vendor_manager/lead.js?='.time()) }}"></script>
@append


