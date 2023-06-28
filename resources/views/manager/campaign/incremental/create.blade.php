@extends('layouts.master')

@section('stylesheet')
    @parent
    <!-- select2 css -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/plugins/select2/css/select2.min.css') }}">
    <!-- material datetimepicker css -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/plugins/material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
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
                                        <h5 class="m-b-10">Campaign Management</h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('manager.campaign.list') }}">Campaign Management</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('manager.campaign.show', base64_encode($resultCampaign->id)) }}">{{ $resultCampaign->campaign_id }}</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Add Incremental</a></li>
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
                                            <h5>Add Incremental</h5>
                                        </div>
                                        <div class="card-block">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <form id="form-campaign-create" method="post" action="{{ route('manager.campaign.store') }}" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="parent_id" value="{{ base64_encode($resultCampaign->id) }}">
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label for="name">Campaign Name:</label>
                                                                <h5>{{ $resultCampaign->name }}</h5>
                                                                <input type="hidden" name="name" value="{{ $resultCampaign->name }}">
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="v_mail_campaign_id">V-Mail Campaign ID:</label>
                                                                <h5>{{ $resultCampaign->v_mail_campaign_id ? $resultCampaign->v_mail_campaign_id : 'NA' }}</h5>
                                                                <input type="hidden" name="v_mail_campaign_id" value="{{ $resultCampaign->v_mail_campaign_id }}">
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="campaign_filter_id">Campaign Filter:</label>
                                                                <h5>{{ $resultCampaign->campaignFilter->name }}</h5>
                                                                <input type="hidden" name="campaign_filter_id" value="{{ $resultCampaign->campaign_filter_id }}">
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="campaign_type_id">Campaign Type:</label>
                                                                <h5>{{ $resultCampaign->campaignType->name }}</h5>
                                                                <input type="hidden" name="campaign_type_id" value="{{ $resultCampaign->campaign_type_id }}">
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="country_id">Country(s):</label>
                                                                <br>
                                                                @foreach($resultCampaign->countries->pluck('country.name')->toArray() as $country)
                                                                    <span class="badge badge-info m-1" style="padding: 5px 15px;">{{$country}}</span>
                                                                @endforeach
                                                                @foreach($resultCampaign->countries as $country)
                                                                    <input type="hidden" name="country_id[]" value="{{ $country->country_id }}">
                                                                @endforeach
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="region_id">Region(s)<span class="text-danger">*</span></label>
                                                                <br>
                                                                @foreach($resultCampaign->countries->pluck('country.region.name')->unique()->toArray() as $region)
                                                                    <span class="badge badge-dark m-1" style="padding: 5px 15px;">{{$region}}</span>
                                                                @endforeach
                                                            </div>

                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label for="start_date">Start Date<span class="text-danger">*</span></label>
                                                                <h5>{{ date('d-M-Y', strtotime($resultCampaign->start_date)) }}</h5>
                                                                <input type="hidden" id="start_date" name="start_date" value="{{ date('d-M-Y', strtotime($resultCampaign->start_date)) }}">
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="end_date">End Date<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="end_date" name="end_date" placeholder="Select End Date">
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="allocation">Allocation<span class="text-danger">*</span></label>
                                                                <input type="number" class="form-control btn-square only-non-zero-number" id="allocation" name="allocation" placeholder="Enter allocation">
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="campaign_status_id">Status</label>
                                                                <select class="form-control btn-square" id="campaign_status_id" name="campaign_status_id">
                                                                    @foreach($resultCampaignStatuses as $campaign_status)
                                                                        <option value="{{$campaign_status->id}}">{{ $campaign_status->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 form-group">
                                                                <label for="pacing">Pacing</label>
                                                                <div class="form-control">
                                                                    <div class="form-group d-inline">
                                                                        <div class="radio radio-primary d-inline">
                                                                            <input type="radio" name="pacing" id="pacing_radio_1" value="Daily" class="pacing">
                                                                            <label for="pacing_radio_1" class="cr">Daily</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group d-inline">
                                                                        <div class="radio radio-primary d-inline">
                                                                            <input type="radio" name="pacing" id="pacing_radio_3" value="Weekly" class="pacing">
                                                                            <label for="pacing_radio_3" class="cr">Weekly</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group d-inline">
                                                                        <div class="radio radio-primary d-inline">
                                                                            <input type="radio" name="pacing" id="pacing_radio_2" value="Monthly" class="pacing">
                                                                            <label for="pacing_radio_2" class="cr">Monthly</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="total-sub-allocation">Total Sub-Allocation</label>
                                                                <br>
                                                                <span id="total-sub-allocation" class="h3">0</span><span id="text-allocation" class="h3"> / 0</span>
                                                            </div>

                                                            <div class="col-md-12 row" id="div_pacing_details" style="display: none;">
                                                                <div class="col-md-3 col-sm-12">
                                                                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                                                    </ul>
                                                                </div>
                                                                <div class="col-md-9 col-sm-12">
                                                                    <div class="tab-content" id="v-pills-tabContent">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">

                                                            <div class="col-md-6 form-group">
                                                                <label for="specifications">Specifications</label>
                                                                <input type="file" class="form-control-file" id="specifications" name="specifications[]" multiple>
                                                            </div>

                                                            <div class="col-md-12 form-group">
                                                                <label for="note">Note</label>
                                                                <textarea id="note" name="note" class="form-control classic-editor" placeholder="Enter note here..." rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <button id="form-campaign-create-submit" type="button" class="btn btn-primary btn-square float-right">Save</button>
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
    <!-- select2 Js -->
    <script src="{{ asset('public/template/assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- material datetimepicker Js -->
    <script src="{{ asset('public/template/assets/plugins/material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
    <!-- Ckeditor js -->
    <script src="{{ asset('public/template/assets/plugins/ckeditor/js/ckeditor.js') }}"></script>
    <!-- page custom Js -->
    <script src="{{ asset('public/js/manager/campaign_incremental_create.js?='.time()) }}"></script>
@append


