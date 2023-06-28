

let URL = $('meta[name="base-path"]').attr('content');
let MONTHS = ['Jan','Feb','Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
let DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
let HOLIDAYS = [];
let HOLIDAY_LIST = [];
let CAMPAIGN_HISTORY_SKIP = 0;

//Initializations
$(function(){

    $.ajax({
        url: URL + '/holiday/get-holiday-list',
        type: "get",
        success: function(response) {
            if(response.status === true) {
                HOLIDAYS = response.data;
                $.each(HOLIDAYS, function (index, row) {
                    HOLIDAY_LIST.push(row.date);
                });
            } else {
                HOLIDAY_LIST = [];
            }
        }
    });

    // classic editor
    $(window).on('load', function() {
        // classic editor
        ClassicEditor
            .create(document.querySelector('.classic-editor'))
            .then( editor => {
                // console.log( 'Editor was initialized', editor );
                myEditor = editor;
            } )
            .catch(error => {
            console.error(error);
        });
    });

    $('.foo-table').footable({
        "paging": { "enabled": true }
    });

    $('.toggle-pacing-details').click(function (){
        if($(this).hasClass('icon-plus-square')) {
            $(this).removeClass('icon-plus-square').addClass('icon-minus-square');
            $(this).parents('tr').next('tr').show(1000);
        } else {
            $(this).removeClass('icon-minus-square').addClass('icon-plus-square');
            $(this).parents('tr').next('tr').hide(500);
        }
    });

    //Auto select regions
    $("#country_id").change(function () {
        let regions = [];
        $.each($(this).children('option:selected'), function () {
            regions.push($(this).data('region-id'));
        });
        $("#region_id").val(regions);
        $("#region_id").select2('destroy').select2({
            placeholder: " -- Region(s) --",
        });
    });

    $("#campaign_status_id").change(function (){
        if(parseInt($(this).val()) === 6) {
            $("#div-shortfall-count").show();
            $("#shortfall_count").removeAttr('disabled');
        } else {
            $("#div-shortfall-count").hide();
            $("#shortfall_count").attr('disabled','disabled');
        }
    });

    //Initialize date picker for edit pacing details
    $('#start_date').bootstrapMaterialDatePicker({
        weekStart: 0,
        time: false,
        format: 'D-MMM-YYYY',
        dropdownParent: $('#modal-edit-pacing-details'),
        switchOnClick : true,
    }).on('change', function(e, date) {
        $('#end_date').bootstrapMaterialDatePicker('setMinDate', date);
        let d_sd = new Date(date);
        let d_ed = new Date($('#end_date').val());
        d_sd.setHours(0,0,0,0);
        d_ed.setHours(0,0,0,0);
        if(d_sd > d_ed) {
            $('#end_date').val(d_sd.getDate() + '-' + MONTHS[d_sd.getMonth()] + '-' + d_sd.getFullYear());
        }
    });

    $('#end_date').bootstrapMaterialDatePicker({
        weekStart: 0,
        time: false,
        format: 'D-MMM-YYYY',
        dropdownParent: $('#modal-edit-pacing-details'),
        switchOnClick : true,
    }).on('change', function(e, date) {
        $('#start_date').bootstrapMaterialDatePicker('setMaxDate', date);
    });

    //Update total sub-allocation count
    $('body').on('keyup change', ".sub-allocation",function () {
        let total = 0;

        $('body').find('.sub-allocation').each(function(){
            if($(this).val() !== '') {
                total = total + parseInt($(this).val());
            }
        });
        total = (total > 0) ? total : 0;
        $("#total-sub-allocation").html(total);
        if(total > parseInt($("#campaign_allocation").val())) {
            $("#total-sub-allocation").html(total - parseInt($(this).val()));
            $(this).val('');
            $(this).keyup();
        }
    });

    $('body').on("input", ".only-non-zero-number", function (){
        if(this.value < 1) {
            $(this).val('');
        } else {
            $(this).val(parseInt(this.value));
        }
    });

});


$(function(){

    $('#form-attach-specification-reset').on('click', function (e) {
        document.getElementById("modal-form-attach-specification").reset();
    });

    $('#form-attach-campaign-file-reset').on('click', function (e) {
        document.getElementById("modal-form-attach-campaign-file").reset();
    });

    $("#modal-form-attach-specification").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'specifications[]' : {
                required: true,
            }
        },
        messages: {
            'specifications[]' : {
                required: "Please upload file",
            }
        },
        errorPlacement: function errorPlacement(error, element) {
            var $parent = $(element).parents('.form-group');

            // Do not duplicate errors
            if ($parent.find('.jquery-validation-error').length) {
                return;
            }

            $parent.append(
                error.addClass('jquery-validation-error small form-text invalid-feedback')
            );
        },
        highlight: function(element) {
            var $el = $(element);
            var $parent = $el.parents('.form-group');

            $el.addClass('is-invalid');

            // Select2 and Tagsinput
            if ($el.hasClass('select2-hidden-accessible') || $el.attr('data-role') === 'tagsinput') {
                $el.parent().addClass('is-invalid');
            }
        },
        unhighlight: function(element) {
            $(element).parents('.form-group').find('.is-invalid').removeClass('is-invalid');
        }
    });

    $('#modal-form-attach-specification-submit').on('click', function (e) {
        e.preventDefault();

        if($("#modal-form-attach-specification").valid()) {
            let form_data = new FormData($('#modal-form-attach-specification')[0]);

            $.ajax({
                url: URL +'/email-marketing-executive/campaign-management/attach-specification/'+$('#campaign_id').val(),
                processData: false,
                contentType: false,
                data: form_data,
                type: 'post',
                success: function(response) {
                    if(response.status === true) {
                        //If no specification remove all list
                        if($('.specification-li').length === 0) {
                            $('#specification_ul').html('');
                        }
                        let html = '';
                        $.each(response.data, function(key, value){
                            html += '<li class="media d-flex m-b-15 specification-li">\n\
                                    <div class="m-r-20 file-attach">\n\
                                        <i class="far fa-file f-28 text-muted"></i>\n\
                                    </div>\n\
                                    <div class="media-body">\n\
                                        <a href="'+ $('meta[name="base-path"]').attr('content') + '/public/storage/campaigns/'+$('#campaign_campaign_id').val()+value.file_name+'" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Click to view"><span class="m-b-5 d-block text-primary">'+value.file_name+'</span></a>\n\
                                    </div>\n\
                                    <div class="float-right text-muted">\n\
                                        <a href="javascript:void(0);" onclick="removeSpecification(this, \''+btoa(value.id)+'\');"><i class="fas fa-times f-24 text-danger"></i></a>\n\
                                    </div>\n\
                                </li>';
                        });
                        $('#specification_ul').append(html);
                        $('#modal-attach-specification').modal('hide');
                        trigger_pnofify('success', 'Successful', response.message);
                    } else {
                        trigger_pnofify('error', 'Error while processing request', response.message);
                    }

                    $("#reload-campaign-history").click();
                }
            });

            document.getElementById("modal-form-attach-specification").reset();
        }
    });

    $("#modal-form-attach-campaign-file").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'suppression_email' : {
                extension: "xlsx"
            },
            'suppression_domain' : {
                extension: "xlsx"
            },
            'suppression_account_name' : {
                extension: "xlsx"
            },
            'target_domain' : {
                extension: "xlsx"
            },
            'target_account_name' : {
                extension: "xlsx"
            },
        },
        messages: {
            'suppression_email' : {
                extension: "Please upload valid file, (xlsx)"
            },
            'suppression_domain' : {
                extension: "Please upload valid file, (xlsx)"
            },
            'suppression_account_name' : {
                extension: "Please upload valid file, (xlsx)"
            },
            'target_domain' : {
                extension: "Please upload valid file, (xlsx)"
            },
            'target_account_name' : {
                extension: "Please upload valid file, (xlsx)"
            },
        },
        errorPlacement: function errorPlacement(error, element) {
            var $parent = $(element).parents('.form-group');

            // Do not duplicate errors
            if ($parent.find('.jquery-validation-error').length) {
                return;
            }

            $parent.append(
                error.addClass('jquery-validation-error small form-text invalid-feedback')
            );
        },
        highlight: function(element) {
            var $el = $(element);
            var $parent = $el.parents('.form-group');

            $el.addClass('is-invalid');

            // Select2 and Tagsinput
            if ($el.hasClass('select2-hidden-accessible') || $el.attr('data-role') === 'tagsinput') {
                $el.parent().addClass('is-invalid');
            }
        },
        unhighlight: function(element) {
            $(element).parents('.form-group').find('.is-invalid').removeClass('is-invalid');
        }
    });

    $('#modal-form-attach-campaign-file-submit').on('click', function (e) {
        e.preventDefault();
        if($("#modal-form-attach-campaign-file").valid()) {
            let form_data = new FormData($('#modal-form-attach-campaign-file')[0]);

            $.ajax({
                url: $('meta[name="base-path"]').attr('content') +'/email-marketing-executive/campaign-management/attach-campaign-file/'+$('#campaign_id').val(),
                processData: false,
                contentType: false,
                data: form_data,
                type: 'post',
                success: function(response) {
                    if(response.status === true) {
                        //If no suppression remove all list
                        if($('.campaign-file-li').length === 0) {
                            $('#campaign-file-ul').html('');
                        }
                        let html = '';

                        if(response.data.suppression_email) {
                            let value = response.data.suppression_email;
                            html += '<li class="media d-flex m-b-15 campaign-file-li">\n\
                                    <div class="m-r-20 file-attach">\n\
                                        <i class="far fa-file f-28 text-muted"></i>\n\
                                    </div>\n\
                                    <div class="media-body">\n\
                                        <a href="'+ $('meta[name="base-path"]').attr('content') + '/public/storage/campaigns/'+value.campaign.campaign_id+'/'+value.file_name+'" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Click to view"><span class="m-b-5 d-block text-primary">'+value.file_name+'</span></a>\n\
                                    </div>\n\
                                    <div class="float-right text-muted" style="display: none;">\n\
                                        <a href="javascript:void(0);" onclick="removeSuppression(this, \''+btoa(value.id)+'\');"><i class="fas fa-times f-24 text-danger"></i></a>\n\
                                    </div>\n\
                                </li>';
                        }

                        if(response.data.suppression_domain) {
                            let value = response.data.suppression_domain;
                            html += '<li class="media d-flex m-b-15 campaign-file-li">\n\
                                    <div class="m-r-20 file-attach">\n\
                                        <i class="far fa-file f-28 text-muted"></i>\n\
                                    </div>\n\
                                    <div class="media-body">\n\
                                        <a href="'+ $('meta[name="base-path"]').attr('content') + '/public/storage/campaigns/'+value.campaign.campaign_id+'/'+value.file_name+'" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Click to view"><span class="m-b-5 d-block text-primary">'+value.file_name+'</span></a>\n\
                                    </div>\n\
                                    <div class="float-right text-muted" style="display: none;">\n\
                                        <a href="javascript:void(0);" onclick="removeSuppression(this, \''+btoa(value.id)+'\');"><i class="fas fa-times f-24 text-danger"></i></a>\n\
                                    </div>\n\
                                </li>';
                        }

                        if(response.data.suppression_account_name) {
                            let value = response.data.suppression_account_name;
                            html += '<li class="media d-flex m-b-15 campaign-file-li">\n\
                                    <div class="m-r-20 file-attach">\n\
                                        <i class="far fa-file f-28 text-muted"></i>\n\
                                    </div>\n\
                                    <div class="media-body">\n\
                                        <a href="'+ $('meta[name="base-path"]').attr('content') + '/public/storage/campaigns/'+value.campaign.campaign_id+'/'+value.file_name+'" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Click to view"><span class="m-b-5 d-block text-primary">'+value.file_name+'</span></a>\n\
                                    </div>\n\
                                    <div class="float-right text-muted" style="display: none;">\n\
                                        <a href="javascript:void(0);" onclick="removeSuppression(this, \''+btoa(value.id)+'\');"><i class="fas fa-times f-24 text-danger"></i></a>\n\
                                    </div>\n\
                                </li>';
                        }

                        if(response.data.target_domain) {
                            let value = response.data.target_domain;
                            html += '<li class="media d-flex m-b-15 campaign-file-li">\n\
                                    <div class="m-r-20 file-attach">\n\
                                        <i class="far fa-file f-28 text-muted"></i>\n\
                                    </div>\n\
                                    <div class="media-body">\n\
                                        <a href="'+ $('meta[name="base-path"]').attr('content') + '/public/storage/campaigns/'+value.campaign.campaign_id+'/'+value.file_name+'" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Click to view"><span class="m-b-5 d-block text-primary">'+value.file_name+'</span></a>\n\
                                    </div>\n\
                                    <div class="float-right text-muted" style="display: none;">\n\
                                        <a href="javascript:void(0);" onclick="removeSuppression(this, \''+btoa(value.id)+'\');"><i class="fas fa-times f-24 text-danger"></i></a>\n\
                                    </div>\n\
                                </li>';
                        }

                        if(response.data.target_account_name) {
                            let value = response.data.target_account_name;
                            html += '<li class="media d-flex m-b-15 campaign-file-li">\n\
                                    <div class="m-r-20 file-attach">\n\
                                        <i class="far fa-file f-28 text-muted"></i>\n\
                                    </div>\n\
                                    <div class="media-body">\n\
                                        <a href="'+ $('meta[name="base-path"]').attr('content') + '/public/storage/campaigns/'+value.campaign.campaign_id+'/'+value.file_name+'" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="Click to view"><span class="m-b-5 d-block text-primary">'+value.file_name+'</span></a>\n\
                                    </div>\n\
                                    <div class="float-right text-muted" style="display: none;">\n\
                                        <a href="javascript:void(0);" onclick="removeSuppression(this, \''+btoa(value.id)+'\');"><i class="fas fa-times f-24 text-danger"></i></a>\n\
                                    </div>\n\
                                </li>';
                        }

                        $('#campaign-file-ul').append(html);
                        $('#modal-attach-campaign-file').modal('hide');
                        trigger_pnofify('success', 'Successful', response.message);
                    } else {
                        $('#modal-attach-campaign-file').modal('hide');
                        trigger_pnofify('error', 'Error while processing request', response.message);
                    }

                    $("#reload-campaign-history").click();
                }
            });

            document.getElementById("modal-form-attach-campaign-file").reset();
        }

    });

    $("#btn-get-campaign-history").click();
    $("#reload-campaign-history").on('click', function (){
        CAMPAIGN_HISTORY_SKIP = 0;
        $("#btn-get-campaign-history").click();
    });

});

function editCampaignDetails() {
    $('#country_id').select2({
        placeholder: " -- Select Country(s) --",
        dropdownParent: $('#modal-edit-campaign-details')
    });
    $('#region_id').select2({
        placeholder: " -- Region(s) --",
        dropdownParent: $('#modal-edit-campaign-details')
    });
    $('#modal-edit-campaign-details').modal('show');
}

function editPacingDetails(id) {
    if(confirm("Are you sure to edit pacing details?")) {
        $.ajax({
            type: 'get',
            url: $('meta[name="base-path"]').attr('content') + '/email-marketing-executive/campaign-management/edit-pacing-details/' + id,
            success: function (response){
                if(response.status === true) {
                    $('input.campaign_id').val(btoa(response.data.id));
                    $('#start_date').bootstrapMaterialDatePicker('setDate', new Date(response.data.start_date));
                    $('#end_date').bootstrapMaterialDatePicker('setMinDate', new Date(response.data.start_date));
                    $('#end_date').bootstrapMaterialDatePicker('setDate', new Date(response.data.end_date));
                    $('#allocation').val(response.data.allocation);
                    $('#deliver_count').val(response.data.deliver_count);
                    $('#campaign_status_id').val(response.data.campaign_status_id);
                    $('#pacing').val(response.data.pacing);

                    if(response.data.campaign_status_id === 6) {
                        $("#div-shortfall-count").show();
                        $("#shortfall_count").removeAttr('disabled');
                        $('#shortfall_count').val(response.data.shortfall_count);
                    } else {
                        $("#div-shortfall-count").hide();
                        $("#shortfall_count").attr('disabled','disabled');
                        $('#shortfall_count').val(0);
                    }
                    $("#modal-edit-pacing-details").modal('show');
                    //trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Error while processing request', response.message);
                }
            }
        });
    }
}

function editSubAllocations(id) {
    if(confirm("Are you sure to edit sub-allocations?")) {
        $.ajax({
            type: 'get',
            url: $('meta[name="base-path"]').attr('content') + '/email-marketing-executive/campaign-management/edit-sub-allocations/' + id,
            success: function (response){
                $('#edit_sub_allocation_campaign_id').val(id);
                $("#total-sub-allocation").html(0)
                $('#v-pills-tab-month-list').html('');
                $('#v-pills-tabContent').html('');

                if(response.status === true) {
                    let data = response.data;
                    $('#modal-edit-sub-allocations').find('.label-start-date').html(moment(data.resultCampaign.start_date).format('DD-MMM-YYYY'));
                    $('#campaign_start_date').val(moment(data.resultCampaign.start_date).format('DD-MMM-YYYY'));
                    $('#modal-edit-sub-allocations').find('.label-end-date').html(moment(data.resultCampaign.end_date).format('DD-MMM-YYYY'));
                    $('#campaign_end_date').val(moment(data.resultCampaign.end_date).format('DD-MMM-YYYY'));
                    $('#modal-edit-sub-allocations').find('.label-pacing').html(data.resultCampaign.pacing);
                    $('#modal-edit-sub-allocations').find('.label-allocation').html(data.resultCampaign.allocation);
                    $('#campaign_allocation').val(data.resultCampaign.allocation);

                    $.each(response.data.resultMonthList, function(key, value){
                        let html_month_list_tabs = '<li><a class="nav-link text-left '+ (0===key?'show active':'') +'" id="v-pills-'+ value.month_name +'-tab" data-toggle="pill" href="#v-pills-'+ value.month_name +'" role="tab" aria-controls="v-pills-'+ value.month_name +'" aria-selected="false">'+ value.month_name +'</a></li>';

                        let html_tabs_content = '<div class="tab-pane fade '+ (0===key?'show active':'') +'" id="v-pills-'+ value.month_name +'" role="tabpanel" aria-labelledby="v-pills-'+ value.month_name +'-tab">' +
                                '<div class="row">' +
                                    '<div class="col-md-6 form-group">' +
                                        getDaySelection_html(response.data.resultCampaign.pacing, value) +
                                    '</div>' +
                                '</div>' +

                                '<div class="row" id="'+ value.month_name + '-' + value.year +'-dates">' +
                                    getSubAllocations_html(value, response.data.resultCampaign.pacing) +
                                '</div>' +
                            '</div>';
                        $('#v-pills-tabContent').append(html_tabs_content);
                        $('#v-pills-tab-month-list').append(html_month_list_tabs);
                    });

                    $('.sub-allocation').trigger('keyup');

                } else {

                }

                $(".select2-multiple-days").select2({
                    placeholder: " -- Select Day(s) --",
                    dropdownParent: $('#modal-edit-sub-allocations')
                });
                $("#modal-edit-sub-allocations").modal('show');
            }
        });
    }
}

function removeSpecification(_this, specification_id) {
    if(confirm("Are you sure to remove specification?")) {
        $.ajax({
            url: $('meta[name="base-path"]').attr('content') + '/email-marketing-executive/campaign-management/remove-specification/' + specification_id,
            success: function (response){

                if(response.status === true) {
                    //remove specification from list
                    $(_this).parents('.specification-li').remove();

                    if($('.specification-li').length === 0) {
                        var html = '<li class="media d-flex m-b-15"> <a href="javascript:void(0);" class="m-b-5 d-block text-warning">No File Attached</a> </div> </li>';
                        $('#specification_ul').html(html);
                    }
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Error while processing request', response.message);
                }

                $("#reload-campaign-history").click();
            }
        });
    }
}

//Pacing Related Functions

function getDaySelection_html(pacing, value) {
    let html = '';
    let days_temp = [];
    let days = [];
    if(Array.isArray(value.days)) {
        days_temp = value.days;
    } else {
        $.each(value.days, function (key, value){
            days_temp.push(value);
        });
    }

    $.each(days_temp, function (key, value){
        days.push(parseInt(value));
    });

    switch (pacing) {
        case 'Daily':
            html += '<label for="days">Select Day(s)<span class="text-danger">*</span></label>\n' +
                '<select class="form-control btn-square select2-multiple select2-multiple-days" id="' + value.month_name + '_days" name="days[' + value.month_name + '][]" multiple="multiple" data-month="'+(parseInt(value.month-1))+'" data-year="'+ value.year +'" onChange="getHtmlPacingDates(this);">\n' +
                '   <option value="1" ' + ( (days.includes(1)) ? 'selected' : '') + '> Monday</option>\n' +
                '   <option value="2" ' + ( (days.includes(2)) ? 'selected' : '') + '> Tuesday</option>\n' +
                '   <option value="3" ' + ( (days.includes(3)) ? 'selected' : '') + '> Wednesday</option>\n' +
                '   <option value="4" ' + ( (days.includes(4)) ? 'selected' : '') + '> Thursday</option>\n' +
                '   <option value="5" ' + ( (days.includes(5)) ? 'selected' : '') + '> Friday</option>\n' +
                '   <option value="6" ' + ( (days.includes(6)) ? 'selected' : '') + '> Saturday</option>\n' +
                '   <option value="0" ' + ( (days.includes(0)) ? 'selected' : '') + '> Sunday</option>\n' +
                '</select>';
            break;
        case 'Weekly':
            html += '<label for="days">Select Day<span class="text-danger">*</span></label>\n' +
                '<select class="form-control btn-square form-control-sm" id="' + value.month_name + '_day" name="day[' + value.month_name + ']" data-month="'+(parseInt(value.month-1))+'" data-year="'+ value.year +'" onChange="getHtmlPacingDates(this);">\n' +
                '   <option value="1" ' + ( (days.includes(1)) ? 'selected' : '') + '> Monday</option>\n' +
                '   <option value="2" ' + ( (days.includes(2)) ? 'selected' : '') + '> Tuesday</option>\n' +
                '   <option value="3" ' + ( (days.includes(3)) ? 'selected' : '') + '> Wednesday</option>\n' +
                '   <option value="4" ' + ( (days.includes(4)) ? 'selected' : '') + '> Thursday</option>\n' +
                '   <option value="5" ' + ( (days.includes(5)) ? 'selected' : '') + '> Friday</option>\n' +
                '   <option value="6" ' + ( (days.includes(6)) ? 'selected' : '') + '> Saturday</option>\n' +
                '   <option value="0" ' + ( (days.includes(0)) ? 'selected' : '') + '> Sunday</option>\n' +
                '</select>';
            break;
        case 'Monthly': html = ''; break;
    }

    return html;
}

function getSubAllocations_html(data, pacing) {
    let html = '';
    let total_allocation = parseInt($("#total-sub-allocation").html());
    if(data.sub_allocations.length > 0) {
        $.each(data.sub_allocations, function(key, value){
            if(value.sub_allocation > 0) {
                total_allocation = total_allocation + parseInt(value.sub_allocation);
            }
            html += '<div class="col-md-6">\n' +
                '       <div class="input-group mb-3">\n' +
                '           <div class="input-group-prepend"><span class="input-group-text '+ ( (parseInt(value.is_holiday)) ? 'text-danger' : '' ) +'">' + moment(value.date).format('ddd DD-MMM-YYYY') + '</span></div>\n' +
                '           <input type="number" class="form-control btn-square only-non-zero-number sub-allocation" name="sub-allocation[' + value.date + ']" value="' + value.sub_allocation + '" '+ ( (parseInt(value.is_holiday)) ? ' disabled placeholder="Holiday" ' : ' placeholder="Enter Sub-Allocation" ' ) +'>\n' +
                '       </div>\n' +
                '   </div>\n';
        });
        $("#total-sub-allocation").html(total_allocation);
    } else {
        switch (pacing) {
            case 'Daily' :
                html += '';
                break;
            case 'Weekly' :
                html += '';
                break;
            case 'Monthly' :
                html += getDatesMonthlyPacing_html(data.month, data.year);
                break;
        }

    }
    return html;

}

function getHtmlPacingDates(_this) {
    let month = $(_this).data('month');
    let year = $(_this).data('year');
    let selectedDays =  $(_this).val();

    let dayArr = [];
    let allDates = [];

    if(Array.isArray(selectedDays)) {
        dayArr = selectedDays;
    } else {
        dayArr.push(selectedDays);
    }

    $.each(dayArr, function () {
        $.merge(allDates, getDaysInMonthYear(parseInt(month), parseInt(year), parseInt(this)));
    });
    allDates.sort((a, b) => a.valueOf() - b.valueOf());
    let html = '';

    $('body').find('#'+MONTHS[month]+'-'+year+'-dates').html(html);

    $.each(allDates, function () {

        let currentDate = this.getFullYear()+'-'+((this.getMonth()+1)<=9?('0'+(this.getMonth()+1)) : (this.getMonth()+1))+'-'+(this.getDate()<=9 ? '0'+this.getDate() : this.getDate());
        let disabled = '';
        let place_holder = 'Sub-Allocation';
        let text_color = '';
        let title = '';
        let date_title = '';

        if($.inArray(currentDate, HOLIDAY_LIST) !== -1) {
            disabled = ' disabled ';
            text_color = 'text-danger';
            place_holder = 'holiday';
            title = 'holiday';
            date_title = 'Holiday';
            $.each(HOLIDAYS, function (index, row) {
                if(currentDate === row.date) {
                    title = place_holder = row.title;
                }
            });

        }

        html = '<div class="col-md-6">'+
            '               <div class="input-group mb-3">'+
            '                   <div class="input-group-prepend"><span class="input-group-text '+text_color+'" title="'+date_title+'">'+DAYS[this.getDay()]+' '+this.getDate()+'-'+MONTHS[this.getMonth()]+'-'+this.getFullYear()+'</span></div>'+
            '                   <input type="number" class="form-control btn-square only-non-zero-number sub-allocation" name="sub-allocation['+currentDate+']" placeholder="'+place_holder+'" '+disabled+' title="'+title+'">'+
            '               </div>'+
            '          </div>';

        $('body').find('#'+MONTHS[month]+'-'+year+'-dates').append(html);
    });

    $('.sub-allocation').trigger('keyup');
}

function getDaysInMonthYear(month, year, weekday) {

    let date = new Date(year, month, 1);
    let days = [];
    let start_date = $("#campaign_start_date").val();
    let end_date = $("#campaign_end_date").val();
    let start = new Date(start_date);
    let end = new Date(end_date);

    while (date.getMonth() === month) {
        if(date.getDay() === weekday && (start <= date) && (end >= date)) {
            days.push(new Date(date));
        }
        date.setDate(date.getDate() + 1);
    }
    return days;
}

function getDatesMonthlyPacing_html(month, year) {
    let html = '';
    let start_date = $("#campaign_start_date").val();
    let end_date = $("#campaign_end_date").val();
    let start = new Date(start_date);
    let end = new Date(end_date);

    while (start <= end || (start.getMonth() === end.getMonth())) {
        //month = MONTHS[start.getMonth()]+'-'+start.getFullYear();
        lastDay = new Date(start.getFullYear(), start.getMonth() + 1, 0);

        if(lastDay > end) { lastDay = end; }

        secondLast = new Date(lastDay.getFullYear(), lastDay.getMonth(), lastDay.getDate() - 1);
        var secondLastDate = secondLast.getFullYear()+'-'+((secondLast.getMonth()+1)<=9?('0'+(secondLast.getMonth()+1)) : (secondLast.getMonth()+1))+'-'+(secondLast.getDate()<=9 ? '0'+secondLast.getDate() : secondLast.getDate());

        while ($.inArray(secondLastDate, HOLIDAY_LIST) !== -1) {
            secondLast = new Date(secondLast.getFullYear(), secondLast.getMonth(), secondLast.getDate() - 1);
            secondLastDate = secondLast.getFullYear()+'-'+((secondLast.getMonth()+1)<=9?('0'+(secondLast.getMonth()+1)) : (secondLast.getMonth()+1))+'-'+(secondLast.getDate()<=9 ? '0'+secondLast.getDate() : secondLast.getDate());
        }
        if(parseInt(secondLast.getMonth()+1) === parseInt(month) && parseInt(secondLast.getFullYear()) === parseInt(year)) {
            html += '<div class="col-md-6">'+
                '               <div class="input-group mb-3">'+
                '                   <div class="input-group-prepend"><span class="input-group-text">'+ DAYS[secondLast.getDay()]+' '+secondLast.getDate()+'-'+MONTHS[secondLast.getMonth()]+'-'+secondLast.getFullYear() +'</span></div>'+
                '                   <input type="number" class="form-control btn-square only-non-zero-number sub-allocation" name="sub-allocation['+secondLastDate+']" placeholder="Enter Sub-Allocation">'+
                '               </div>'+
                '          </div>';
        }

        start.setMonth( start.getMonth() + 1 );
    }

    return html;
}

function getCampaignHistory(_this) {
    $.ajax({
        //url: '{{ route('campaign.get_campaign_history', base64_encode($resultCampaign->id)) }}',
        url: URL + '/email-marketing-executive/campaign-management/get-campaign-history/' + $('meta[name="campaign-id"]').attr('content'),
        data: { skip:CAMPAIGN_HISTORY_SKIP },
        success: function(response){
            if(CAMPAIGN_HISTORY_SKIP === 0) {
                $("#campaign-history-ul").html('');
            }
            $("#campaign-history-ul").append(response);

            CAMPAIGN_HISTORY_SKIP++;
        },
        error: function(jqXHR, textStatus, errorThrown) { checkSession(jqXHR); }
    });
}

