@extends('layouts.master')

@section('style')
    @parent
    <style>
        .card-customer i {
            width: 45px !important;
            height: 45px !important;
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
                            <input type="date" id="start_date">
                            <input type="date" id="end_date">
                            <!-- [ Main Content ] start -->
                            <div class="row" style="padding: 0px 15px;margin-bottom: 30px;">
                                @foreach($resultCampaignStatuses as $key => $status)
                                    <div class="col-md-4" style="padding: 4px !important;">
                                        <div class="card card-customer shadow" style="margin-bottom: 2px;">
                                            <div class="card-block" style="padding: 10px 25px !important;">
                                                <div class="row align-items-center justify-content-center">
                                                    <div class="col">
                                                        <h2 class="mb-2 f-w-300 lead-counts" id="count-{{$status->slug}}">0</h2>
                                                        <h5 class="text-muted mb-0">{{ $status->name }}</h5>
                                                    </div>
                                                    <div class="col-auto">
                                                        @switch($status->slug)
                                                            @case('live') <i
                                                                class="feather icon-play f-20 text-white bg-success shadow"></i>
                                                            @break
                                                            @case('paused') <i
                                                                class="feather icon-pause f-20 text-white bg-warning shadow"></i>
                                                            @break
                                                            @case('cancelled') <i
                                                                class="feather icon-x f-20 text-white bg-danger shadow"></i>
                                                            @break
                                                            @case('delivered') <i
                                                                class="feather icon-check f-20 text-white bg-info shadow"></i>
                                                            @break
                                                            @case('reactivated') <i
                                                                class="feather icon-refresh-cw f-20 text-white bg-success shadow"></i>
                                                            @break
                                                            @case('shortfall') <i
                                                                class="feather icon-chevrons-down f-20 text-white bg-secondary shadow"></i>
                                                            @break
                                                        @endswitch
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
    <!-- Resources -->
    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

    <!-- dashboard-custom js -->
    <script src="{{ asset('public/js/email_marketing_executive/dashboard.js?='.time()) }}"></script>
@append


