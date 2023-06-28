@extends('layouts.master')

@section('stylesheet')
    @parent
    <!-- data tables css -->
    <link rel="stylesheet" href="{{asset('public/template/assets/plugins/data-tables/css/datatables.min.css')}}">
    <meta name="ca-agent-id" content="{{ base64_encode($resultCAAgent->id) }}">

    <style>
        .table td{
            padding: 5px 10px !important;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before {
            position: absolute;
            top: 7px;
        }

        table td .btn-sm{
            padding: 4px 7px;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before {
            height: 24px;
            width: 24px;
            line-height: 25px;
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
                                        <h5 class="m-b-10">Manage Data</h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('vendor_manager.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('vendor_manager.ra.campaign.list') }}">My Campaigns</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('vendor_manager.ra.campaign.show', base64_encode($resultCAAgent->id)) }}">Campaign Details</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">View Data</a></li>
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
                                            <h5>Data List</h5>
                                        </div>
                                        <div class="card-block">
                                            <div class="table-responsive">
                                                <table id="table-data" class="display table dt-responsive nowrap table-striped table-hover" style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Action</th>
                                                        <th class="text-center">LinkedIn Profile<br>URL</th>
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
                                                        <th>LinkedIn Profile SN URL</th>
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

    <div id="modal-form-edit-data" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="background: rgba(0, 0, 0, 0.7) !important;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit / Nurture Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="form-edit-data">
                    <div class="modal-body">

                        <input type="hidden" id="data_id" name="data_id">

                        <div class="row">
                            <div class="col-md-4 form-group">
                                    <label for="first_name">First Name<span class="text-danger">*</span></label>
                                <input autofocus type="text" class="form-control btn-square" id="first_name" name="first_name" placeholder="Enter first name">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="last_name" name="last_name" placeholder="Enter last name">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="company_name">Company Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="company_name" name="company_name" placeholder="Enter company name">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="email_address">Email Address<span class="text-danger">*</span></label>
                                <input autofocus type="text" class="form-control btn-square" id="email_address" name="email_address" placeholder="Enter email address">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="specific_title">Specific Title<span class="text-danger">*</span></label>
                                <input autofocus type="text" class="form-control btn-square" id="specific_title" name="specific_title" placeholder="Enter title">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="job_level">Job Level<span class="text-info"> <small>(optional)</small></span></label>
                                <input autofocus type="text" class="form-control btn-square" id="job_level" name="job_level" placeholder="Enter job level">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="job_role">Job Role<span class="text-info"> <small>(optional)</small></span></label>
                                <input autofocus type="text" class="form-control btn-square" id="job_role" name="job_role" placeholder="Enter job role">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="phone_number">Phone Number<span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="phone_number" name="phone_number" placeholder="Enter phone number">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="address_1">Address 1<span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="address_1" name="address_1" placeholder="Enter address 1">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="address_2">Address 2<span class="text-info"> <small>(optional)</small></span></label>
                                <input type="text" class="form-control btn-square" id="address_2" name="address_2" placeholder="Enter address 2">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="city">City<span class="text-danger">*</span></label>
                                <input autofocus type="text" class="form-control btn-square" id="city" name="city" placeholder="Enter city">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="state">State<span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="state" name="state" placeholder="Enter state">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="zipcode">Zipcode<span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="zipcode" name="zipcode" placeholder="Enter zipcode">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="country">Country<span class="text-danger">*</span></label>
                                <input autofocus type="text" class="form-control btn-square" id="country" name="country" placeholder="Enter country">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="employee_size">Employee/Company Size<span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="employee_size" name="employee_size" placeholder="Enter size">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="revenue">Revenue<span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="revenue" name="revenue" placeholder="Enter revenue">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="company_domain">Company Domain<span class="text-danger">*</span></label>
                                <input autofocus type="text" class="form-control btn-square" id="company_domain" name="company_domain" placeholder="Enter company domain">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="website">Website<span class="text-info"> <small>(optional)</small></span></label>
                                <input type="text" class="form-control btn-square" id="website" name="website" placeholder="Enter website">
                            </div>
                        </div>

                        <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="company_linkedin_url">Company LinkedIn URL<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control btn-square" id="company_linkedin_url" name="company_linkedin_url" placeholder="Enter company linkedin url">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="linkedin_profile_link">LinkedIn Profile Link<span class="text-danger">*</span></label>
                                    <input autofocus type="text" class="form-control btn-square" id="linkedin_profile_link" name="linkedin_profile_link" placeholder="Enter linkedin profile link">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="linkedin_profile_sn_link">LinkedIn Profile SN Link<span class="text-info"> <small>(optional)</small></span></label>
                                    <input type="text" class="form-control btn-square" id="linkedin_profile_sn_link" name="linkedin_profile_sn_link" placeholder="Enter LinkedIn Profile SN Link">
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <button id="form-edit-data-submit" type="button" class="btn btn-primary btn-square float-right">Update</button>
                            </div>
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
    <script src="{{ asset('public/js/vendor_manager/ra/data.js?='.time()) }}"></script>
@append


