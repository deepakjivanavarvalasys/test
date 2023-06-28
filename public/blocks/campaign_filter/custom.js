
$(function (){
    //Filters inputs initialization
    $('#filter_start_date').datepicker({
        autoclose: true,
        todayHighlight: true
    });

    $('#filter_end_date').datepicker({
        autoclose: true,
        todayHighlight: true
    });

    $("#filter_campaign_status_id").select2({
        placeholder: " -- Select Status(s) --"
    });

    $("#filter_delivery_day").select2({
        placeholder: " -- Select Day(s) --",
        templateSelection: function (a){return!!$(a.element).data("abbreviation")&&$(a.element).data("abbreviation")}
    });

    $("#filter_due_in").select2({
        placeholder: " -- Select --",
    });

    $("#filter_country_id").select2({ placeholder: " -- Select Country(s) --"});

    $("#filter_region_id").select2({
        placeholder: " -- Select Region(s) --",
        templateSelection: function (a){return!!$(a.element).data("abbreviation")&&$(a.element).data("abbreviation")}
    });

    $("#filter_campaign_type_id").select2({ placeholder: " -- Select Campaign Type --"});

    $("#filter_campaign_filter_id").select2({ placeholder: " -- Select Campaign Filter --"});

    $("#form-campaign-filters-reset").on('click', function(){
        $("#form-campaign-filters").find('input').val('');
        $("#form-campaign-filters").find('select').val('').trigger('change');
        CAMPAIGN_TABLE.ajax.reload();
    });

    $('#form-campaign-filters-submit').on('click', function (e) {
        CAMPAIGN_TABLE.ajax.reload();
    });
});
