@extends('layouts.master')

@section('title', '| Agent Work Type Management')

@section('stylesheet')
    @parent
    <!-- data tables css -->
    <link rel="stylesheet" href="{{asset('public/template/assets/plugins/data-tables/css/datatables.min.css')}}">
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
                                        <h5 class="m-b-10">Agent Work Type Managements </h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Campaign Settings</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Agent Work Type Management</a></li>
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
                                            <h5>Agent Work Types</h5>
                                            <div class="float-right">
                                                <button type="button" class="btn btn-primary btn-square btn-sm" onclick="addAgentWorkType();"><i class="feather icon-plus"></i>New Work Type</button>
                                            </div>
                                        </div>
                                        <div class="card-block">
                                            <div class="table-responsive">
                                                <table id="table-agent-work-types" class="display table nowrap table-striped table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Slug</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Created At</th>
                                                        <th class="text-center">Updated At</th>
                                                        <th class="text-center" style="width: 20%;">Action</th>
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

    <div id="modal-agent-work-type" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="modal-heading">Add new work type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="form-agent-work-type">
                        <input type="hidden" name="agent_work_type_id" id="agent_work_type_id" value="">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="name">Campaign Filter Name</label>
                                <input type="text" class="form-control btn-square" id="name" name="name" placeholder="Enter campaign filter name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="status">Status</label>
                                <select class="form-control btn-square" id="status" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-square btn-sm" data-dismiss="modal">Close</button>
                    <button id="form-agent-work-type-submit" type="button" class="btn btn-primary btn-square btn-sm">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    @parent
    <!-- datatable Js -->
    <script src="{{ asset('public/template/assets/plugins/data-tables/js/datatables.min.js') }}"></script>
    <script src="{{ asset('public/js/admin/agent_work_type.js?='.time()) }}"></script>
@append


