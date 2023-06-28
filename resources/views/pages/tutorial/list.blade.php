@extends('layouts.master')

@section('title', '| Tutorials')

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
                                        <h5 class="m-b-10">{{ Auth::user()->role->name }} Tutorials</h5>
                                        <div class="card-header-right mb-1" style="float: right;">
                                            <a href="{{ route('manager.dashboard') }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Tutorials</a></li>
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

                                <div class="col-sm-12">
                                    <hr>
                                    <div class="accordion" id="tutorials">
                                        <div class="row">
                                            @forelse($resultTutorials as $tutorial)
                                                <div class="col-md-6">
                                                    <div class="card">
                                                        <div class="card-header" id="heading_{{ $tutorial->id }}">
                                                            <h5 class="mb-0"><a href="javascript:void(0);" class="collapsed" data-toggle="collapse" data-target="#collapse_{{ $tutorial->id }}" aria-expanded="false" aria-controls="collapse_{{ $tutorial->id }}">{{ $tutorial->title }}</a></h5>
                                                        </div>
                                                        <div id="collapse_{{ $tutorial->id }}" class="card-body collapse" aria-labelledby="heading_{{ $tutorial->id }}" data-parent="#tutorials">
                                                            {!! $tutorial->description !!}
                                                            <br><br>
                                                            <a href="{{ $tutorial->link }}" target="_blank">
                                                                <button type="button" class="btn btn-danger btn-square"><i class="feather icon-play-circle"></i> Play</button>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-md-12">
                                                    <div class="alert alert-warning" role="alert">
                                                        Tutorial video not found!
                                                    </div>
                                                </div>
                                            @endforelse
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
