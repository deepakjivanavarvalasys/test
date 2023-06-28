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
                                        <h5 class="m-b-10">Campaign Assign</h5>
                                        <div class="card-header-right mb-1" style="float: right;">
                                            <a href="{{ route('team_leader.campaign_assign.list') }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('team_leader.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('team_leader.campaign_assign.list') }}">Campaign Assign</a></li>
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

                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Campaign Files</h5>
                                            <br><small>Suppression/Target List</small>
                                        </div>
                                        <div class="card-block task-attachment">
                                            <ul class="media-list p-0" id="campaign-file-ul">
                                                @if($resultCampaign->campaignFiles->count())
                                                    @foreach($resultCampaign->campaignFiles as $campaignFile)
                                                        <li class="media d-flex m-b-15 campaign-file-li">
                                                            <div class="m-r-20 file-attach">
                                                                <i class="far fa-file f-28 text-muted"></i>
                                                            </div>
                                                            <div class="media-body">
                                                                <a href="{{ url('public/storage/campaigns/'.$resultCampaign->campaign_id.'/'.rawurlencode($campaignFile->file_name)) }}" class="double-click" target="_blank" download data-toggle="tooltip" data-placement="top" data-original-title="{{ $campaignFile->file_name }}"><span class="m-b-5 d-block text-primary">@if(strlen($campaignFile->file_name) < 30) {{ $campaignFile->file_name }} @else {{ substr($campaignFile->file_name, 0, 27).'...' }} @endif</span></a>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                @else
                                                    <li class="media d-flex m-b-15">
                                                        <div class="media-body">
                                                            <a href="javascript:void(0);" class="m-b-5 d-block text-warning">No File Attached</a>
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
                                        <div class="card-block">
                                            <div class="table-responsive">
                                                <table class="table m-b-0 f-14 b-solid requid-table">
                                                    <thead>
                                                    <tr class="text-uppercase">
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">Assign Status</th>
                                                        <th class="text-center">Start Date</th>
                                                        <th class="text-center">End Date</th>
                                                        <th class="text-center">Pacing</th>
                                                        <th class="text-center">Completion</th>
                                                        <th class="text-center">Deliver Count / <br>Allocation</th>
                                                        <th class="text-center">Status</th>
                                                        @if(empty($resultCARATL->submitted_at))
                                                        <th class="text-center">Action</th>
                                                        @endif
                                                    </tr>
                                                    </thead>
                                                    <tbody class="text-center text-muted">
                                                    <tr>
                                                        <td><i class="feather icon-plus-square toggle-pacing-details" style="cursor: pointer;font-size: 17px;"></i></td>
                                                        <td>
                                                            @switch($resultCARATL->status)
                                                                @case(0)
                                                                <span class="badge badge-warning" style="padding: 5px;min-width: 70px;">Inactive</span>
                                                                @break
                                                                @case(1)
                                                                <span class="badge badge-success" style="padding: 5px;min-width: 70px;">Active</span>
                                                                @break
                                                                @case(2)
                                                                <span class="badge badge-danger" style="padding: 5px;min-width: 70px;">Revoked</span>
                                                                @break
                                                            @endswitch
                                                        </td>
                                                        <td>{{ date('d-M-Y', strtotime($resultCARATL->campaign->start_date)) }}</td>
                                                        <td>{{ date('d-M-Y', strtotime($resultCARATL->display_date)) }}</td>
                                                        <td>{{ ucfirst($resultCARATL->campaign->pacing) }}</td>
                                                        <td>
                                                            @php
                                                                $percentage = ($resultCARATL->agent_lead_total_count/$resultCARATL->allocation)*100;
                                                                $percentage = number_format($percentage,2,".", "");
                                                                if($percentage >= 100) {
                                                                    $color_class = 'bg-success';
                                                                } else {
                                                                    $color_class = 'bg-warning text-dark';
                                                                }
                                                            @endphp
                                                            <div class="progress mb-4" style="height: 20px;border: 1px solid #e2dada;">
                                                                <div class="progress-bar {{ $color_class }}" role="progressbar" aria-valuenow="{{$percentage}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$percentage}}%; font-weight: bolder;">{{$percentage}}%</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($resultCARATL->campaign->campaign_status_id === 6)
                                                                {{ $resultCARATL->campaign->deliver_count }} <span class="text-danger" title="Shortfall Count">({{ $resultCARATL->campaign->shortfall_count }})</span> / {{ $resultCARATL->allocation }}
                                                            @else
                                                                {{ $resultCARATL->agent_lead_total_count.' / '.$resultCARATL->allocation }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                $campaign_type = '';
                                                                if($resultCARATL->campaign->type == 'incremental') {
                                                                    $campaign_type = ' (Incremental)';
                                                                }
                                                            @endphp
                                                            @switch($resultCARATL->campaign->campaign_status_id)
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
                                                        @if(empty($resultCARATL->submitted_at))
                                                        <td>
                                                            <a href="javascript:;" onclick="viewAssignmentDetails('{{ base64_encode($resultCARATL->id) }}');" class="btn btn-outline-primary btn-sm btn-rounded mb-0" title="view assignment details" style="padding: 5px 8px;"><i class="feather icon-eye mr-0"></i></a>
                                                        </td>
                                                        @endif
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

                                    <div class="card">
                                        <div class="card-header">

                                            <h5><i class="fas fa-users m-r-5"></i> Campaign Agents</h5>

                                            <div class="card-header-right">
                                                <div class="btn-group card-option">

                                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </button>
                                                    <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                                        <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                                                        <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-block" style="padding: 0 10px 10px 10px;">
                                            <div class="table-responsive">
                                                <table id="table-agents" class="table m-b-0 f-14 b-solid requid-table">
                                                    <thead>
                                                    <tr class="text-uppercase">
                                                        <th class="text-center">Name</th>
                                                        <th class="text-center">Completion</th>
                                                        <th class="text-center">Deliver Count / <br>Allocation</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="text-center text-muted">
                                                    @php
                                                        $total_agents = $resultCARATL->agents->count();
                                                        $total_submitted = 0;
                                                    @endphp
                                                    @forelse($resultCARATL->agents as $ca_agent)
                                                        <tr>
                                                            <td>{{ $ca_agent->user->full_name }}</td>
                                                            <td>
                                                                @php
                                                                    $allocation = $ca_agent->allocation < 1 ? 1 : $ca_agent->allocation;
                                                                    $percentage = ($ca_agent->agent_lead_count/$allocation)*100;
                                                                    $percentage = number_format($percentage,2,".", "");
                                                                    if($percentage >= 100) {
                                                                        $color_class = 'bg-success';
                                                                    } else {
                                                                        $color_class = 'bg-warning text-dark';
                                                                    }
                                                                @endphp
                                                                <div class="progress" style="height: 20px;border: 1px solid #e2dada;">
                                                                    <div class="progress-bar {{ $color_class }}" role="progressbar" aria-valuenow="{{$percentage}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$percentage}}%; font-weight: bolder;">{{$percentage}}%</div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                {{ $ca_agent->agent_lead_count.' / '.$ca_agent->allocation }}
                                                            </td>
                                                            <td>
                                                                @if($ca_agent->status == 2)
                                                                    <span class="badge badge-pill badge-danger" style="padding: 5px;min-width: 70px;">Campaign Revoked</span>
                                                                @else
                                                                    @if(empty($ca_agent->started_at) && empty($ca_agent->submitted_at))
                                                                        <span class="badge badge-pill badge-danger" style="padding: 5px;min-width: 70px;">Campaign Assigned</span>
                                                                    @else
                                                                        @if(!empty($ca_agent->started_at) && empty($ca_agent->submitted_at))
                                                                            <span class="badge badge-pill badge-warning" style="padding: 5px;min-width: 70px;">Campaign In Progress</span>
                                                                        @else
                                                                            @php $total_submitted++; @endphp
                                                                            <span class="badge badge-pill badge-success" style="padding: 5px;min-width: 70px;">Campaign Submit</span>
                                                                        @endif
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if(!empty($ca_agent->started_at))
                                                                    <a href="javascript:;" onclick="viewAgentLeadDetails('{{ base64_encode($ca_agent->id) }}');" class="btn btn-outline-primary btn-sm btn-rounded mb-0" title="view agent lead details" style="padding: 5px 8px;"><i class="feather icon-eye mr-0"></i></a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                    @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        @if(empty($resultCARATL->submitted_at) && empty($resultCAQATL->submitted_at))
                                            <div id="div-manage-leads" class="col-md-3">
                                                <a href="{{ route('team_leader.lead.list', base64_encode($resultCARATL->id)) }}">
                                                    <button type="button" class="btn btn-primary btn-sm btn-square w-100">Manage Leads</button>
                                                </a>
                                            </div>
                                            <div id="div-submit-campaign" class="col-md-3">
                                                <button type="button" @if($total_submitted < $total_agents) class="btn btn-danger btn-sm btn-square w-100" disabled title="Campaign Not Submitted By All Agents!" @else class="btn btn-success btn-sm btn-square w-100" @endif onclick="submitCampaign('{{ base64_encode($resultCARATL->id) }}');">Submit Campaign</button>
                                            </div>
                                            <div id="div-send-for-quality" class="col-md-3">
                                                <button type="button" class="btn btn-dark btn-sm btn-square w-100" @if($resultCARATL->agent_lead_total_count != $resultCARATL->count_agent_leads_send_to_qc) onclick="sendForQualityCheck('{{ base64_encode($resultCARATL->id) }}');" @else disabled title="No leads to send for quality check" @endif>Send For QC</button>
                                            </div>
                                            <div id="div-raise-issue" class="col-md-3">
                                                <button type="button" class="btn btn-warning btn-sm btn-square w-100" data-toggle="modal" data-target="#modal-raise-issue">Raise Issue</button>
                                            </div>
                                        @endif
                                    </div>

                                    @if(isset($resultCampaignIssues) && $resultCampaignIssues->count())
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="fas fa-info-circle m-r-5"></i> Camapign Issue(s)</h5>
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
                                                        <th class="text-center">Raise By</th>
                                                        <th class="text-center">Priority</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Title</th>
                                                        <th class="text-center">Description</th>
                                                        <th class="text-center">Created At</th>
                                                        <th class="text-center">Response</th>
                                                        <th class="text-center">Closed By</th>
                                                        <th class="text-center">Closed At</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="text-center text-muted">
                                                    @foreach($resultCampaignIssues as $key => $campaign_issue)
                                                        <tr>
                                                            <td>{{ $campaign_issue->user->full_name }}</td>
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
                                                            <td>{{ date('d/M/Y', strtotime($campaign_issue->created_at)) }}</td>
                                                            <td>@if(!empty($campaign_issue->response)) {{ $campaign_issue->response }} @else - @endif</td>
                                                            <td>@if(!empty($campaign_issue->closed_by)) {{ $campaign_issue->closed_by_user->full_name }} @else - @endif</td>
                                                            <td>@if(!empty($campaign_issue->closed_by)) {{ date('d/M/Y', strtotime($campaign_issue->updated_at)) }} @else - @endif</td>
                                                            <td>
                                                                @if(empty($campaign_issue->closed_by) && $campaign_issue->user_id != Auth::id())
                                                                <a href="javascript:;" onclick="closeCampaignIssue('{{ base64_encode($campaign_issue->id) }}');" class="btn btn-outline-primary btn-sm btn-rounded mb-0" title="Close Issue" style="padding: 5px 8px;"><i class="feather icon-edit mr-0"></i></a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(empty($resultCARATL->submitted_at))
                                    <div id="div-get-data" class="card">
                                        <div class="card-header">
                                            <h5><i class="fas fa-chart-pie m-r-5"></i> Get Data</h5>
                                            <div class="card-header-right">
                                                <div class="btn-group card-option">
                                                    <button type="button" class="btn minimize-card">
                                                        <a href="#!"><span><i class="feather icon-minus"></i></span><span style="display:none"><i class="feather icon-plus"></i></span></a>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-block">
                                            <form id="form-get-data">

                                                <input type="hidden" name="campaign_id" value="{{ base64_encode($resultCampaign->id) }}">
                                                <input type="hidden" name="ca_ratl_id" value="{{ base64_encode($resultCARATL->id) }}">

                                                <div class="row">
                                                    <div class="col-md-6 form-group">
                                                        <label for="filter_job_level">Job Level</label>
                                                        <select class="form-control btn-square p-1 pl-2 select2-multiple" id="filter_job_level" name="job_level[]" style="height: unset;" multiple>
                                                            @forelse($resultFilterJobLevels as $value)
                                                            <option value="{{ $value->job_level }}">{{ $value->job_level }}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label for="filter_job_role">Job Role</label>
                                                        <select class="form-control btn-square p-1 pl-2 select2-multiple" id="filter_job_role" name="job_role[]" style="height: unset;" multiple>
                                                            @forelse($resultFilterJobRoles as $value)
                                                                <option value="{{ $value->job_role }}">{{ $value->job_role }}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label for="filter_employee_size">Employee Size</label>
                                                        <select class="form-control btn-square p-1 pl-2 select2-multiple" id="filter_employee_size" name="employee_size[]" style="height: unset;" multiple>
                                                            @forelse($resultFilterEmployeeSizes as $value)
                                                                <option value="{{ $value->employee_size }}">{{ $value->employee_size }}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label for="filter_revenue">Revenue</label>
                                                        <select class="form-control btn-square p-1 pl-2 select2-multiple" id="filter_revenue" name="revenue[]" style="height: unset;" multiple>
                                                            @forelse($resultFilterRevenues as $value)
                                                                <option value="{{ $value->revenue }}">{{ $value->revenue }}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label for="filter_country">Country</label>
                                                        <select class="form-control btn-square p-1 pl-2 select2-multiple" id="filter_country" name="country[]" style="height: unset;" multiple>
                                                            @forelse($resultFilterCountries as $value)
                                                                <option value="{{ $value->country }}">{{ $value->country }}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                        <label for="filter_state">State</label>
                                                        <select class="form-control btn-square p-1 pl-2 select2-multiple" id="filter_state" name="state[]" style="height: unset;" multiple>
                                                            @forelse($resultFilterStates as $value)
                                                                <option value="{{ $value->state }}">{{ $value->state }}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="float-right">
                                                            <button id="form-get-data-submit" type="button" class="btn btn-primary btn-sm btn-square">Get Data</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card-footer" @if(!$countAgentData) style="display: none;" @endif>
                                            <div id="div-result-get-data" class="row" style="display: none;">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="float-left">
                                                                <h3><span id="result-record-found" class="text-success"></span></h3>
                                                                <h5>Records found.</h5>
                                                            </div>
                                                            <div class="float-right">
                                                                <form id="form-assign-data">
                                                                    <input type="hidden" name="ca_ratl_id" value="{{ base64_encode($resultCARATL->id) }}">
                                                                    <input type="hidden" id="data_ids" name="data_ids" value="">
                                                                    <button id="form-assign-data-submit" type="button" class="btn btn-primary btn-sm btn-square">Assign Data</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h5>Assigned Data: <span id="count-agent-data" class="text-success">{{ $countAgentData }}</span></h5>
                                                </div>
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

    <div id="modal-view-assignment-details" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Campaign Assignment Details</h5>
                        <div class="float-right">
                            <a id="button-assign-campaign" href="javascript:void(0);" data-campaign-id="" data-display-date="" onclick="assignCampaign();" class="btn btn-outline-dark btn-sm mb-0 float-right" title="Assign Campaign" style="padding: 5px 8px;position: absolute;right: 50px;"><i class="feather icon-user-plus mr-0"></i></a>
                        </div>
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
                                    <th class="text-center">End Date</th>
                                    <th class="text-center">Allocation</th>
                                    <th class="text-center">Assigned By</th>
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

    <div id="modal-view-lead-details" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Campaign Lead Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" style="padding-top: 0px !important;">
                    <div class="table-responsive">
                        <table id="table-agent-lead-details" class="table m-b-0 f-14 b-solid requid-table">
                            <thead>
                            <tr class="text-uppercase">
                                <th>#</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Company Name</th>
                                <th>Email Address</th>
                                <th>Phone Number</th>
                                <th>Address 1</th>
                                <th>Address 2</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zipcode</th>
                                <th>Country</th>
                                <th>Employee Size</th>
                                <th>Employee Size 2</th>
                                <th>Revenue</th>
                                <th>Company Domain</th>
                                <th>Website</th>
                                <th>Company LinkedIn URL</th>
                                <th>LinkedIn Profile Link</th>
                                <th>LinkedIn Profile SN Link</th>
                                <th>Comment</th>
                                <th>TL Comment</th>
                                <th>QC Comment</th>
                                <th>Status</th>
                                <th>Date</th>
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

    <div id="modal-raise-issue" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Raise Issue</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="form-raise-issue" action="{{ route('team_leader.campaign_issue.store') }}" method="post">
                            @csrf
                            <input type="hidden" name="campaign_id" value="{{ base64_encode($resultCARATL->campaign_id) }}">
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
                                        <option value="high">high</option>
                                    </select>
                                </div>
                            </div>
                            <button id="form-raise-issue-submit" type="button" class="btn btn-primary btn-square float-right">Raise Issue</button>
                            <button type="reset" class="btn btn-secondary btn-square float-right" data-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            @csrf
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
                                <input type="hidden" name="campaign_id" value="">
                                <input type="hidden" name="display_date" value="">
                                <div class="col-md-12 form-group">
                                    <label for="user_list">Select User(s)</label>
                                    <select class="form-control btn-square p-1 pl-2 select2-multiple" id="user_list" name="user_list[]" style="height: unset;" multiple>
                                        @foreach($resultUsers as $user)
                                            @if(!in_array($user->id, $resultAssignedUsers))
                                                <option id="user_list_{{ $user->id }}" value="{{ $user->id }}" data-name="{{ $user->first_name.' '.$user->last_name }}">{{ $user->first_name.' '.$user->last_name.' - [ '.$user->role->name.' ]' }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="allocation">Allocation</label>
                                    <input type="number" class="form-control" name="allocation" value="0">
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
    <!-- jquery-validation Js -->
    <script src="{{ asset('public/template/assets/plugins/jquery-validation/js/jquery.validate.min.js') }}"></script>

    <script src="{{ asset('public/js/team_leader/campaign_assign_show.js?='.time()) }}"></script>
@append
