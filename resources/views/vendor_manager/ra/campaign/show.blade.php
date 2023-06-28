@extends('layouts.master')

@section('stylesheet')
    @parent
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
                                        <h5 class="m-b-10">My Campaings</h5>
                                        <div class="card-header-right mb-1" style="float: right;">
                                            <a href="{{ route('vendor_manager.ra.campaign.list') }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('vendor_manager.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('vendor_manager.ra.campaign.list') }}">My Campaigns</a></li>
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

                                    <div class="card" style="word-break: break-all;">
                                        <div class="card-block pt-3 pb-0">
                                            <div class="row col-md-12 m-0 p-0 border-bottom">
                                                <div class="col-md-5 text-left" style="color: #292c2f;padding: 10px 0 !important;">ID: </div>
                                                <div class="col-md-7 text-right" style="color: #292c2f;padding: 10px 0 !important;">
                                                    <span id="campaign_campaign_id">{{ $resultCampaign->campaign_id }}</span>
                                                </div>
                                            </div>
                                            @if($resultCampaign->v_mail_campaign_id)
                                                <div class="row col-md-12 m-0 p-0 border-bottom">
                                                    <div class="col-md-5 text-left" style="color: #292c2f;padding: 10px 0 !important;">Name: </div>
                                                    <div class="col-md-7 text-right" style="color: #6c757d;padding: 10px 0 !important;">{{ $resultCampaign->name }}</div>
                                                </div>
                                                <div class="row col-md-12 m-0 p-0">
                                                    <div class="col-md-5 text-left" style="color: #292c2f;padding: 10px 0 !important;">V-Mail Campaign ID: </div>
                                                    <div class="col-md-7 text-right" style="color: #6c757d;padding: 10px 0 !important;">{{ $resultCampaign->v_mail_campaign_id }}</div>
                                                </div>
                                            @else
                                                <div class="row col-md-12 m-0 p-0">
                                                    <div class="col-md-5 text-left" style="color: #292c2f;padding: 10px 0 !important;">Name: </div>
                                                    <div class="col-md-7 text-right" style="color: #6c757d;padding: 10px 0 !important;">{{ $resultCampaign->name }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-header">
                                            <h5>Campaign Details</h5>
                                        </div>
                                        <div class="card-block pt-3">
                                            <div class="row col-md-12 m-0 p-0 border-bottom">
                                                <div class="col-md-5 text-left" style="color: #292c2f;padding: 10px 0 !important;">Campaign Type: </div>
                                                <div class="col-md-7 text-right" style="color: #6c757d;padding: 10px 0 !important;">{{ $resultCampaign->campaignType->name }}</div>
                                            </div>
                                            <div class="row col-md-12 m-0 p-0 border-bottom">
                                                <div class="col-md-5 text-left" style="color: #292c2f;padding: 10px 0 !important;">Campaign Filter: </div>
                                                <div class="col-md-7 text-right" style="color: #6c757d;padding: 10px 0 !important;">{{ $resultCampaign->campaignFilter->name }}</div>
                                            </div>
                                            <div class="row col-md-12 m-0 p-0 border-bottom">
                                                <div class="col-md-12 text-left" style="color: #292c2f;padding: 10px 0 5px 0 !important;">Country(s): </div>
                                                <div class="col-md-12 text-right" style="color: #6c757d;padding: 0 0 10px 0 !important;">
                                                    @foreach($resultCampaign->countries->pluck('country.name')->toArray() as $country)
                                                        <span class="badge badge-info m-1" style="padding: 5px 15px;">{{$country}}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="row col-md-12 m-0 p-0 border-bottom">
                                                <div class="col-md-12 text-left" style="color: #292c2f;padding: 10px 0 5px 0 !important;">Region(s): </div>
                                                <div class="col-md-12 text-right" style="color: #6c757d;padding: 0 0 10px 0 !important;">
                                                    @foreach($resultCampaign->countries->pluck('country.region.name')->unique()->toArray() as $region)
                                                        <span class="badge badge-dark m-1" style="padding: 5px 15px;">{{$region}}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="row col-md-12 m-0 p-0">
                                                <div class="col-md-12 text-left" style="color: #292c2f;padding: 10px 0 5px 0 !important;">Note: </div>
                                                <div class="col-md-12 text-right" style="color: #6c757d;padding: 0 !important;">
                                                    @if(strlen($resultCampaign->note) > 200)
                                                        <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#modal-campaign-note">View Note</button>
                                                    @else
                                                        {!! $resultCampaign->note !!}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Specifications</h5>
                                            <div class="card-header-right">
                                            </div>
                                        </div>
                                        <div class="card-block task-attachment">
                                            <ul class="media-list p-0" id="specification_ul">
                                                @php $flag = 0; @endphp
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
                                                    @php $flag = 1; @endphp
                                                @endforelse

                                                @isset($resultCampaignParent)
                                                @forelse($resultCampaignParent->specifications as $specification)
                                                    @php $flag = 0; @endphp
                                                    <li class="media d-flex m-b-15 specification-li">
                                                        <div class="m-r-20 file-attach">
                                                            <i class="far fa-file f-28 text-muted"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <a href="{{ url('public/storage/campaigns/'.$resultCampaignParent->campaign_id.'/'.rawurlencode($specification->file_name)) }}" class="double-click" target="_blank" download data-toggle="tooltip" data-placement="top" data-original-title="{{ $specification->file_name }}"><span class="m-b-5 d-block text-primary">@if(strlen($specification->file_name) < 30) {{ $specification->file_name }} @else {{ substr($specification->file_name, 0, 27).'...' }} @endif</span></a>
                                                        </div>
                                                    </li>
                                                @empty
                                                    @php $flag = 1; @endphp
                                                @endforelse
                                                @endisset

                                                @if($flag)
                                                    <li class="media d-flex m-b-15">
                                                        <div class="media-body">
                                                            <a href="javascript:void(0);" class="m-b-5 d-block text-warning">No File Attached</a>
                                                        </div>
                                                    </li>
                                                @endif

                                            </ul>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header">
                                            <h5>RPF File</h5>
                                        </div>
                                        <div class="card-block task-attachment">
                                            <ul class="media-list p-0" id="rpf_file_ul">
                                                @if(!empty($resultCAAgent->reporting_file))
                                                    <li class="media d-flex m-b-15 rpf-file-li">
                                                        <div class="m-r-20 file-attach">
                                                            <i class="far fa-file f-28 text-muted"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <a href="{{ url('public/storage/campaigns/'.$resultCampaign->campaign_id.'/reporting_file/'.$resultCAAgent->reporting_file) }}" class="double-click" target="_blank" download data-toggle="tooltip" data-placement="top" data-original-title="{{ $resultCAAgent->reporting_file }}"><span class="m-b-5 d-block text-primary">@if(strlen($resultCAAgent->reporting_file) < 30) {{ $resultCAAgent->reporting_file }} @else {{ substr($resultCAAgent->reporting_file, 0, 27).'...' }} @endif</span></a>
                                                        </div>
                                                    </li>
                                                @else
                                                    <li class="media d-flex m-b-15">
                                                        <div class="media-body">
                                                            <a href="javascript:void(0);" class="m-b-5 d-block text-warning">No RPF File Attached</a>
                                                        </div>
                                                    </li>
                                                @endif

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
                                        <div class="card-block" style="padding: 0 10px 10px 10px;">
                                            <div class="table-responsive">
                                                <table class="table m-b-0 f-14 b-solid requid-table">
                                                    <thead>
                                                    <tr class="text-uppercase">
                                                        <th class="text-center">Work<br>Type</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Start Date</th>
                                                        <th class="text-center">End Date</th>
                                                        <th class="text-center">Pacing</th>
                                                        <th class="text-center">Completion</th>
                                                        <th class="text-center">Deliver Count / <br>Allocation</th>
                                                        <th class="text-center">Campaign Status</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="text-center text-muted">
                                                    <tr>
                                                        <td>
                                                            {{ $resultCAAgent->agent_work_type->name }}
                                                        </td>
                                                        <td>
                                                            @switch($resultCAAgent->status)
                                                                @case(0)
                                                                <span class="badge badge-pill badge-warning" style="padding: 5px;min-width: 70px;">Inactive</span>
                                                                @break
                                                                @case(1)
                                                                <span class="badge badge-pill badge-success" style="padding: 5px;min-width: 70px;">Active</span>
                                                                @break
                                                                @case(2)
                                                                <span class="badge badge-pill badge-danger" style="padding: 5px;min-width: 70px;">Revoked</span>
                                                                @break
                                                            @endswitch
                                                        </td>
                                                        <td>{{ date('d-M-Y', strtotime($resultCampaign->start_date)) }}</td>
                                                        <td>{{ date('d-M-Y', strtotime($resultCAAgent->display_date)) }}</td>
                                                        <td>{{ ucfirst($resultCampaign->pacing) }}</td>
                                                        <td>
                                                            @php
                                                                $percentage = ($resultCAAgent->agent_lead_count / $resultCAAgent->allocation) * 100;
                                                                $percentage = number_format($percentage,2,".", "");
                                                                if($percentage == 100) {
                                                                    $color_class = 'bg-success';
                                                                } else {
                                                                    $color_class = 'bg-warning text-dark';
                                                                }
                                                            @endphp
                                                            <div class="progress mb-4" style="height: 20px;border: 1px solid #e2dada;">
                                                                <div class="progress-bar {{ $color_class }}" role="progressbar" aria-valuenow="{{$percentage}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$percentage}}%; font-weight: bolder;">&nbsp;&nbsp;{{$percentage}}%</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            {{ $resultCAAgent->agent_lead_count.' / '.$resultCAAgent->allocation }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $campaign_type = '';
                                                                if($resultCampaign->type == 'incremental') {
                                                                    $campaign_type = ' (Incremental)';
                                                                }
                                                            @endphp
                                                            @switch($resultCampaign->campaign_status_id)
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
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    @if($resultCAAgent->status == 1)
                                    <div class="row mb-4">
                                        @if(empty($resultCAAgent->started_at))
                                        <div id="div-start-campaign" class="col-md-3">
                                            <button type="button" class="btn btn-success btn-sm btn-square w-100" onclick="startCampaign('{{ base64_encode($resultCAAgent->id) }}');">Start Campaign</button>
                                        </div>
                                        @endif

                                        @if(!empty($resultCAAgent->started_at) && empty($resultCAAgent->caratl->submitted_at))
                                            <div id="div-manage-leads" class="col-md-3" @if(!empty($resultCAAgent->submitted_at)) style="display: none;" @endif>
                                                <a href="{{ route('vendor_manager.ra.lead.list', base64_encode($resultCAAgent->id)) }}">
                                                    <button type="button" class="btn btn-primary btn-sm btn-square w-100">Manage Leads</button>
                                                </a>
                                            </div>
                                            @if(empty($resultCAAgent->submitted_at))

                                                @if($countAgentData)
                                                    <div class="col-md-3">
                                                        <a href="{{ route('vendor_manager.ra.data.list', base64_encode($resultCAAgent->id)) }}">
                                                            <button type="button" class="btn btn-info btn-sm btn-square w-100">View Data</button>
                                                        </a>
                                                    </div>
                                                @endif

                                                <div id="div-submit-campaign"  class="col-md-3">
                                                    <button type="button" class="btn btn-danger btn-sm btn-square w-100" data-toggle="modal" data-target="#modal-submit-campaign" @if($resultCAAgent->agent_lead_count < 1) disabled @endif>Submit Campaign</button>
                                                </div>
                                                <div id="div-raise-issue" class="col-md-3">
                                                    <button type="button" class="btn btn-warning btn-sm btn-square w-100" data-toggle="modal" data-target="#modal-raise-issue">Raise Issue</button>
                                                </div>
                                            @else
                                                <div id="div-start-again-campaign" class="col-md-3">
                                                    <button type="button" class="btn btn-success btn-sm btn-square w-100" onclick="startAgainCampaign('{{ base64_encode($resultCAAgent->id) }}');">Restart Campaign</button>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    @endif

                                    @if(isset($resultCampaignIssues) && $resultCampaignIssues->count())
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="fas fa-info-circle m-r-5"></i> Campaign Issue(s)</h5>
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
                                        <div class="card-block" style="padding: 0 10px 10px 10px;">
                                            <div class="table-responsive">
                                                <table class="table m-b-0 f-14 b-solid requid-table">
                                                    <thead>
                                                    <tr class="text-uppercase">
                                                        <th class="text-center">Priority</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Title</th>
                                                        <th class="text-center">Description</th>
                                                        <th class="text-center">Created At</th>
                                                        <th class="text-center">Response</th>
                                                        <th class="text-center">Closed By</th>
                                                        <th class="text-center">Closed At</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="text-center text-muted">
                                                    @foreach($resultCampaignIssues as $key => $campaign_issue)
                                                    <tr>
                                                        <td>
                                                            @switch($campaign_issue->priority)
                                                                @case('low') <span class="badge badge-pill badge-info" style="padding: 5px;min-width: 70px;">Low</span> @break;
                                                                @case('normal') <span class="badge badge-pill badge-warning" style="padding: 5px;min-width: 70px;">Normal</span> @break;
                                                                @case('high') <span class="badge badge-pill badge-danger" style="padding: 5px;min-width: 70px;">High</span> @break;
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            @switch($campaign_issue->status)
                                                                @case(0) <span class="badge badge-pill badge-warning" style="padding: 5px;min-width: 70px;">Open</span> @break;
                                                                @case(1) <span class="badge badge-pill badge-success" style="padding: 5px;min-width: 70px;">Closed</span> @break;
                                                            @endswitch
                                                        </td>
                                                        <td>{{ $campaign_issue->title }}</td>
                                                        <td>{{ $campaign_issue->description }}</td>
                                                        <td>{{ date('d/M/Y H:i:s', strtotime($campaign_issue->created_at)) }}</td>
                                                        <td>@if(!empty($campaign_issue->response)) {{ $campaign_issue->response }} @else - @endif</td>
                                                        <td>@if(!empty($campaign_issue->closed_by)) {{ $campaign_issue->closed_by_user->full_name }} @else - @endif</td>
                                                        <td>@if(!empty($campaign_issue->closed_by)) {{ date('d/M/Y H:i:s', strtotime($campaign_issue->updated_at)) }} @else - @endif</td>

                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

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

    <div id="modal-campaign-note" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="background: rgba(0, 0, 0, 0.7) !important;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Campaign Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12" style="font-size: 17px;">
                        {!! $resultCampaign->note !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-raise-issue" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Raise Issue</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="form-raise-issue" action="{{ route('vendor_manager.ra.campaign_issue.store') }}" method="post">
                            @csrf
                            <input type="hidden" name="campaign_id" value="{{ base64_encode($resultCampaign->id) }}">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="description">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" placeholder="Enter description" required row="3"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="priority">Priority <span class="text-danger">*</span></label>
                                    <select class="form-control btn-square" id="priority" name="priority">
                                        <option value="low">Low</option>
                                        <option value="normal">Normal</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                            </div>
                            <button id="form-raise-issue-submit" type="submit" class="btn btn-primary btn-square float-right">Raise Issue</button>
                            <button type="reset" class="btn btn-secondary btn-square float-right" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
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
                        <form id="form-submit-campaign" method="post">
                            @csrf
                            <input type="hidden" name="ca_agent_id" value="{{ base64_encode($resultCAAgent->id) }}">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label>Are you sure to submit campaign ?</label>
                                </div>
                            </div>
                            @if($resultCAAgent->agent_work_type->slug == 'abm')
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="accounts_utilized">Account Utilized <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="accounts_utilized" name="accounts_utilized" placeholder="Enter accounts utilized count" required>
                                </div>
                            </div>
                            @endif
                            <button id="form-submit-campaign-submit" type="submit" class="btn btn-primary btn-square float-right">Submit</button>
                            <button type="reset" class="btn btn-secondary btn-square float-right" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('javascript')
<!-- footable Js -->
    <script src="{{ asset('public/template/assets/plugins/footable/js/footable.min.js') }}"></script>
    <!-- select2 Js -->
    <script src="{{ asset('public/template/assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- material datetimepicker Js -->
    <script src="{{ asset('public/template/assets/plugins/material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
    <!-- Ckeditor js -->
    <script src="{{ asset('public/template/assets/plugins/ckeditor/js/ckeditor.js') }}"></script>
    <!-- jquery-validation Js -->
    <script src="{{ asset('public/template/assets/plugins/jquery-validation/js/jquery.validate.min.js') }}"></script>
    <!-- Page Custom Js -->
    <script src="{{ asset('public/js/vendor_manager/ra/campaign_show.js?='.time()) }}"></script>
@append
