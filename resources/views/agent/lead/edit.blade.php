@extends('layouts.master')

@section('stylesheet')
    @parent
    <meta name="ca-agent-id" content="{{ base64_encode($resultAgentLead->ca_agent_id) }}">
    <meta name="lead-id" content="{{ base64_encode($resultAgentLead->id) }}">
    <style>
        .btn:focus {
            border-color: #80bdff !important;
            outline: 0 !important;
            box-shadow: 0 0 0 0.2rem rgb(0 123 255 / 25%) !important;
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
                                        <h5 class="m-b-10">{{ $resultAgentLead->campaign->name }}</h5>
                                        <div class="card-header-right mb-1" style="float: right;">
                                            <a href="{{ route('agent.lead.list', base64_encode($resultCAAgent->id)) }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('agent.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('agent.campaign.list') }}">My Campaigns</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('agent.campaign.show', base64_encode($resultAgentLead->campaign_id)) }}">Campaign Details</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('agent.lead.list', base64_encode($resultAgentLead->ca_agent_id)) }}">Manage Leads</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Edit Lead Details</a></li>
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
                                            <h5>Edit Lead Details</h5>
                                        </div>
                                        <div class="card-block">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <form id="form-lead-edit" method="post" action="{{ route('agent.lead.update', base64_encode($resultAgentLead->id)) }}">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="first_name">First Name<span class="text-danger">*</span></label>
                                                                <input autofocus type="text" class="form-control btn-square" id="first_name" name="first_name" placeholder="Enter first name" value="{{ $resultAgentLead->first_name }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="last_name" name="last_name" placeholder="Enter last name" value="{{ $resultAgentLead->last_name }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="company_name">Company Name<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="company_name" name="company_name" placeholder="Enter company name" value="{{ $resultAgentLead->company_name }}">
                                                            </div>

                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="email_address">Email Address<span class="text-danger">*</span></label>
                                                                <input autofocus type="text" class="form-control btn-square" id="email_address" name="email_address" placeholder="Enter email address" value="{{ $resultAgentLead->email_address }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="specific_title">Specific Title<span class="text-danger">*</span></label>
                                                                <input autofocus type="text" class="form-control btn-square" id="specific_title" name="specific_title" placeholder="Enter title" value="{{ $resultAgentLead->specific_title }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="job_level">Job Level<span class="text-info"> <small>(optional)</small></span></label>
                                                                <input autofocus type="text" class="form-control btn-square" id="job_level" name="job_level" placeholder="Enter job level" value="{{ $resultAgentLead->job_level }}">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="job_role">Job Role<span class="text-info"> <small>(optional)</small></span></label>
                                                                <input autofocus type="text" class="form-control btn-square" id="job_role" name="job_role" placeholder="Enter job role" value="{{ $resultAgentLead->job_role }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="phone_number">Phone Number<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="phone_number" name="phone_number" placeholder="Enter phone number" value="{{ $resultAgentLead->phone_number }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="address_1">Address 1<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="address_1" name="address_1" placeholder="Enter address 1" value="{{ $resultAgentLead->address_1 }}">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="address_2">Address 2<span class="text-info"> <small>(optional)</small></span></label>
                                                                <input type="text" class="form-control btn-square" id="address_2" name="address_2" placeholder="Enter address 2" value="{{ $resultAgentLead->address_2 }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="city">City<span class="text-danger">*</span></label>
                                                                <input autofocus type="text" class="form-control btn-square" id="city" name="city" placeholder="Enter city" value="{{ $resultAgentLead->city }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="state">State<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="state" name="state" placeholder="Enter state" value="{{ $resultAgentLead->state }}">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="zipcode">Zipcode<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="zipcode" name="zipcode" placeholder="Enter zipcode" value="{{ $resultAgentLead->zipcode }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="country">Country<span class="text-danger">*</span></label>
                                                                <input autofocus type="text" class="form-control btn-square" id="country" name="country" placeholder="Enter country" value="{{ $resultAgentLead->country }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="industry">Industry<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="industry" name="industry" placeholder="Enter industry" value="{{ $resultAgentLead->industry }}">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="employee_size">Employee/Company Size<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="employee_size" name="employee_size" placeholder="Enter size" value="{{ $resultAgentLead->employee_size }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="employee_size_2">Employee/Company Size 2 <span class="text-info"> <small>(optional)</small></span></label>
                                                                <input type="text" class="form-control btn-square" id="employee_size_2" name="employee_size_2" placeholder="Enter size" value="{{ $resultAgentLead->employee_size_2 }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="revenue">Revenue<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="revenue" name="revenue" placeholder="Enter revenue" value="{{ $resultAgentLead->revenue }}">
                                                            </div>

                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label for="company_domain">Company Domain<span class="text-danger">*</span></label>
                                                                <input autofocus type="text" class="form-control btn-square" id="company_domain" name="company_domain" placeholder="Enter company domain" value="{{ $resultAgentLead->company_domain }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label for="comment">Comment <span class="text-info"> <small>(optional)</small></span></label>
                                                                <input type="text" class="form-control btn-square" id="comment" name="comment" placeholder="Enter comment" value="{{ $resultAgentLead->comment }}">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label for="website">Website<span class="text-info"> <small>(optional)</small></span></label>
                                                                <input type="text" class="form-control btn-square" id="website" name="website" placeholder="Enter website" value="{{ $resultAgentLead->website }}">
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="company_linkedin_url">Company LinkedIn URL<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="company_linkedin_url" name="company_linkedin_url" placeholder="Enter company linkedin url" value="{{ $resultAgentLead->company_linkedin_url }}">
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="linkedin_profile_link">LinkedIn Profile Link<span class="text-danger">*</span></label>
                                                                <input autofocus type="text" class="form-control btn-square" id="linkedin_profile_link" name="linkedin_profile_link" placeholder="Enter linkedin profile link" value="{{ $resultAgentLead->linkedin_profile_link }}">
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="linkedin_profile_sn_link">LinkedIn Profile SN Link<span class="text-info"> <small>(optional)</small></span></label>
                                                                <input type="text" class="form-control btn-square" id="linkedin_profile_sn_link" name="linkedin_profile_sn_link" placeholder="Enter LinkedIn Profile SN Link" value="{{ $resultAgentLead->linkedin_profile_sn_link }}">
                                                            </div>
                                                        </div>

                                                        <button id="form-lead-edit-submit" type="button" class="btn btn-primary btn-square float-right">Update</button>
                                                    </form>
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
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    @parent
    <!-- page custom Js -->
    <script src="{{ asset('public/js/agent/lead_edit.js?='.time()) }}"></script>
@append


