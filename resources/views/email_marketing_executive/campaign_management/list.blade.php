@extends('layouts.master')

@section('stylesheet')
    @parent
    <!-- data tables css -->
    <link rel="stylesheet" href="{{asset('public/template/assets/plugins/data-tables/css/datatables.min.css')}}">
    <!-- toolbar css -->
    <link rel="stylesheet" href="{{asset('public/template/assets/plugins/toolbar/css/jquery.toolbar.css')}}">

    <!-- campaign table custom css -->
    <link rel="stylesheet" href="{{asset('public/css/campaign_table_custom.css')}}">

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
                                        <li class="breadcrumb-item"><a href="{{ route('email_marketing_executive.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Campaign Management</a></li>
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
                                    @include('blocks.campaign_filter', $dataFilter)
                                    <div class="card">
                                        <div class="card-header pb-2">
                                            <h5>Campaigns</h5>
                                            <div class="float-right">
                                                <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#modal-import-campaigns"><i class="feather icon-upload mr-0"></i></button>
                                                <button type="button" class="btn btn-primary btn-square btn-sm" onclick="window.location.href='{{ route('email_marketing_executive.campaign_management.create') }}'"><i class="feather icon-plus"></i>New Campaign</button>
                                            </div>
                                        </div>
                                        <div class="card-block pt-3">
                                            <div class="table-responsive">
                                                <table id="table-campaigns" class="display table nowrap table-striped table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>Campaign ID</th>
                                                        <th>Name</th>
                                                        <th class="text-center">Current<br>Status</th>
{{--                                                        <th class="text-center">Completion (%)</th>--}}
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Allocation</th>
                                                        <th class="text-center">Status</th>
                                                        <th>Action</th>
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

    <div id="modal-import-campaigns" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form-import-campaigns" method="post" action="" enctype="multipart/form-data">

                    <div class="modal-header">
                        <h5 class="modal-title">Import Campaign(s)</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Select excel file <span class="text-danger">*</span></label>
                                <div class="float-right">
                                    <button type="button" class="btn btn-outline-dark btn-square btn-sm p-1 pl-2 pr-2" style="font-size: 11px" onclick="downloadSampleFile('import-campaign.xlsx');" download><i class="feather icon-download"></i>Download Sample</button>
                                </div>
                                <br><br>
                                <input type="file" class="form-control" id="campaign_file" name="campaign_file" required>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12 form-group">
                                <label>Select zip file (specification)</label>
                                <input type="file" class="form-control" id="specification_file" name="specification_file">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button id="form-import-campaigns-submit" type="button" class="btn btn-primary">Import</button>
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
    <!-- toolbar Js -->
    <script src="{{ asset('public/template/assets/plugins/toolbar/js/jquery.toolbar.min.js') }}"></script>

    <script src="{{ asset('public/blocks/campaign_filter/custom.js?='.time()) }}"></script>

    <script src="{{ asset('public/js/email_marketing_executive/campaign_management.js?='.time()) }}"></script>
@append


