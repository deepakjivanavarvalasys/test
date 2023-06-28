@extends('layouts.master')

@section('title', '| My Profile')

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
                                        <h5 class="m-b-10">User Management</h5>
                                        <div class="card-header-right mb-1" style="float: right;">
                                            <a href="{{ route('manager.dashboard') }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">My Profile</a></li>
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
                                <!-- [ task-detail ] start -->
                                <div class="col-xl-4 col-lg-12 task-detail-right">
                                    <div class="card loction-user">
                                        <div class="card-block p-0">
                                            <div class="row align-items-center justify-content-center">
                                                <div class="col-auto">
                                                    <img class="img-fluid rounded-circle" style="width:80px;" src="@if(!empty($resultUser->profile)){{asset('public/storage/user/'.$resultUser->employee_code.'/profile/'.$resultUser->profile)}}@else{{asset('public/template/assets/images/user/avatar-2.jpg')}}@endif" alt="user-profile">
                                                </div>
                                                <div class="col">
                                                    <h5>{{ $resultUser->full_name }}</h5>
                                                    <span>{{ $resultUser->designation->name }}</span>
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <button type="button" class="btn btn-square btn-info btn-sm" data-toggle="modal" data-target="#modal-upload-profile">Update Profile</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card" style="word-break: break-all;">
                                        <div class="card-header">
                                            <h5>User Details</h5>
                                        </div>
                                        <div class="card-block pt-3">
                                            <div class="row col-md-12 m-0 p-0 border-bottom">
                                                <div class="col-md-5 text-left" style="color: #292c2f;padding: 10px 0 !important;">Email: </div>
                                                <div class="col-md-7 text-right" style="color: #6c757d;padding: 10px 0 !important;">{{ $resultUser->email }}</div>
                                            </div>
                                            <div class="row col-md-12 m-0 p-0 border-bottom">
                                                <div class="col-md-5 text-left" style="color: #292c2f;padding: 10px 0 !important;">Employee Code: </div>
                                                <div class="col-md-7 text-right" style="color: #6c757d;padding: 10px 0 !important;">{{ $resultUser->employee_code }}</div>
                                            </div>
                                            <div class="row col-md-12 m-0 p-0 border-bottom">
                                                <div class="col-md-5 text-left" style="color: #292c2f;padding: 10px 0 !important;">Department: </div>
                                                <div class="col-md-7 text-right" style="color: #6c757d;padding: 10px 0 !important;">{{ $resultUser->department->name }}</div>
                                            </div>
                                            <div class="row col-md-12 m-0 p-0 border-bottom">
                                                <div class="col-md-5 text-left" style="color: #292c2f;padding: 10px 0 !important;">Designation: </div>
                                                <div class="col-md-7 text-right" style="color: #6c757d;padding: 10px 0 !important;">{{ $resultUser->designation->name }}</div>
                                            </div>
                                            <div class="row col-md-12 m-0 p-0">
                                                <div class="col-md-5 text-left" style="color: #292c2f;padding: 10px 0 !important;">Status: </div>
                                                <div class="col-md-7 text-right" style="color: #6c757d;padding: 10px 0 !important;">
                                                    @switch($resultUser->status)
                                                        @case(1) <span @class('text-success')>Active</span> @break
                                                        @case(0) <span @class('text-danger')>Inactive</span> @break
                                                    @endswitch
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-8 col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-3"><i class="fas fa-edit m-r-5"></i> Update your profile</h5>
                                        </div>
                                        <div class="card-block">
                                            <div class="row">
                                                <div class="col-md-8 offset-2">
                                                    <form id="form-profile-edit">
                                                        <div class="row">
                                                            <div class="col-md-12 form-group">
                                                                <label for="first_name">First Name<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="first_name" name="first_name" value="{{ $resultUser->first_name }}" placeholder="Enter first name">
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 form-group">
                                                                <label for="last_name">Last Name<span class="text-danger">*</span></label>
                                                                <input type="text" class="form-control btn-square" id="last_name" name="last_name" value="{{ $resultUser->last_name }}" placeholder="Enter last name">
                                                            </div>
                                                        </div>
                                                        <button id="form-profile-edit-submit" type="button" class="btn btn-primary btn-square float-right">Update</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-3"><i class="fas fa-edit m-r-5"></i> Change Password</h5>
                                        </div>
                                        <div class="card-block">
                                            <div class="row">
                                                <div class="col-md-8 offset-2">
                                                    <form id="form-change-password" method="post" action="{{ route('manager.user.change_password') }}">
                                                        @csrf
                                                        <div class="row">
                                                            <div class="col-md-12 form-group">
                                                                <label for="password">New Password<span class="text-danger">*</span></label>
                                                                <div class="input-group mb-3">
                                                                    <input type="password" class="form-control btn-square" id="password" name="password" placeholder="Enter new password">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text"><i class="feather icon-eye-off" style="cursor: pointer;" onclick="showPassword('password', this);"></i></span>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12 form-group">
                                                                <label for="confirm_password">Confirm Password<span class="text-danger">*</span></label>
                                                                <div class="input-group mb-3">
                                                                    <input type="password" class="form-control btn-square" id="confirm_password" name="confirm_password" placeholder="Confirm password">
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text"><i class="feather icon-eye-off" style="cursor: pointer;" onclick="showPassword('confirm_password', this);"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-dark btn-square float-right">Change Password</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- [ task-detail ] end -->
                            </div>
                            <!-- [ Main Content ] end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-upload-profile" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Profile Picture</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="form-upload-profile" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="profile">Upload Profile <span class="text-danger">*</span><span class="text-info">max:256KB</span></label>
                                    <input type="file" class="form-control-file" id="profile" name="profile" required>
                                </div>
                            </div>
                            <button type="reset" class="btn btn-secondary btn-square float-right" data-dismiss="modal" aria-label="Close">Cancel</button>
                            <button id="form-submit-upload-profile" type="button" class="btn btn-primary btn-square float-right">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    @parent
    <script src="{{ asset('public/js/manager/my_profile.js?='.time()) }}"></script>
@append
