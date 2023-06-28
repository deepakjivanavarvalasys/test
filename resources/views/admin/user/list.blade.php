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
                                        <h5 class="m-b-10">User Management</h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">User Management</a></li>
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
                                            @include('layouts.alert')
                                            <h5>Users</h5>
                                            <div class="float-right">
                                                <button type="button" class="btn btn-primary btn-square btn-sm" onclick="addUser();"><i class="feather icon-plus"></i>New User</button>
                                            </div>
                                        </div>
                                        <div class="card-block">
                                            <div class="table-responsive">
                                                <table id="table-users" class="display table nowrap table-striped table-hover">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">Login<br>Status</th>
                                                        <th class="text-center">Employee<br>Code</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Department</th>
                                                        <th>Designation</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Created At</th>
                                                        <th class="text-center">Updated At</th>
                                                        <th class="text-center">Action</th>
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

    <div id="modalUser" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="modal-heading">Add new user</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="modal-user-form">
                        <input type="hidden" name="user_id" id="user_id" value="">
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="first_name">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="first_name" name="first_name" placeholder="Enter first name" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="middle_name">Middle Name</label>
                                <input type="text" class="form-control btn-square" id="middle_name" name="middle_name" placeholder="Enter middle name">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="last_name" name="last_name" placeholder="Enter last name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="employee_code">Employee Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="employee_code" name="employee_code" placeholder="Enter employee code" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control btn-square email-suggession" id="email" name="email" placeholder="Enter email address" required>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="reporting_user_id">Reporting to <span class="text-danger">*</span></label>
                                <select class="form-control btn-square" id="reporting_user_id" name="reporting_user_id" required>
                                    <option value="">-- Select User --</option>
                                    @foreach($resultUsers as $user)
                                        <option value="{{$user->id}}">{{ $user->first_name.' '.$user->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="role_id">Role <span class="text-danger">*</span></label>
                                <select class="form-control btn-square" id="role_id" name="role_id" required>
                                    <option value="">-- Select Role --</option>
                                    @foreach($resultRoles as $role)
                                        <option value="{{$role->id}}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="department_id">Department <span class="text-danger">*</span></label>
                                <select class="form-control btn-square" id="department_id" name="department_id" required>
                                    <option value="">-- Select Department --</option>
                                    @foreach($resultDepartments as $department)
                                        <option value="{{$department->id}}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="designation_id">Designation <span class="text-danger">*</span></label>
                                <select class="form-control btn-square" id="designation_id" name="designation_id" required>
                                    <option value="">-- Select Designation --</option>
                                    @foreach($resultDesignations as $designation)
                                        <option value="{{$designation->id}}">{{ $designation->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control btn-square" id="status" name="status">
                                    <option value="">-- Select Status --</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-square btn-sm" data-dismiss="modal">Close</button>
                    <button id="modal-form-button-submit" type="button" class="btn btn-primary btn-square btn-sm">Save</button>
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

    <script src="{{ asset('public/js/admin/user.js?='.time()) }}"></script>
    <script src="{{ asset('public/js/extras/email_suggessions.js?='.time()) }}"></script>
@append


