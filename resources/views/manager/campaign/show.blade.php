@extends('layouts.master')

@section('stylesheet')
    @parent
    <meta name="campaign-id" content="{{ base64_encode($resultCampaign->id) }}">
    <meta name="campaign-campaign-id" content="{{ $resultCampaign->campaign_id }}">
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
                                        <h5 class="m-b-10">Campaign Management</h5>
                                        <div class="card-header-right mb-1" style="float: right;">
                                             <a href="{{ route('manager.campaign.list') }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('manager.campaign.list') }}">Campaign Management</a></li>
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
                                                    <h5><span class="text-muted">ID: </span><span>{{ $resultCampaign->campaign_id }}</span></h5>
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
                                            <div class="card-header-right">
                                                <a href="{{ route('manager.campaign.edit', base64_encode($resultCampaign->id)) }}"><button type="button" class="btn btn-outline-primary btn-sm btn-square"><i class="feather icon-edit mr-0"></i> Edit</button></a>
                                            </div>
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

                                    @if(isset($resultCampaign->delivery_file) && !empty($resultCampaign->delivery_file))
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Delivery File</h5>
                                        </div>
                                        <div class="card-block task-attachment">
                                            <ul class="media-list p-0" id="specification_ul">
                                                <li class="media d-flex m-b-15 specification-li">
                                                    <div class="m-r-20 file-attach">
                                                        <i class="far fa-file f-28 text-muted"></i>
                                                    </div>
                                                    <div class="media-body">
                                                        <a href="{{ url('public/storage/campaigns/'.$resultCampaign->campaign_id.'/quality/delivery/'.$resultCampaign->delivery_file) }}" class="double-click" target="_blank" download data-toggle="tooltip" data-placement="top" data-original-title="{{ $resultCampaign->delivery_file }}"><span class="m-b-5 d-block text-primary">@if(strlen($resultCampaign->delivery_file) < 30) {{ $resultCampaign->delivery_file }} @else {{ substr($resultCampaign->delivery_file, 0, 27).'...' }} @endif</span></a>
                                                    </div>
                                                </li>
                                                @if($resultCampaign->children->count())
                                                    @foreach($resultCampaign->children as $incremental)
                                                        <li class="media d-flex m-b-15 specification-li">
                                                            <div class="media-body">
                                                                Incremental Delivery File(s)
                                                            </div>
                                                        </li>
                                                        @if(isset($incremental->delivery_file) && !empty($incremental->delivery_file))
                                                        <li class="media d-flex m-b-15 specification-li">
                                                            <div class="m-r-20 file-attach">
                                                                <i class="far fa-file f-28 text-muted"></i>
                                                            </div>
                                                            <div class="media-body">
                                                                <a href="{{ url('public/storage/campaigns/'.$incremental->campaign_id.'/quality/delivery/'.$incremental->delivery_file) }}" class="double-click" target="_blank" download data-toggle="tooltip" data-placement="top" data-original-title="{{ $incremental->delivery_file }}"><span class="m-b-5 d-block text-primary">@if(strlen($incremental->delivery_file) < 30) {{ $incremental->delivery_file }} @else {{ substr($incremental->delivery_file, 0, 27).'...' }} @endif</span></a>
                                                            </div>
                                                        </li>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Specifications</h5>
                                            <div class="card-header-right">
                                                <button class="btn btn-primary btn-sm btn-square pt-1 pb-1" data-toggle="modal" data-target="#modal-attach-specification" style=""><i class="feather icon-plus mr-0"></i> Attach</button>
                                            </div>
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
                                                        <div class="float-right text-muted">
                                                            <a href="javascript:void(0);" onclick="removeSpecification(this, '{{base64_encode($specification->id)}}');"><i class="fas fa-times f-24 text-danger"></i></a>
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
                                            @if($resultCampaign->campaignFiles->count() < 5)
                                                <div class="card-header-right">
                                                    <button class="btn btn-primary btn-sm btn-square pt-1 pb-1" data-toggle="modal" data-target="#modal-attach-campaign-file" style=""><i class="feather icon-plus mr-0"></i> Attach</button>
                                                </div>
                                            @endif
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
                                                            <div class="float-right text-muted" style="display: none;">
                                                                <a href="javascript:void(0);" onclick="removeSuppression(this, '{{base64_encode($campaignFile->id)}}');"><i class="fas fa-times f-24 text-danger"></i></a>
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
                                                    <span>
                                                        @php
                                                            if($resultCampaign->children->count()) {
                                                                $key = $resultCampaign->children->count() - 1;
                                                                if($resultCampaign->children[$key]->campaign_status_id == 4) {
                                                                    $flagIncremental = true;
                                                                } else {
                                                                    $flagIncremental = false;
                                                                }
                                                            } else {
                                                                $flagIncremental = true;
                                                            }
                                                        @endphp
                                                        @if($resultCampaign->campaign_status_id == 4 && $flagIncremental)
                                                        <a href="{{ route('manager.campaign.create_incremental', base64_encode($resultCampaign->id)) }}">
                                                        <button class="btn btn-primary btn-sm btn-square pt-1 pb-1"><i class="feather icon-plus"></i>Incremental</button>
                                                        </a>
                                                        @endif
                                                    </span>
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
                                                            @if($resultCampaign->campaign_status_id != 4)
                                                                <a href="javascript:;" onclick="editPacingDetails('{{ base64_encode($resultCampaign->id) }}');" class="btn btn-outline-primary btn-sm btn-rounded mb-0" title="Edit pacing details" style="padding: 5px 8px;"><i class="feather icon-edit mr-0"></i></a>
                                                                <a href="javascript:;" onclick="editSubAllocations('{{ base64_encode($resultCampaign->id) }}');" class="btn btn-outline-secondary btn-sm btn-rounded mb-0" title="Edit Sub-Allocations" style="padding: 5px 8px;"><i class="feather icon-edit mr-0"></i></a>
                                                            @else
                                                                -
                                                            @endif
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
                                                                @if($children->campaign_status_id != 4)
                                                                    <a href="javascript:;" onclick="editPacingDetails('{{ base64_encode($children->id) }}');" class="btn btn-outline-primary btn-sm btn-rounded mb-0" title="Edit pacing details" style="padding: 5px 8px;"><i class="feather icon-edit mr-0"></i></a>
                                                                    <a href="javascript:;" onclick="editSubAllocations('{{ base64_encode($children->id) }}');" class="btn btn-outline-secondary btn-sm btn-rounded mb-0" title="Edit Sub-Allocations" style="padding: 5px 8px;"><i class="feather icon-edit mr-0"></i></a>
                                                                @else
                                                                    -
                                                                @endif
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

                                    <div class="card" id="card-campaign-history" style="overflow-y: auto;">
                                        <div class="card-header">
                                            <h5><i class="fas fa-clock m-r-5"></i> Campaign History</h5>
                                            <div class="card-header-right">
                                                <div class="btn-group card-option">
                                                    <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="feather icon-more-vertical"></i>
                                                    </button>
                                                    <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                                        <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                                                        <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                                                        <li class="dropdown-item reload-card"><a href="#!" id="reload-campaign-history"><i class="feather icon-refresh-cw"></i> reload</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-block">
                                            <ul class="task-list" id="campaign-history-ul">

                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                            <div class="text-center">
                                                <button id="btn-get-campaign-history" type="button" class="btn btn-warning shadow-4 btn-sm text-dark btn-square pt-1 pb-1" onclick="getCampaignHistory(this);"><i class="fas fa-spinner"></i> Load More</button>
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

    <div id="modal-edit-campaign-details" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Campaign Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <form id="modal-form-update-campaign-details">
                        <input type="hidden" id="campaign_id" name="campaign_id" value="{{ base64_encode($resultCampaign->id) }}">
                        <div class="row pl-md-3 pr-md-3">
                            <div class="col-md-6 form-group">
                                <label for="name">Campaign Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control btn-square" id="name" name="name" placeholder="Enter campaign name" value="{{ $resultCampaign->name }}">
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="v_mail_campaign_id">V-Mail Campaign ID<span class="text-info"> <small>(Optional)</small></span></label>
                                <input type="text" class="form-control btn-square" id="v_mail_campaign_id" name="v_mail_campaign_id" placeholder="Enter v-mail campaign id" value="{{ $resultCampaign->v_mail_campaign_id }}">
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="campaign_filter_id">Campaign Filter<span class="text-danger">*</span></label>
                                <select class="form-control btn-square" id="campaign_filter_id" name="campaign_filter_id">
                                    <option value="">-- Select Campaign Filter --</option>
                                    @foreach($resultCampaignFilters as $campaign_filter)
                                        <option value="{{$campaign_filter->id}}" @if($campaign_filter->id == $resultCampaign->campaign_filter_id) selected @endif>{{ $campaign_filter->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="campaign_type_id">Campaign Type<span class="text-danger">*</span></label>
                                <select class="form-control btn-square" id="campaign_type_id" name="campaign_type_id">
                                    <option value="">-- Select Campaign Type --</option>
                                    @foreach($resultCampaignTypes as $campaign_type)
                                        <option value="{{$campaign_type->id}}" @if($campaign_type->id == $resultCampaign->campaign_type_id) selected @endif>{{ $campaign_type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="country_id">Country(s)<span class="text-danger">*</span></label>
                                <select class="form-control btn-square select2-multiple" id="country_id" name="country_id[]" multiple="multiple">
                                    @foreach($resultCountries as $country)
                                        <option value="{{$country->id}}" data-region-id="{{$country->region_id}}" @if(in_array($country->id, $resultCampaign->countries->pluck('country_id')->toArray())) selected @endif>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="region_id">Region(s)<span class="text-danger">*</span></label>
                                <select class="form-control btn-square select2-multiple" id="region_id" name="region_id[]" multiple="multiple" disabled>
                                    @foreach($resultRegions as $region)
                                        <option value="{{$region->id}}" @if(in_array($region->id, $resultCampaign->countries->pluck('country.region.id')->toArray())) selected @endif>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="note">Note</label>
                                <textarea id="note" name="note" class="form-control classic-editor" placeholder="Enter note here..." rows="3">{{ $resultCampaign->note }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <div class="modal-footer">
                        <div class="row pl-md-2 pr-md-2">
                            <button type="button" class="btn btn-secondary btn-square btn-sm" data-dismiss="modal">Cancel</button>
                            <button id="modal-form-update-campaign-details-submit" type="button" class="btn btn-primary btn-square btn-sm">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-edit-pacing-details" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="my-modal-edit-pacing-details" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-edit-pacing-details">Edit Pacing Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="modal-form-edit-pacing-details" method="post" action="{{ route('manager.campaign.update', base64_encode($resultCampaign->id)) }}">
                                @csrf
                                <input type="hidden" class="campaign_id" name="id" value="">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="start_date">Start Date<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control btn-square" id="start_date" name="start_date" placeholder="Select Start Date" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="end_date">End Date<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control btn-square" id="end_date" name="end_date" placeholder="Select End Date" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="allocation">Allocation<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control btn-square only-non-zero-number" id="allocation" name="allocation" placeholder="Enter allocation" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="campaign_status_id">Pacing</label>
                                        <select class="form-control btn-square" id="pacing" name="pacing">
                                            <option value="Daily">Daily</option>
                                            <option value="Weekly">Weekly</option>
                                            <option value="Monthly">Monthly</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="campaign_status_id">Status</label>
                                        <select class="form-control btn-square" id="campaign_status_id" name="campaign_status_id">
                                            @foreach($resultCampaignStatuses as $campaign_status)
                                                <option value="{{$campaign_status->id}}">{{ $campaign_status->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="div-shortfall-count" class="col-md-6 form-group" style="display: none;">
                                        <label for="shortfall_count">Shortfall Count<span class="text-danger">*</span></label>
                                        <input type="number" class="form-control btn-square only-non-zero-number" id="shortfall_count" name="shortfall_count" placeholder="Enter Shortfall Count" disabled>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-warning" role="alert">
                                            <p>Warning: If Start Date, End Date OR Pacing are updated then corresponding sub-allocation will be removed.</p>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-square float-right">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-attach-specification" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attach Specification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="modal-form-attach-specification" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="specifications">Specifications</label>
                                    <input type="file" class="form-control-file" id="specifications" name="specifications[]" multiple required>
                                </div>
                            </div>
                            <button id="form-attach-specification-reset" type="button" class="btn btn-secondary btn-square float-right" data-dismiss="modal">Cancel</button>
                            <button id="modal-form-attach-specification-submit" type="button" class="btn btn-primary btn-square float-right">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-attach-campaign-file" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attach Campaign Files</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="modal-form-attach-campaign-file" enctype="multipart/form-data">
                            @csrf
                            @php
                                $campaignFileFlag['suppression_email'] = 1;
                                $campaignFileFlag['suppression_domain'] = 1;
                                $campaignFileFlag['suppression_account_name'] = 1;
                                $campaignFileFlag['target_domain'] = 1;
                                $campaignFileFlag['target_account_name'] = 1;
                            @endphp
                            @if($resultCampaign->campaignFiles->count())
                                @foreach($resultCampaign->campaignFiles as $campaignFile)
                                    @switch($campaignFile->file_type)
                                        @case('suppression_email') @php $campaignFileFlag['suppression_email'] = 0; @endphp @break
                                        @case('suppression_domain') @php $campaignFileFlag['suppression_domain'] = 0; @endphp @break
                                        @case('suppression_account_name') @php $campaignFileFlag['suppression_account_name'] = 0; @endphp @break
                                        @case('target_domain') @php $campaignFileFlag['target_domain'] = 0; @endphp @break
                                        @case('target_account_name') @php $campaignFileFlag['target_account_name'] = 0; @endphp @break
                                    @endswitch
                                @endforeach
                            @else
                                @php
                                    $campaignFileFlag['suppression_email'] = 1;
                                    $campaignFileFlag['suppression_domain'] = 1;
                                    $campaignFileFlag['suppression_account_name'] = 1;
                                    $campaignFileFlag['target_domain'] = 1;
                                    $campaignFileFlag['target_account_name'] = 1;
                                @endphp
                            @endif
                            <div class="row">
                                @if($campaignFileFlag['suppression_email'])
                                <div class="col-md-4 form-group">
                                    <label for="suppression_email">Email Suppression</label>
                                    <input type="file" class="form-control-file btn btn-outline-success btn btn-square" id="suppression_email" name="suppression_email">
                                </div>
                                @endif
                                @if($campaignFileFlag['suppression_domain'])
                                <div class="col-md-4 form-group">
                                    <label for="suppression_domain">Domain Suppression</label>
                                    <input type="file" class="form-control-file btn btn-outline-success btn btn-square" id="suppression_domain" name="suppression_domain">
                                </div>
                                @endif
                                @if($campaignFileFlag['suppression_account_name'])
                                <div class="col-md-4 form-group">
                                    <label for="suppression_account_name">Account Suppression</label>
                                    <input type="file" class="form-control-file btn btn-outline-success btn btn-square" id="suppression_account_name" name="suppression_account_name">
                                </div>
                                @endif
                                @if($campaignFileFlag['target_domain'])
                                    <div class="col-md-4 form-group">
                                        <label for="target_domain">Domain Target List</label>
                                        <input type="file" class="form-control-file btn btn-outline-secondary btn btn-square" id="target_domain" name="target_domain">
                                    </div>
                                @endif
                                @if($campaignFileFlag['target_account_name'])
                                    <div class="col-md-4 form-group">
                                        <label for="target_account_name">Account Target List</label>
                                        <input type="file" class="form-control-file btn btn-outline-secondary btn btn-square" id="target_account_name" name="target_account_name">
                                    </div>
                                @endif
                            </div>

                            <button id="form-attach-campaign-file-reset" type="button" class="btn btn-secondary btn-square float-right" data-dismiss="modal">Cancel</button>
                            <button id="modal-form-attach-campaign-file-submit" type="button" class="btn btn-primary btn-square float-right">Upload</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-edit-sub-allocations" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="my-modal-edit-sub-allocations" aria-hidden="true" style="background: rgba(0, 0, 0, 0.7) !important;" >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-edit-sub-allocations">Edit Sub Allocations</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="modal-form-edit-sub-allocations" method="post" action="{{ route('manager.campaign.update_sub_allocations', base64_encode($resultCampaign->id)) }}">
                                @csrf
                                <input type="hidden" id="edit_sub_allocation_campaign_id" name="campaign_id" value="">
                                <div class="row pl-md-4 pr-md-4">
                                    <div class="col-md-3 form-group">
                                        <label for="start_date">Start Date: <h5 class="label-start-date">{{ date('d-M-Y', strtotime($resultCampaign->start_date)) }}</h5></label>
                                        <input type="hidden" id="campaign_start_date" value="{{ date('d-M-Y', strtotime($resultCampaign->start_date)) }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="end_date">End Date: <h5 class="label-end-date">{{ date('d-M-Y', strtotime($resultCampaign->end_date)) }}</h5></label>
                                        <input type="hidden" id="campaign_end_date" value="{{ date('d-M-Y', strtotime($resultCampaign->end_date)) }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="pacing">Pacing: <h5 class="label-pacing">{{ ucfirst($resultCampaign->pacing) }}</h5></label>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="pacing">Total Sub-Allocations:
                                            <h5>
                                                <span id="total-sub-allocation">0</span> /
                                                <span class="label-allocation">{{ $resultCampaign->allocation }}</span>
                                            </h5>
                                        </label>
                                        <input type="hidden" id="campaign_allocation" value="{{ $resultCampaign->allocation }}">
                                    </div>
                                </div>

                                <div class="row pl-md-4 pr-md-4" id="div-pacing-details">
                                    <div class="col-md-3 col-sm-12">
                                        <ul class="nav flex-column nav-pills" id="v-pills-tab-month-list" role="tablist" aria-orientation="vertical">
                                        </ul>
                                    </div>

                                    <div class="col-md-9 col-sm-12">
                                        <div class="tab-content" id="v-pills-tabContent">
                                        </div>
                                    </div>
                                </div>

                                <div class="row pl-md-4 pr-md-4 float-right" id="div-pacing-details">
                                    <button type="submit" class="btn btn-primary btn-square float-right">Update</button>
                                </div>
                            </form>
                        </div>
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

    <script src="{{ asset('public/js/manager/campaign_show.js?='.time()) }}"></script>
@append
