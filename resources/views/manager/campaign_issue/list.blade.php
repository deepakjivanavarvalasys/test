@extends('layouts.master')

@section('stylesheet')
    @parent
    <!-- data tables css -->
    <link rel="stylesheet" href="{{asset('public/template/assets/plugins/data-tables/css/datatables.min.css')}}">
    <!-- toolbar css -->
    <link rel="stylesheet" href="{{asset('public/template/assets/plugins/toolbar/css/jquery.toolbar.css')}}">
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
                                        <h5 class="m-b-10">Campaign Issue Management</h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Campaign Issue Management</a></li>
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
                                        <div class="card-header pb-2">
                                            <h5>Campaign Issues</h5>
                                        </div>
                                            <div class="float-left">
<!--                                                <button type="button" class="btn btn-outline-dark btn-sm" data-toggle="modal" data-target="#modal-import-campaigns"><i class="feather icon-upload mr-0"></i></button>
                                                <button type="button" class="btn btn-primary btn-square btn-sm" onclick="window.location.href='{{ route('manager.campaign.create') }}'"><i class="feather icon-plus"></i>New Campaign</button>-->

                                                {{-- <form action="filterissue" method="POST">
                                                    @csrf --}}
                                                    <label> Select Campaign  Status</label>
                                                <select   id="filterissue">
                                                    <option value="all">All</option>
                                                    <option value="open">Open</option>
                                                    <option value="close">Close</option>
                                                </select>
                                            
                                                {{-- <input type="submit">
                                                
                                                </form>        --}}
                                            </div>
                                        
                                        <div class="card-block pt-3">
                                            <div class="table-responsive">
                                                <table id="table-campaign-issues" class="display table nowrap table-striped table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>Action</th>
                                                        <th>Campaign ID</th>
                                                        <th class="text-center">Priority</th>
                                                        <th class="text-center">Status</th>
                                                        <th>Title</th>
                                                        <th>Description</th>
                                                        <th>Response</th>
                                                        <th>Raised By</th>
                                                        <th>Created At</th>
                                                        <th>Closed By</th>
                                                        <th>Closed At</th>
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

    <div id="modal-close-issue" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Close Issue</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="form-close-issue">
                            <input type="hidden" name="id" value="">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="response">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="response" name="response" placeholder="Enter response" required row="3"></textarea>
                                </div>
                            </div>
                            <button id="form-close-issue-submit" type="button" class="btn btn-primary btn-square float-right">Close Issue</button>
                            <button type="reset" class="btn btn-secondary btn-square float-right" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
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
    <!-- jquery-validation Js -->
    <script src="{{ asset('public/template/assets/plugins/jquery-validation/js/jquery.validate.min.js') }}"></script>

    <script src="{{ asset('public/js/manager/campaign_issue.js?='.time()) }}"></script>
@append


