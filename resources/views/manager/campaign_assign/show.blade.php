@extends('layouts.master')
@section('title', '| Campaign Details-'.$resultCampaign->campaign_id)

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
                                            <a href="{{ route('manager.campaign_assign.list') }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('manager.campaign_assign.list') }}">Campaign Assign</a></li>
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
                                                        <th class="text-center">Completion</th>
                                                        <th class="text-center">Deliver Count / <br>Allocation</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="text-center text-muted">
                                                    <tr>
                                                        <td><i class="feather icon-plus-square toggle-pacing-details" style="cursor: pointer;font-size: 17px;"></i></td>
                                                        <td>{{ date('d-M-Y', strtotime($resultCampaign->start_date)) }}</td>
                                                        <td>{{ date('d-M-Y', strtotime($resultCampaign->end_date)) }}</td>
                                                        <td>{{ ucfirst($resultCampaign->pacing) }}</td>
                                                        <td>
                                                            @php
                                                                $percentage = ($resultCampaign->completed_count/$resultCampaign->allocation)*100;
                                                                $percentage = number_format($percentage,2,".", "");
                                                                if($percentage == 100) {
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
                                                            @if($resultCampaign->campaign_status === 6)
                                                                {{ $resultCampaign->completed_count }} <span class="text-danger" title="Shortfall Count">({{ $resultCampaign->shortfall_count }})</span> / {{ $resultCampaign->allocation }}
                                                            @else
                                                                {{ $resultCampaign->completed_count.' / '.$resultCampaign->allocation }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @switch($resultCampaign->campaign_status_id)
                                                                @case(1)
                                                                <span class="badge badge-pill badge-success" style="padding: 5px;min-width: 70px;">Live</span>
                                                                @break
                                                                @case(2)
                                                                <span class="badge badge-pill badge-warning" style="padding: 5px;min-width: 70px;">Paused</span>
                                                                @break
                                                                @case(3)
                                                                <span class="badge badge-pill badge-danger" style="padding: 5px;min-width: 70px;">Cancelled</span>
                                                                @break
                                                                @case(4)
                                                                <span class="badge badge-pill badge-primary" style="padding: 5px;min-width: 70px;">Delivered</span>
                                                                @break
                                                                @case(5)
                                                                <span class="badge badge-pill badge-success" style="padding: 5px;min-width: 70px;">Reactivated</span>
                                                                @break
                                                                @case(6)
                                                                <span class="badge badge-pill badge-secondary" style="padding: 5px;min-width: 80px;">Shortfall</span>
                                                                @break
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            <a href="javascript:;" onclick="viewAssignmentDetails('{{ base64_encode($resultCampaign->id) }}');" class="btn btn-outline-primary btn-sm btn-rounded mb-0" title="view assignment details" style="padding: 5px 8px;"><i class="feather icon-eye mr-0"></i></a>
{{--                                                            <a href="javascript:;" onclick="updateDeliveryDetails('{{ base64_encode($resultCampaign->id) }}');" class="btn btn-outline-dark btn-sm btn-rounded mb-0" title="Update Delivery Details" style="padding: 5px 8px;"><i class="feather icon-edit mr-0"></i></a>--}}
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
                                                                        <td colspan="3">
                                                                            Sub allocations not updated,
                                                                            <br>
                                                                            <a href="javascript:;" onclick="editSubAllocations('{{ base64_encode($resultCampaign->id) }}');" title="Edit Sub-Allocations">Click Here</a> to update.
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    @forelse($resultCampaign->children as $children)
                                                        <tr>
                                                            <td><i class="feather icon-plus-square toggle-pacing-details" style="cursor: pointer;font-size: 17px;"></i></td>
                                                            <td>{{ date('d-M-Y', strtotime($children->start_date)) }}</td>
                                                            <td>{{ date('d-M-Y', strtotime($children->end_date)) }}</td>
                                                            <td>{{ ucfirst($children->pacing) }}</td>
                                                            <td>
                                                                @php
                                                                    $percentage = ($children->completed_count/$children->allocation)*100;
                                                                    $percentage = number_format($percentage,2,".", "");
                                                                    if($percentage == 100) {
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
                                                                @if($children->campaign_status === 6)
                                                                    {{ $children->completed_count }} <span class="text-danger" title="Shortfall Count">({{ $children->shortfall_count }})</span> / {{ $children->allocation }}
                                                                @else
                                                                    {{ $children->completed_count.' / '.$children->allocation }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @switch($children->campaign_status_id)
                                                                    @case(1)
                                                                    <span class="badge badge-pill badge-success" style="padding: 5px;min-width: 70px;">Live (Incremental)</span>
                                                                    @break
                                                                    @case(2)
                                                                    <span class="badge badge-pill badge-warning" style="padding: 5px;min-width: 70px;">Paused (Incremental)</span>
                                                                    @break
                                                                    @case(3)
                                                                    <span class="badge badge-pill badge-danger" style="padding: 5px;min-width: 70px;">Cancelled (Incremental)</span>
                                                                    @break
                                                                    @case(4)
                                                                    <span class="badge badge-pill badge-primary" style="padding: 5px;min-width: 70px;">Delivered (Incremental)</span>
                                                                    @break
                                                                    @case(5)
                                                                    <span class="badge badge-pill badge-success" style="padding: 5px;min-width: 70px;">Reactivated (Incremental)</span>
                                                                    @break
                                                                    @case(6)
                                                                    <span class="badge badge-pill badge-secondary" style="padding: 5px;min-width: 80px;">Shortfall (Incremental)</span>
                                                                    @break
                                                                @endswitch
                                                            </td>
                                                            <td>
                                                                <a href="javascript:;" onclick="viewAssignmentDetails('{{ base64_encode($children->id) }}');" class="btn btn-outline-primary btn-sm btn-rounded mb-0" title="view assignment details" style="padding: 5px 8px;"><i class="feather icon-eye mr-0"></i></a>
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
                                                                    @forelse($children->pacingDetails as $subAllocation)
                                                                        @if($subAllocation->sub_allocation)
                                                                            <tr>
                                                                                <td>{{ date('d-M-Y', strtotime($subAllocation->date)) }}</td>
                                                                                <td>{{ date('D', strtotime($subAllocation->date)) }}</td>
                                                                                <td>{{ $subAllocation->sub_allocation }}</td>
                                                                            </tr>
                                                                        @endif
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="3">
                                                                                Sub allocations not updated,
                                                                                <br>
                                                                                <a href="javascript:;" onclick="editSubAllocations('{{ base64_encode($children->id) }}');" title="Edit Sub-Allocations">Click Here</a> to update.
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                    </tbody>
                                                                </table>
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
                                        <div id="div-update-delivery_details" class="col-md-3">
                                            <button type="button" class="btn btn-dark btn-sm btn-square w-100" onclick="updateDeliveryDetails('{{ base64_encode($resultCampaign->id) }}');" style="padding: 6px 10px;"><i class="feather icon-edit"></i>Delivery Detail</button>
                                        </div>
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
                                                                    @if(empty($campaign_issue->closed_by))
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
                    <div class="modal-body pt-0">
                        <div class="table-responsive">
                            <table class="table m-b-0 f-14 b-solid requid-table">
                                <thead>
                                <tr class="text-uppercase">
                                    <th class="text-center">#</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">End Date</th>
                                    <th class="text-center">Allocation</th>
                                    <th class="text-center">Total Agents / <br>Vendors</th>
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

    <div id="modal-update-delivery-details" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Client Delivery Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="form-update-delivery-details">
                            <input type="hidden" id="campaign_id" name="campaign_id" value="">
                            <input type="hidden" name="id" value="">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="lead_sent">Lead Sent</label>
                                    <input type="number" class="form-control btn-square" id="lead_sent" name="lead_sent" placeholder="Enter lead sent" value="0">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="lead_approved">Lead Approved</label>
                                    <input type="number" class="form-control btn-square" id="lead_approved" name="lead_approved" placeholder="Enter lead approved" value="0">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="lead_available">Lead Available</label>
                                    <input type="number" class="form-control btn-square" id="lead_available" name="lead_available" placeholder="Enter lead available" value="0">
                                </div>
                            </div>
                            <button id="form-update-delivery-details-submit" type="button" class="btn btn-primary btn-square btn-sm float-right">Upload</button>
                            <button type="reset" class="btn btn-secondary btn-square btn-sm float-right" data-dismiss="modal">Cancel</button>
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
                                    <select class="form-control btn-square p-1 pl-2 select2 select2-multiple" id="user_list" name="user_list[]" style="height: unset;" multiple>
                                        @foreach($resultUsers as $user)
                                            @if(!in_array($user->id, $resultAssignedUsers))
                                            <option id="user_list_{{ $user->id }}" value="{{ $user->id }}" data-name="{{ $user->first_name.' '.$user->last_name }}" data-designation="{{ $user->designation->slug }}">{{ $user->first_name.' '.$user->last_name.' - [ '.$user->role->name.' ]' }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div id="div-select-work-type" class="col-md-12 form-group" style="display: none;">
                                    <label for="work_type">Select Work Type</label>
                                    <select disabled class="form-control btn-square p-1 pl-2" name="agent_work_type_id" style="height: unset;">
                                        @foreach($resultWorkTypes as $work_type)
                                            <option value="{{ $work_type->id }}">{{ $work_type->name }}</option>
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
    <!-- material datetimepicker Js -->
    <script src="{{ asset('public/template/assets/plugins/material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
    <!-- Ckeditor js -->
    <script src="{{ asset('public/template/assets/plugins/ckeditor/js/ckeditor.js') }}"></script>
    <!-- jquery-validation Js -->
    <script src="{{ asset('public/template/assets/plugins/jquery-validation/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('public/js/manager/campaign_assign_show.js?='.time()) }}"></script>
@append
