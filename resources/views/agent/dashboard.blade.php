@extends('layouts.master')

@section('style')
    @parent
    <meta name="user-image-path" content="{{asset('public/storage/user')}}">
    <meta name="user-default-image-path" content="{{ asset('public/template/assets/images/user/avatar-2.jpg') }}">
    <style>
        .form-control-small {
            line-height: 1;
            padding: 1px 20px;
            font-size: 12px;
        }
    </style>
@append

@section('content')
    <div class="pcoded-main-container">
        <div class="pcoded-wrapper">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <!-- [ breadcrumb ] start -->
                    <div class="page-header">
                        <div class="page-block">
                            <div class="row align-items-center">
                                <div class="col-md-12">
                                    <div class="page-header-title">
                                        <h5 class="m-b-10">Dashboard</h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);"><i
                                                    class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
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
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Filter</h5>
                                            <div class="float-right">
                                                <div class="row">
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-8">
                                                        <div class="input-daterange input-group" id="datepicker_range">
                                                            @php
                                                            if(date('H') >= 12 && date('H') <=23) {
                                                                if(date('N') == 1) {
                                                                    $today_shift_date = date('d-m-Y', strtotime('-3 day'));
                                                                } else {
                                                                    $today_shift_date = date('d-m-Y', strtotime('-1 day'));
                                                                }
                                                            }
                                                            else {
                                                                if(date('N') == 2) {
                                                                    $today_shift_date = date('d-m-Y', strtotime('-4 day'));
                                                                } else {
                                                                    $today_shift_date = date('d-m-Y', strtotime('-2 day'));
                                                                }
                                                            }
                                                            @endphp
                                                            <input type="text" class="form-control text-left form-control-small" placeholder="Start date" name="start_date" id="filter_start_date" value="{{ $today_shift_date }}" title="Start Date">
                                                            <input type="text" class="form-control text-right form-control-small" placeholder="End date" name="end_date" id="filter_end_date" value="{{ $today_shift_date }}" title="End Date">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Productivity</h5>
                                        </div>
                                        <div class="card-block text-center" style="height: 270px;">
                                            <div id="chart-gauge-productivity" style="height: 300px;top: -40px;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Quality</h5>
                                        </div>
                                        <div class="card-block text-center" style="height: 270px;">
                                            <div id="chart-gauge-quality" style="height: 300px;top: -40px;"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-block  text-center dial-chart pb-1">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <h5 class="mb-4" style="color: #66ca00;">Campaign Processed</h5>
                                                    <input id="campaign_processed_percentage" style="display: none;" data-fgColor="#66ca00" type="text" class="dial" value="0" data-width="150" data-height="150" data-angleOffset="-125" data-angleArc="250" data-rotation="clockwise" data-readonly="true" data-displayInput="false">
                                                    <input id="campaign_processed_count" style="color:#66ca00;" type="text" class="dial-value" value="0" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5 class="mb-4" style="color: #2a199c;">Leads Generated</h5>
                                                    <input id="leads_generated_percentage" style="display: none;" data-fgColor="#2a199c" type="text" class="dial" value="0" data-width="150" data-height="150" data-angleOffset="-125" data-angleArc="250" data-rotation="clockwise" data-readonly="true" data-displayInput="false">
                                                    <input id="leads_generated_count" style="color:#2a199c;" type="text" class="dial-value" value="0" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5 class="mb-4" style="color: #1afbcf;">Leads Qualified</h5>
                                                    <input id="leads_qualified_percentage" style="display: none;" data-fgColor="#1afbcf" type="text" class="dial" value="0" data-width="150" data-height="150" data-angleOffset="-125" data-angleArc="250" data-rotation="clockwise" data-readonly="true" data-displayInput="false">
                                                    <input id="leads_qualified_count" style="color:#1afbcf;" type="text" class="dial-value" value="0" readonly>
                                                </div>
                                                <div class="col-md-3">
                                                    <h5 class="mb-4" style="color: #c60a2d;">Leads Rejected</h5>
                                                    <input id="leads_rejected_percentage" style="display: none;" data-fgColor="#c60a2d" type="text" class="dial" value="0" data-width="150" data-height="150" data-angleOffset="-125" data-angleArc="250" data-rotation="clockwise" data-readonly="true" data-displayInput="false">
                                                    <input id="leads_rejected_count" style="color:#c60a2d;" type="text" class="dial-value" value="0" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Top Productivity</h5>
                                        </div>
                                        <div class="card-block text-center">
                                            <div id="top_productivity_1" data-label="100%" class="radial-bar radial-bar-100 radial-bar-lg radial-bar-success" title="" data-toggle="tooltip" data-original-title="">
                                                <img src="{{ asset('public/template') }}/assets/images/user/avatar-2.jpg" alt="User-Image">
                                            </div>
                                            <div id="top_productivity_2" data-label="100%" class="radial-bar radial-bar-100 radial-bar-md radial-bar-warning" title="" data-toggle="tooltip" data-original-title="">
                                                <img src="{{ asset('public/template') }}/assets/images/user/avatar-2.jpg" alt="User-Image">
                                            </div>
                                            <div id="top_productivity_3" data-label="100%" class="radial-bar radial-bar-100 radial-bar-sm radial-bar-danger" title="" data-toggle="tooltip" data-original-title="">
                                                <img src="{{ asset('public/template') }}/assets/images/user/avatar-2.jpg" alt="User-Image">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Top Quality</h5>
                                        </div>
                                        <div class="card-block text-center">
                                            <div id="top_quality_1" data-label="100%" class="radial-bar radial-bar-100 radial-bar-lg radial-bar-success" title="" data-toggle="tooltip" data-original-title="">
                                                <img src="{{ asset('public/template') }}/assets/images/user/avatar-2.jpg" alt="User-Image">
                                            </div>
                                            <div id="top_quality_2" data-label="100%" class="radial-bar radial-bar-100 radial-bar-md radial-bar-warning" title="" data-toggle="tooltip" data-original-title="">
                                                <img src="{{ asset('public/template') }}/assets/images/user/avatar-2.jpg" alt="User-Image">
                                            </div>
                                            <div id="top_quality_3" data-label="100%" class="radial-bar radial-bar-100 radial-bar-sm radial-bar-danger" title="" data-toggle="tooltip" data-original-title="">
                                                <img src="{{ asset('public/template') }}/assets/images/user/avatar-2.jpg" alt="User-Image">
                                            </div>
                                        </div>
                                    </div>
                                </div>



                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Counts by Work Type</h5>
                                        </div>
                                        <div class="card-block text-center pb-1 pt-1">
                                            <div id="bar-counts-by-work-type" style="width: 80%; height: 450px;margin: 0 auto;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Leads Generated Counts (Monthly)</h5>
                                            <div class="float-right">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control text-center select2-border form-control-small" id="filter_monthly" name="filter_monthly" value="{{ date('M-Y') }}" style="width: 100px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-block text-center">
                                            <div id="bar-leads-generated-monthly" style="width: 100%; height: 300px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- [ Main Content ] end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    @parent
    <!-- chart-knob Js -->
    <script src="{{ asset('public/template/assets/plugins/chart-knob/js/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('public/template/assets/plugins/chart-knob/js/jquery.knob-custom.min.js') }}"></script>

    <!--echarts chart -->
    <script src="{{ asset('public/template/assets/plugins/chart-echarts/js/echarts-en.min.js') }}"></script>

    <!--echarts chart -->
    <script src="http://echarts.baidu.com/echarts2/doc/example/timelineOption.js"></script>
    <script src="{{ asset('public/template') }}/assets/plugins/chart-echarts/js/echarts-en.min.js"></script>
    <script src="{{ asset('public/js/agent/dashboard.js?='.time()) }}"></script>
@append
