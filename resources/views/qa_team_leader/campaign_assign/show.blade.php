@extends('layouts.master')

@section('stylesheet')
    @parent
    <meta name="ca-qatl-id" content="{{ base64_encode($resultCAQATL->id) }}">
    <meta name="ca-qa-id" content="{{ $resultActiveCAQA ? base64_encode($resultActiveCAQA->id) : NULL }}">

    <!-- footable css -->
    <link rel="stylesheet" href="{{ asset('public/template/') }}/assets/plugins/footable/css/footable.bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('public/template/') }}/assets/plugins/footable/css/footable.standalone.min.css">

    <!-- select2 css -->
    <link rel="stylesheet" href="{{ asset('public/template/assets/plugins/select2/css/select2.min.css') }}">

    <!-- material datetimepicker css -->
    <link rel="stylesheet" href="{{ asset('public/template') }}/assets/plugins/material-datetimepicker/css/bootstrap-material-datetimepicker.css">

    <style>
        .modal {
            z-index: 99999999 !important;
        }
        .dtp{z-index:999999999 !important;}
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
                                        <h5 class="m-b-10">Campaign Assign</h5>
                                        <div class="card-header-right mb-1" style="float: right;">
                                            <a href="{{ route('qa_team_leader.campaign_assign.list') }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('qa_team_leader.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('qa_team_leader.campaign_assign.list') }}">Campaign Assign</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Campaign Details</a></li>
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
                                <div class="col-xl-4 col-md-4 task-detail-right">
                                    <div class="card loction-user">
                                        <div class="card-block p-0">
                                            <div class="row align-items-center justify-content-center">
                                                <div class="col">
                                                    <h5><span class="text-muted">ID: </span><span id="campaign_campaign_id">{{ $resultCampaign->campaign_id }}</span></h5>
                                                    <h6><span><span class="text-muted">Name: </span>{{ $resultCampaign->name }}</span></h6>
                                                    @if($resultCampaign->v_mail_campaign_id)
                                                        <h6><span><span class="text-muted">V-Mail Campaign ID: </span>{{ $resultCampaign->v_mail_campaign_id }}</span></h6>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Campaign Details</h5>
                                        </div>
                                        <div class="card-block">
                                            <h6 class="text-muted f-w-300">Campaign Type: <span class="float-right">{{ $resultCampaign->campaignType->name }}</span></h6>
                                            <div style="border-bottom: 1px solid #e2dada;">&nbsp;</div>
                                            <h6 class="text-muted f-w-300 mt-4">Campaign Filter: <span class="float-right">{{ $resultCampaign->campaignFilter->name }}</span></h6>
                                            <div style="border-bottom: 1px solid #e2dada;">&nbsp;</div>
                                            <h6 class="text-muted f-w-300 mt-4">Country(s): <br><br><span class="float-right">
                                                    @foreach($resultCampaign->countries->pluck('country.name')->toArray() as $country)
                                                        <span class="badge badge-info m-1" style="padding: 5px 15px;">{{$country}}</span>
                                                    @endforeach
                                                </span></h6>
                                            <div style="border-bottom: 1px solid #e2dada;">&nbsp;</div>
                                            <h6 class="text-muted f-w-300 mt-4">Region(s): <br><br><span class="float-right">
                                                    @foreach($resultCampaign->countries->pluck('country.region.name')->unique()->toArray() as $region)
                                                        <span class="badge badge-dark m-1" style="padding: 5px 15px;">{{$region}}</span>
                                                    @endforeach
                                                </span></h6>
                                            <div style="border-bottom: 1px solid #e2dada;">&nbsp;</div>
                                            <h6 class="text-muted f-w-300 mt-4">
                                                Note: <br><br>
                                                <span class="float-right">
                                                    @if(strlen($resultCampaign->note) > 200)
                                                        <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#modal-campaign-note">View Note</button>
                                                    @else
                                                        {!! $resultCampaign->note !!}
                                                    @endif
                                                </span>
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Specifications</h5>
                                        </div>
                                        <div class="card-block task-attachment">
                                            <ul class="media-list p-0" id="specification_ul">
                                                @forelse($resultCampaign->specifications as $specification)
                                                    <li class="media d-flex m-b-15 specification-li">
                                                        <div class="m-r-20 file-attach">
                                                            <i class="far fa-file f-28 text-muted"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <a href="{{ url('public/storage/campaigns/'.$resultCampaign->campaign_id.'/'.rawurlencode($specification->file_name)) }}" class="double-click" target="_blank" download data-toggle="tooltip" data-placement="top" data-original-title="{{ $specification->file_name }}"><span class="m-b-5 d-block text-primary">@if(strlen($specification->file_name) < 30) {{ $specification->file_name }} @else {{ substr($specification->file_name, 0, 27).'...' }} @endif</span></a>
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li class="media d-flex m-b-15">
                                                        <div class="media-body">
                                                            <a href="javascript:void(0);" class="m-b-5 d-block text-warning">No File Attached</a>
                                                        </div>
                                                    </li>
                                                @endforelse

                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-8 col-md-8">

                                    <div class="card">
                                        <div class="card-header">

                                            <h5><i class="fas fa-chart-pie m-r-5"></i> Pacing Details</h5>

                                            <div class="card-header-right">
                                                <div class="btn-group card-option">

                                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </button>
                                                    <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                                        <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                                                        <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                                                        <li class="dropdown-item reload-card"><a href="#!"><i class="feather icon-refresh-cw"></i> reload</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-block">
                                            <div class="table-responsive">
                                                <table class="table m-b-0 f-14 b-solid requid-table">
                                                    <thead>
                                                    <tr class="text-uppercase">
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">Start Date</th>
                                                        <th class="text-center">End Date</th>
                                                        <th class="text-center">Pacing</th>
                                                        <th class="text-center">Allocation</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="text-center text-muted">
                                                    <tr>
                                                        <td><i class="feather icon-plus-square toggle-pacing-details" style="cursor: pointer;font-size: 17px;"></i></td>
                                                        <td>{{ date('d-M-Y', strtotime($resultCAQATL->campaign->start_date)) }}</td>
                                                        <td>{{ date('d-M-Y', strtotime($resultCAQATL->display_date)) }}</td>
                                                        <td>{{ ucfirst($resultCAQATL->campaign->pacing) }}</td>
                                                        <td>
                                                            {{ $resultCAQATL->campaign->allocation }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $campaign_type = '';
                                                                if($resultCAQATL->campaign->type == 'incremental') {
                                                                    $campaign_type = ' (Incremental)';
                                                                }
                                                            @endphp
                                                            @switch($resultCAQATL->campaign->campaign_status_id)
                                                                @case(1)
                                                                <span class="badge badge-pill badge-success" style="padding: 5px;min-width: 70px;">Live{{ $campaign_type }}</span>
                                                                @break
                                                                @case(2)
                                                                <span class="badge badge-pill badge-warning" style="padding: 5px;min-width: 70px;">Paused{{ $campaign_type }}</span>
                                                                @break
                                                                @case(3)
                                                                <span class="badge badge-pill badge-danger" style="padding: 5px;min-width: 70px;">Cancelled{{ $campaign_type }}</span>
                                                                @break
                                                                @case(4)
                                                                <span class="badge badge-pill badge-primary" style="padding: 5px;min-width: 70px;">Delivered{{ $campaign_type }}</span>
                                                                @break
                                                                @case(5)
                                                                <span class="badge badge-pill badge-success" style="padding: 5px;min-width: 70px;">Reactivated{{ $campaign_type }}</span>
                                                                @break
                                                                @case(6)
                                                                <span class="badge badge-pill badge-secondary" style="padding: 5px;min-width: 80px;">Shortfall{{ $campaign_type }}</span>
                                                                @break
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            <a href="javascript:;" onclick="viewAssignmentDetails('{{ base64_encode($resultCAQATL->id) }}');" class="btn btn-outline-primary btn-sm btn-rounded mb-0" title="view assignment details" style="padding: 5px 8px;"><i class="feather icon-eye mr-0"></i></a>
                                                        </td>
                                                    </tr>
                                                    <tr class="pacing-details" style="display: none;">
                                                        <td colspan="7" class="bg-light text-left">
                                                            <table class="table table-hover foo-table text-center">
                                                                <thead>
                                                                <tr>
                                                                    <th class="text-center" data-breakpoints="xs">Date</th>
                                                                    <th class="text-center" data-breakpoints="xs">Day</th>
                                                                    <th class="text-center" data-breakpoints="xs">Sub-Allocation</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @forelse($resultCampaign->pacingDetails as $subAllocation)
                                                                    @if($subAllocation->sub_allocation)
                                                                        <tr>
                                                                            <td>{{ date('d-M-Y', strtotime($subAllocation->date)) }}</td>
                                                                            <td>{{ date('D', strtotime($subAllocation->date)) }}</td>
                                                                            <td>{{ $subAllocation->sub_allocation }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="3">Sub allocations not updated.</td>
                                                                    </tr>
                                                                @endforelse
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        @if(empty($resultCAQATL->submitted_at) && !empty($resultCAQATL->quality_analysts->count()))
                                            @foreach($resultCAQATL->quality_analysts as $ca_quality_analyst)
                                                @if(!empty($ca_quality_analyst->submitted_at) &&  !empty($ca_quality_analyst->file_name) && $submitFlag = 1)
                                                <div id="div-download-delivery-file" class="col-md-3">
                                                    <a href="{{ url('public/storage/campaigns/'.$resultCAQATL->campaign->campaign_id.'/quality/qa_final/'.rawurlencode($ca_quality_analyst->file_name)) }}">
                                                        <button type="button" class="btn btn-dark btn-sm btn-square w-100"><i class="feather icon-download"></i> Delivery File ({{$ca_quality_analyst->user->full_name}})</button>
                                                    </a>
                                                </div>
                                                @endif
                                            @endforeach
                                        @endif

                                        <div id="div-submit-campaign" class="col-md-3">
                                            <button type="button"  class="btn btn-danger btn-sm btn-square w-100" @if(isset($submitFlag) && $submitFlag) data-toggle="modal" data-target="#modal-submit-campaign" @else disabled title="Campaign not submitted by QA" @endif>Submit Campaign</button>
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

    <div id="modal-view-assignment-details" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Campaign Assignment Details</h5>
                        @if((!isset($resultActiveCAQA) && empty($resultActiveCAQA->id)) || !empty($resultActiveCAQA->submitted_at))
                        <div class="float-right">
                            <a id="button-assign-campaign" href="javascript:void(0);" onclick="assignCampaign();" class="btn btn-outline-dark btn-sm mb-0 float-right" title="Assign Campaign" style="padding: 5px 8px;position: absolute;right: 50px;"><i class="feather icon-user-plus mr-0"></i></a>
                        </div>
                        @endif
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table m-b-0 f-14 b-solid requid-table">
                                <thead>
                                <tr class="text-uppercase">
                                    <th class="text-center">#</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Assigned On</th>
                                    <th class="text-center">Progress Status</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody class="text-center text-muted">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>

    <div id="modal-submit-campaign" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Campaign</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="form-submit-campaign" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="npf_file">Upload Delivery File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file" id="delivery_file" name="delivery_file" required>
                                </div>
                            </div>
                            <button type="reset" class="btn btn-secondary btn-square float-right">Clear</button>
                            <button id="form-submit-campaign-submit" type="button" class="btn btn-primary btn-square float-right">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-assign-campaign" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Campaign</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="form-assign-campaign">
                            <div class="row">
                                <input type="hidden" name="ca_qatl_id" value="{{ base64_encode($resultCAQATL->id) }}">
                                <div class="col-md-12 form-group">
                                    <label for="user_list">Select User</label>
                                    <select class="form-control btn-square p-1 pl-2" id="user_list" name="user_id" style="height: unset;">
                                        <option value=""></option>
                                        @foreach($resultUsers as $user)
                                        <option id="user_list_{{ $user->id }}" value="{{ $user->id }}" data-name="{{ $user->full_name }}">{{ $user->full_name.' - [ '.$user->role->name.' ]' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button id="form-assign-campaign-submit" type="button" class="btn btn-primary btn-square float-right">Assign</button>
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
    <!-- footable Js -->
    <script src="{{ asset('public/template/assets/plugins/footable/js/footable.min.js') }}"></script>
    <!-- select2 Js -->
    <script src="{{ asset('public/template/assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- material datetimepicker Js -->
    <script src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <script src="{{ asset('public/template/assets/plugins/material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
    <!-- Ckeditor js -->
    <script src="{{ asset('public/template/assets/plugins/ckeditor/js/ckeditor.js') }}"></script>

    <script src="{{ asset('public/js/qa_team_leader/campaign_assign_show.js?='.time()) }}"></script>
@append
