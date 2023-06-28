@extends('layouts.master')

@section('stylesheet')
    @parent
    <!-- select2 css -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/plugins/select2/css/select2.min.css') }}">
    <!-- material datetimepicker css -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/plugins/material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">

    <meta name="campaign-id" content="{{ base64_encode($resultCampaign->id) }}">
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
                                        <div class="card-header-right mb-1" style="float: right;">
                                            <a href="{{ route('manager.campaign.show', base64_encode($resultCampaign->id)) }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>

                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('manager.campaign.list') }}">Campaign Management</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('manager.campaign.show', base64_encode($resultCampaign->id)) }}">{{ $resultCampaign->campaign_id }}</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Edit Campaign</a></li>
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
                                            <h5>Edit Campaign Details</h5>
                                        </div>
                                        <div class="card-block">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <form id="form-campaign-edit" method="post" action="{{ route('manager.campaign.update', base64_encode($resultCampaign->id)) }}">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-6 form-group">
                                                                <label for="name">Campaign Name<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="name" name="name" placeholder="Enter campaign name" value="{{ $resultCampaign->name }}">
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="v_mail_campaign_id">V-Mail Campaign ID<span class="text-info"> <small>(Optional)</small></span></label>
                                                                <input type="text" class="form-control btn-square" id="v_mail_campaign_id" name="v_mail_campaign_id" placeholder="Enter v-mail campaign id"  value="{{ $resultCampaign->v_mail_campaign_id }}">
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="campaign_filter_id">Campaign Filter<span class="text-danger">*</span></label>
                                                                <select class="form-control btn-square" id="campaign_filter_id" name="campaign_filter_id">
                                                                    <option value="">-- Select Campaign Filter --</option>
                                                                    @foreach($resultCampaignFilters as $campaign_filter)
                                                                        <option value="{{$campaign_filter->id}}" @if($resultCampaign->campaign_filter_id == $campaign_filter->id) selected @endif>{{ $campaign_filter->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="campaign_type_id">Campaign Type<span class="text-danger">*</span></label>
                                                                <select class="form-control btn-square" id="campaign_type_id" name="campaign_type_id">
                                                                    <option value="">-- Select Campaign Type --</option>
                                                                    @foreach($resultCampaignTypes as $campaign_type)
                                                                        <option value="{{$campaign_type->id}}" @if($resultCampaign->campaign_type_id == $campaign_type->id) selected @endif>{{ $campaign_type->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="country_id">Country(s)<span class="text-danger">*</span></label>
                                                                <select class="form-control btn-square select2-multiple" id="country_id" name="country_id[]" multiple="multiple">
                                                                    @foreach($resultCountries as $country)
                                                                        <option value="{{$country->id}}" data-region-id="{{$country->region_id}}" @if(in_array($country->id, $resultCampaign->countries->pluck('country_id')->toArray())) selected @endif>{{ $country->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6 form-group">
                                                                <label for="region_id">Region(s)<span class="text-danger">*</span></label>
                                                                <select class="form-control btn-square select2-multiple" id="region_id" name="region_id[]" multiple="multiple" disabled>
                                                                    @foreach($resultRegions as $region)
                                                                        <option value="{{$region->id}}" @if(in_array($region->id, $resultCampaign->countries->pluck('country.region.id')->toArray())) selected @endif>{{ $region->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-12 form-group">
                                                                <label for="note">Note</label>
                                                                <textarea id="note" name="note" class="form-control classic-editor" placeholder="Enter note here..." rows="3">
                                                                    {{ $resultCampaign->note }}
                                                                </textarea>
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary btn-square float-right">Update</button>
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
    <!-- jquery-validation Js -->
    <script src="{{ asset('public/template/assets/plugins/jquery-validation/js/jquery.validate.min.js') }}"></script>
    <!-- page custom Js -->
    <script src="{{ asset('public/js/manager/campaign_edit.js?='.time()) }}"></script>
@append


