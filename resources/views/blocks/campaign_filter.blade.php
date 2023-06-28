<style>
    .selection {
        line-height: 1.3 !important;
    }
    .select2-selection--single {
        height: 32px !important;
    }

    .select2-border {
        border: 1px solid #aaa !important;
    }

</style>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-filter m-r-5"></i> Filters</h5>
        <div class="card-header-right">
            <div class="btn-group card-option">
                <button style="display: none;" type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="feather icon-more-vertical"></i>
                </button>
                <button type="button" class="btn minimize-card card-toggle-custom" id="filter-card-toggle"><i class="feather icon-plus m-0"></i></button>
                <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right" style="display: none;">
                    <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                    <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-block" style="display: none;">
        <form id="form-campaign-filters">
            <div class="row">
                <div class="col-md-3 form-group">
                    <label for="start_date">Start Date</label>
                    <input type="text" class="form-control p-1 pl-2 bg-white select2-border" id="filter_start_date" name="start_date" placeholder="Select Start Date">
                </div>
                <div class="col-md-3 form-group">
                    <label for="end_date">End Date</label>
                    <input type="text" class="form-control p-1 pl-2 bg-white select2-border" id="filter_end_date" name="end_date" placeholder="Select End Date">
                </div>
                <div class="col-md-3 form-group">
                    <label for="campaign_status">Status</label>
                    <select class="form-control btn-square p-1 pl-2 select2-multiple" id="filter_campaign_status_id" name="campaign_status_id[]" style="height: unset;" multiple>
                        @foreach($resultCampaignStatuses as $status)
                            <option value="{{$status->id}}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="delivery_day">Delivery Day</label>
                    <select class="form-control btn-square select2-multiple" id="filter_delivery_day" name="delivery_day[]" style="height: unset;" multiple="multiple">
                        <option value="1" data-abbreviation="Mon"> Monday</option>
                        <option value="2" data-abbreviation="Tue"> Tuesday</option>
                        <option value="3" data-abbreviation="Wed"> Wednesday</option>
                        <option value="4" data-abbreviation="Thu"> Thursday</option>
                        <option value="5" data-abbreviation="Fri"> Friday</option>
                        <option value="6" data-abbreviation="Sat"> Saturday</option>
                        <option value="0" data-abbreviation="Sun"> Sunday</option>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="due_in">Due In</label>
                    <select class="form-control btn-square p-1 pl-2" id="filter_due_in" name="due_in" style="height: unset;">
                        <option value=""> -- Select -- </option>
                        <option value="Today">Today</option>
                        <option value="Tomorrow">Tomorrow</option>
                        <option value="7 Days">7 Days</option>
                        <option value="Past Due">Past Due</option>
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="country_id">Country(s)</label>
                    <select class="form-control btn-square select2-multiple" id="filter_country_id" name="country_id[]" multiple="multiple" style="height: unset;">
                        @foreach($resultCountries as $country)
                            <option value="{{$country->id}}" data-region-id="{{$country->region_id}}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="region_id">Region(s)</label>
                    <select class="form-control btn-square select2-multiple" id="filter_region_id" name="region_id[]" multiple="multiple" style="height: unset;">
                        @foreach($resultRegions as $region)
                            <option value="{{$region->id}}" data-abbreviation="{{ $region->abbreviation }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="campaign_type_id">Campaign Type</label>
                    <select class="form-control btn-square p-1 pl-2" id="filter_campaign_type_id" name="campaign_type_id"  style="height: unset;">
                        <option value="">-- Select Campaign Type --</option>
                        @foreach($resultCampaignTypes as $campaign_type)
                            <option value="{{$campaign_type->id}}">{{ $campaign_type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 form-group">
                    <label for="campaign_filter_id">Campaign Filter</label>
                    <select class="form-control btn-square p-1 pl-2" id="filter_campaign_filter_id" name="campaign_filter_id"  style="height: unset;">
                        <option value="">-- Select Campaign Filter --</option>
                        @foreach($resultCampaignFilters as $campaign_filter)
                            <option value="{{$campaign_filter->id}}">{{ $campaign_filter->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <button id="form-campaign-filters-reset" type="reset" class="btn btn-outline-dark btn-square btn-sm"><i class="fas fa-undo m-r-5"></i>Reset</button>
                    <button id="form-campaign-filters-submit" type="button" class="btn btn-outline-primary btn-square btn-sm"><i class="fas fa-filter m-r-5"></i>Apply</button>
                </div>
            </div>
        </form>
    </div>
</div>
