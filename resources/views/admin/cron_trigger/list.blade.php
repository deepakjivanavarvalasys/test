@extends('layouts.master')

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
                                        <h5 class="m-b-10">Developer Tools</h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Cron Triggers</a></li>
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
                                            <h5>Cron Triggers</h5>
                                        </div>
                                        <div class="card-block">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    @if($dailyreportlog_cron_status)
                                                    <button type="button" class="btn btn-outline-dark btn-lg" title="" data-toggle="tooltip" data-original-title="Daily Report Logs" onclick="javascript:confirm('Are you sure to run this cron?') ? window.location.href = '{{ route('admin.cron_trigger.daily_report_log') }}' : void(0);">Daily Report Logs</button>
                                                    @else
                                                    <button type="button" class="btn btn-outline-danger btn-lg" title="" data-toggle="tooltip" data-original-title="Daily Report Logs" onclick="javascript:confirm('Cron Executed Already.!!!\nAre you sure to run this cron manually?') ? window.location.href = '{{ route('admin.cron_trigger.daily_report_log') }}' : void(0);">Daily Report Logs</button>
                                                    @endif
                                                </div>
                                                <div class="col-md-3"></div>
                                                <div class="col-md-3"></div>
                                                <div class="col-md-3"></div>
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
