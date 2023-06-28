@extends('layouts.master')

@section('stylesheet')
    @parent
    <meta name="ca-qa-id" content="{{ base64_encode($resultCAQA->id) }}">

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

        #table-agents.table td {
            padding: 10px 10px !important;
            vertical-align: inherit !important;
        }

        #table-agent-lead-details.table td {
            padding: 5px 10px !important;
            vertical-align: inherit !important;
        }
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
                                        <h5 class="m-b-10">My Campaigns</h5>
                                        <div class="card-header-right mb-1" style="float: right;">
                                            <a href="{{ route('quality_analyst.campaign.list') }}" class="btn btn-outline-info btn-square btn-sm pt-1 pb-1" style="font-weight: bold;"><i class="feather icon-arrow-left"></i>Back</a>
                                        </div>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('quality_analyst.dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('quality_analyst.campaign.list') }}">My Campaigns</a></li>
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
                                                    <h5><span class="text-muted">ID: </span><span id="campaign_campaign_id">{{ $resultCAQA->campaign->campaign_id }}</span></h5>
                                                    <h6><span><span class="text-muted">Name: </span>{{ $resultCAQA->campaign->name }}</span></h6>
                                                    @if($resultCAQA->campaign->v_mail_campaign_id)
                                                        <h6><span><span class="text-muted">V-Mail Campaign ID: </span>{{ $resultCAQA->campaign->v_mail_campaign_id }}</span></h6>
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
                                            <h6 class="text-muted f-w-300">Campaign Type: <span class="float-right">{{ $resultCAQA->campaign->campaignType->name }}</span></h6>
                                            <div style="border-bottom: 1px solid #e2dada;">&nbsp;</div>
                                            <h6 class="text-muted f-w-300 mt-4">Campaign Filter: <span class="float-right">{{ $resultCAQA->campaign->campaignFilter->name }}</span></h6>
                                            <div style="border-bottom: 1px solid #e2dada;">&nbsp;</div>
                                            <h6 class="text-muted f-w-300 mt-4">Country(s): <br><br><span class="float-right">
                                                    @foreach($resultCAQA->campaign->countries->pluck('country.name')->toArray() as $country)
                                                        <span class="badge badge-info m-1" style="padding: 5px 15px;">{{$country}}</span>
                                                    @endforeach
                                                </span></h6>
                                            <div style="border-bottom: 1px solid #e2dada;">&nbsp;</div>
                                            <h6 class="text-muted f-w-300 mt-4">Region(s): <br><br><span class="float-right">
                                                    @foreach($resultCAQA->campaign->countries->pluck('country.region.name')->unique()->toArray() as $region)
                                                        <span class="badge badge-dark m-1" style="padding: 5px 15px;">{{$region}}</span>
                                                    @endforeach
                                                </span></h6>
                                            <div style="border-bottom: 1px solid #e2dada;">&nbsp;</div>
                                            <h6 class="text-muted f-w-300 mt-4">
                                                Note: <br><br>
                                                <span class="float-right">
                                                    @if(strlen($resultCAQA->campaign->note) > 200)
                                                        <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#modal-campaign-note">View Note</button>
                                                    @else
                                                        {!! $resultCAQA->campaign->note !!}
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
                                                @forelse($resultCAQA->campaign->specifications as $specification)
                                                    <li class="media d-flex m-b-15 specification-li">
                                                        <div class="m-r-20 file-attach">
                                                            <i class="far fa-file f-28 text-muted"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <a href="{{ url('public/storage/campaigns/'.$resultCAQA->campaign->campaign_id.'/'.rawurlencode($specification->file_name)) }}" class="double-click" target="_blank" download data-toggle="tooltip" data-placement="top" data-original-title="{{ $specification->file_name }}"><span class="m-b-5 d-block text-primary">@if(strlen($specification->file_name) < 30) {{ $specification->file_name }} @else {{ substr($specification->file_name, 0, 27).'...' }} @endif</span></a>
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
                                            <h5>EBB File(s)</h5>
                                        </div>
                                        <div class="card-block task-attachment">
                                            <ul class="media-list p-0" id="specification_ul">
                                                @forelse($resultCampaignEBBFiles as $key => $campaign_ebb_file)
                                                    <li class="media d-flex m-b-15 specification-li">
                                                        <div class="m-r-20 file-attach">
                                                            <i class="far fa-file f-28 text-muted"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <a href="{{ url('public/storage/campaigns/'.$resultCAQA->campaign->campaign_id.'/quality/npf/'.rawurlencode($campaign_ebb_file->file_name)) }}" class="double-click" target="_blank" download data-toggle="tooltip" data-placement="top" data-original-title="{{ $campaign_ebb_file->file_name }}"><span class="m-b-5 d-block text-primary">@if(strlen($campaign_ebb_file->file_name) < 30) {{ $campaign_ebb_file->file_name }} @else {{ substr($campaign_ebb_file->file_name, 0, 27).'...' }} @endif</span></a>
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
                                        <div class="card-block" style="padding: 0 10px 10px 10px;">
                                            <div class="table-responsive">
                                                <table class="table m-b-0 f-14 b-solid requid-table">
                                                    <thead>
                                                    <tr class="text-uppercase">
                                                        <th class="text-center">#</th>
                                                        <th class="text-center">Status</th>
                                                        <th class="text-center">Start Date</th>
                                                        <th class="text-center">End Date</th>
                                                        <th class="text-center">Pacing</th>
                                                        <th class="text-center">Allocation</th>
                                                        <th class="text-center">Campaign<br>Status</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="text-center text-muted">
                                                    <tr>
                                                        <td><i class="feather icon-plus-square toggle-pacing-details" style="cursor: pointer;font-size: 17px;"></i></td>
                                                        <td>
                                                            @switch($resultCAQA->status)
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
                                                        <td>{{ date('d-M-Y', strtotime($resultCAQA->campaign->start_date)) }}</td>
                                                        <td>{{ date('d-M-Y', strtotime($resultCAQA->display_date)) }}</td>
                                                        <td>{{ ucfirst($resultCAQA->campaign->pacing) }}</td>
                                                        <td>
                                                            {{ $resultCAQA->campaign->allocation }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $campaign_type = '';
                                                                if($resultCAQA->campaign->type == 'incremental') {
                                                                    $campaign_type = ' (Incremental)';
                                                                }
                                                            @endphp
                                                            @switch($resultCAQA->campaign->campaign_status_id)
                                                                @case(1)
                                                                <span class="badge badge-success" style="padding: 5px;min-width: 70px;">Live{{ $campaign_type }}</span>
                                                                @break
                                                                @case(2)
                                                                <span class="badge badge-warning" style="padding: 5px;min-width: 70px;">Paused{{ $campaign_type }}</span>
                                                                @break
                                                                @case(3)
                                                                <span class="badge badge-danger" style="padding: 5px;min-width: 70px;">Cancelled{{ $campaign_type }}</span>
                                                                @break
                                                                @case(4)
                                                                <span class="badge badge-primary" style="padding: 5px;min-width: 70px;">Delivered{{ $campaign_type }}</span>
                                                                @break
                                                                @case(5)
                                                                <span class="badge badge-success" style="padding: 5px;min-width: 70px;">Reactivated{{ $campaign_type }}</span>
                                                                @break
                                                                @case(6)
                                                                <span class="badge badge-secondary" style="padding: 5px;min-width: 80px;">Shortfall{{ $campaign_type }}</span>
                                                                @break
                                                            @endswitch
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
                                                                @forelse($resultCAQA->campaign->pacingDetails as $subAllocation)
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
                                        @if(empty($resultCAQA->submitted_at))
                                        <div id="div-download-file" class="col-md-3">
                                            <button type="button" class="btn btn-dark btn-sm btn-square w-100" onclick="downloadFile('{{ base64_encode($resultCAQA->id) }}');"><i class="feather icon-download"></i>Download File</button>
                                        </div>
                                        <div id="div-download-npf-file" class="col-md-3">
                                            <button type="button" class="btn btn-secondary btn-sm btn-square w-100" onclick="downloadNPF('{{ base64_encode($resultCAQA->id) }}');"><i class="feather icon-download"></i>Download NPF</button>
                                        </div>
                                        <div id="div-upload-NPF" class="col-md-3">
                                            <button type="button" class="btn btn-warning btn-sm btn-square w-100" data-toggle="modal" data-target="#modal-upload-npf"><i class="feather icon-upload"></i>Upload NPF</button>
                                        </div>
                                        <div id="div-submit-campaign" class="col-md-3">
                                            <button type="button"  class="btn btn-danger btn-sm btn-square w-100" data-toggle="modal" data-target="#modal-submit-campaign">Submit Campaign</button>
                                        </div>
                                       <div id="div-manager-leads" class="col-md-3 mt-2">
                                            <button type="button" class="btn btn-primary btn-sm btn-square w-100" onclick="window.location.href='{{ route('quality_analyst.lead.list', base64_encode($resultCAQA->id)) }}';">Manage Leads</button>
                                        </div>
                                        @endif
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

    <div id="modal-upload-npf" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload NPF File(s)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <form id="form-upload-npf" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="npf_file">NPF File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file" id="npf_file" name="npf_file[]" multiple required=""/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="npf_file">Select EME User <span class="text-danger">*</span></label>
                                    <select class="form-control btn-square" id="user_id" name="user_id">
                                        <option value="">--- Select EME User ---</option>
                                        @foreach($resultEMEUsers as $user)
                                            <option value="{{$user->id}}">{{ $user->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="reset" class="btn btn-secondary btn-square float-right">Clear</button>
                            <button id="form-upload-npf-submit" type="button" class="btn btn-primary btn-square float-right">Upload</button>
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
                        <form id="form-submit-campaign" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-12 form-group">
                                    <label for="npf_file">Upload Final File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control-file" id="final_file" name="final_file" required>
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
@endsection

@section('javascript')
    @parent
    <!-- footable Js -->
    <script src="{{ asset('public/template/assets/plugins/footable/js/footable.min.js') }}"></script>
    <!-- select2 Js -->
    <script src="{{ asset('public/template/assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- material datetimepicker Js -->
    <script src="{{ asset('public/template/assets/plugins/material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
    <!-- Ckeditor js -->
    <script src="{{ asset('public/template/assets/plugins/ckeditor/js/ckeditor.js') }}"></script>

    <script src="{{ asset('public/js/quality_analyst/campaign_show.js?='.time()) }}"></script>
@append
