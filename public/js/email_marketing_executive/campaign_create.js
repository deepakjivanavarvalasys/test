/* ------------------------------------
    Campaign Create Custom Javascript
------------------------------------ */
//Declarations
let URL = $('meta[name="base-path"]').attr('content');
let MONTHS = ['Jan','Feb','Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
let DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
let HOLIDAYS = [];
let HOLIDAY_LIST = [];

// Initializations
$(function (){

    $.ajax({
        url: URL + '/email-marketing-executive/holiday/get-holiday-list',
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


    $("#country_id").select2({
        placeholder: " -- Select Country(s) --",
    });

    $("#region_id").select2({
        placeholder: " -- Region(s) --",
    });

    $('#start_date').bootstrapMaterialDatePicker({
        weekStart: 0,
        time: false,
        format: 'D-MMM-YYYY',
        switchOnClick : true,
    }).on('change', function(e, date) {
        $('#end_date').bootstrapMaterialDatePicker('setMinDate', date);
    });

    $('#end_date').bootstrapMaterialDatePicker({
        weekStart: 0,
        time: false,
        format: 'D-MMM-YYYY',
        switchOnClick : true,
    }).on('change', function(e, date) {
        $('#start_date').bootstrapMaterialDatePicker('setMaxDate', date);
    });

    $('body').on("input", ".only-non-zero-number", function (){
        if(this.value < 1) {
            $(this).val('');
        } else {
            $(this).val(parseInt(this.value));
        }
    });

});


$(function (){

    // classic editor
    ClassicEditor.create(document.querySelector('.classic-editor')).catch(error => {
        console.error(error);
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

    //update sub-allocation total count
    $("#allocation").on('keyup change', function () {
        $('input[name="pacing"]').prop('checked', false);
        resetPacingDetails();
        let allocation = ($(this).val() > 0) ? $(this).val() : '0';
        $("#text-allocation").html(' / '+allocation);
    });

    //Reset Pacing & Sub Allocation
    $('body').on('change', '#start_date, #end_date', function () {
        $('input[type=radio][name=pacing]').prop('checked', false);
        resetPacingDetails();
    });

    //Select pacing
    $('body').on('change','input[type=radio][name=pacing]', function() {

        resetPacingDetails();

        let start_date = $("#start_date").val();
        let end_date = $("#end_date").val();

        if(start_date.length > 0 && end_date.length > 0) {
            let start = new Date(start_date);
            let end = new Date(end_date);

            var month = '';
            var html = '';

            switch ($(this).val()) {
                case 'Daily':
                    var start_loop_date = new Date(start_date);
                    while (start_loop_date <= end) {
                        month = MONTHS[start_loop_date.getMonth()]+'-'+start_loop_date.getFullYear();
                        $("#v-pills-tab").append('<li><a class="nav-link text-left" id="v-pills-'+month+'-tab" data-toggle="pill" href="#v-pills-'+month+'" role="tab" aria-controls="v-pills-'+month+'" aria-selected="false">'+month+'</a></li>');
                        html = '<div class="tab-pane fade" id="v-pills-'+month+'" role="tabpanel" aria-labelledby="v-pills-'+month+'-tab">'+
                            '       <div class="row">'+
                            '           <div class="col-md-6 form-group">'+
                            '               <label for="days">Select Day(s)<span class="text-danger">*</span></label>'+
                            '               <select class="form-control btn-square select2-multiple select2-multiple-days" id="'+month+'_days" name="days['+month+'][]" multiple="multiple" data-month="'+start_loop_date.getMonth()+'" data-year="'+start_loop_date.getFullYear()+'" onChange="getHtmlPacingDates(this);">'+
                            '                   <option value="1"> Monday</option>'+
                            '                   <option value="2"> Tuesday</option>'+
                            '                   <option value="3"> Wednesday</option>'+
                            '                   <option value="4"> Thursday</option>'+
                            '                   <option value="5"> Friday</option>'+
                            '                   <option value="6"> Saturday</option>'+
                            '                   <option value="0"> Sunday</option>'+
                            '               </select>'+
                            '           </div>'+
                            '       </div>'+
                            '       <div class="row" id="'+month+'-dates">'+
                            '       </div>'+
                            '    </div>';
                        $("#v-pills-tabContent").append(html);
                        $(".select2-multiple-days").select2({
                            placeholder: " -- Select Day(s) --",
                        });
                        start_loop_date.setDate(1);
                        start_loop_date.setMonth( start_loop_date.getMonth() + 1 );
                    }
                    break;
                case 'Weekly':
                    var start_loop_date = new Date(start_date);
                    while (start_loop_date <= end) {
                        month = MONTHS[start_loop_date.getMonth()]+'-'+start_loop_date.getFullYear();
                        $("#v-pills-tab").append('<li><a class="nav-link text-left" id="v-pills-'+month+'-tab" data-toggle="pill" href="#v-pills-'+month+'" role="tab" aria-controls="v-pills-'+month+'" aria-selected="false">'+month+'</a></li>');
                        html = '<div class="tab-pane fade" id="v-pills-'+month+'" role="tabpanel" aria-labelledby="v-pills-'+month+'-tab">'+
                            '       <div class="row">'+
                            '           <div class="col-md-6 form-group">'+
                            '               <label for="days">Select Day<span class="text-danger">*</span></label>'+
                            '               <select class="form-control btn-square form-control-sm" id="'+month+'_day" name="day['+month+']" data-month="'+start_loop_date.getMonth()+'" data-year="'+start_loop_date.getFullYear()+'" onChange="getHtmlPacingDates(this);">'+
                            '                   <option value="">-- Select Day --</option>'+
                            '                   <option value="1"> Monday</option>'+
                            '                   <option value="2"> Tuesday</option>'+
                            '                   <option value="3"> Wednesday</option>'+
                            '                   <option value="4"> Thursday</option>'+
                            '                   <option value="5"> Friday</option>'+
                            '                   <option value="6"> Saturday</option>'+
                            '                   <option value="0"> Sunday</option>'+
                            '               </select>'+
                            '           </div>'+
                            '       </div>'+
                            '       <div class="row" id="'+month+'-dates">'+
                            '       </div>'+
                            '    </div>';
                        $("#v-pills-tabContent").append(html);
                        start_loop_date.setDate(1);
                        start_loop_date.setMonth( start_loop_date.getMonth() + 1 );
                    }
                    break;
                case 'Monthly':
                    while (start <= end || (start.getMonth() === end.getMonth())) {
                        month = MONTHS[start.getMonth()]+'-'+start.getFullYear();
                        lastDay = new Date(start.getFullYear(), start.getMonth() + 1, 0);

                        if(lastDay > end) { lastDay = end; }

                        secondLast = new Date(lastDay.getFullYear(), lastDay.getMonth(), lastDay.getDate() - 1);
                        var secondLastDate = secondLast.getFullYear()+'-'+((secondLast.getMonth()+1)<=9?('0'+(secondLast.getMonth()+1)) : (secondLast.getMonth()+1))+'-'+(secondLast.getDate()<=9 ? '0'+secondLast.getDate() : secondLast.getDate());

                        while ($.inArray(secondLastDate, HOLIDAY_LIST) !== -1) {
                            secondLast = new Date(secondLast.getFullYear(), secondLast.getMonth(), secondLast.getDate() - 1);
                            secondLastDate = secondLast.getFullYear()+'-'+((secondLast.getMonth()+1)<=9?('0'+(secondLast.getMonth()+1)) : (secondLast.getMonth()+1))+'-'+(secondLast.getDate()<=9 ? '0'+secondLast.getDate() : secondLast.getDate());
                        }

                        $("#v-pills-tab").append('<li><a class="nav-link text-left" id="v-pills-'+month+'-tab" data-toggle="pill" href="#v-pills-'+month+'" role="tab" aria-controls="v-pills-'+month+'" aria-selected="false">'+month+'</a></li>');
                        html = '<div class="tab-pane fade" id="v-pills-'+month+'" role="tabpanel" aria-labelledby="v-pills-'+month+'-tab">'+
                            '       <div class="row" id="'+month+'-dates">'+
                            '           <div class="col-md-8">'+
                            '               <div class="input-group mb-3">'+
                            '                   <div class="input-group-prepend"><span class="input-group-text">'+DAYS[secondLast.getDay()]+' '+secondLast.getDate()+'-'+MONTHS[secondLast.getMonth()]+'-'+secondLast.getFullYear()+'</span></div>'+
                            '                   <input type="number" class="form-control btn-square only-non-zero-number sub-allocation" name="sub-allocation['+secondLastDate+']" placeholder="Enter Sub-Allocation">'+
                            '               </div>'+
                            '          </div>'+
                            '       </div>';
                        $("#v-pills-tabContent").append(html);
                        start.setMonth( start.getMonth() + 1 );
                    }
                    break;
            }

            $("#div_pacing_details").show();

        } else {
            $(this).prop('checked', false);
            alert('Select Start Date & End Date');
        }
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
        if(total > parseInt($("#allocation").val())) {
            $(this).val('');
            $(this).keyup();
        }
    });

    //Validate Form
    $("#form-campaign-create").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'name' : { required : true },
            'v_mail_campaign-management_id' : {
                required: false,
                remote : {
                    url : $('meta[name="base-path"]').attr('content')+'/email-marketing-executive/campaign-management/validate-v-mail-campaign-id'
                }
            },
            'campaign_filter_id' : { required : true },
            'campaign_type_id' : { required : true },
            'country_id[]' : { required : true },
            'start_date' : { required : true },
            'end_date' : { required : true },
            'allocation' : { required : true },
            'campaign_status_id' : { required : true },
            'pacing' : { required : true },
            'suppression_email' : { extension: "xlsx" },
            'suppression_domain' : { extension: "xlsx" },
            'suppression_account_name' : { extension: "xlsx" },
            'target_domain' : { extension: "xlsx" },
            'target_account_name' : { extension: "xlsx" },

        },
        messages: {
            'name' : { required : "Please enter campaign name" },
            'v_mail_campaign_id' : {
                remote : "V-Mail Campaign Id already exists"
            },
            'campaign_filter_id' : { required : "Please select campaign filter" },
            'campaign_type_id' : { required : "Please select campaign tye" },
            'country_id[]' : { required : "Please select country(s)" },
            'start_date' : { required : "Please select start date" },
            'end_date' : { required : "Please select end date" },
            'allocation' : { required : "Please enter allocation" },
            'campaign_status_id' : { required : "Please select campaign status" },
            'pacing' : { required : "Please select pacing" },
            'suppression_email' : {  extension: "Please upload valid file, (xlsx)" },
            'suppression_domain' : { extension: "Please upload valid file, (xlsx)" },
            'suppression_account_name' : { extension: "Please upload valid file, (xlsx)" },
            'target_domain' : { extension: "Please upload valid file, (xlsx)" },
            'target_account_name' : { extension: "Please upload valid file, (xlsx)" },

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

});

function resetPacingDetails()
{
    $("#v-pills-tab").html('');
    $("#div_pacing_details").hide();
    $("#v-pills-tabContent").html('');
    resetSubAllocation();
}

function resetSubAllocation()
{
    $("#total-sub-allocation").html(0);
}

function getHtmlPacingDates(_this) {
    var month = $(_this).data('month');
    var year = $(_this).data('year');
    var selectedDays =  $(_this).val();

    var dayArr = [];
    var allDates = [];

    if(Array.isArray(selectedDays)) {
        dayArr = selectedDays;
    } else {
        dayArr.push(selectedDays);
    }

    $.each(dayArr, function () {
        $.merge(allDates, getDaysInMonthYear(parseInt(month), parseInt(year), parseInt(this)));
    });

    var html = '';

    $('body').find('#'+MONTHS[month]+'-'+year+'-dates').html(html);

    $.each(allDates, function () {

        var currentDate = this.getFullYear()+'-'+((this.getMonth()+1)<=9?('0'+(this.getMonth()+1)) : (this.getMonth()+1))+'-'+(this.getDate()<=9 ? '0'+this.getDate() : this.getDate());
        var disabled = '';
        var place_holder = 'Sub-Allocation';
        var text_color = '';
        var title = '';
        var date_title = '';

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
}

function getDaysInMonthYear(month, year, weekday) {
    var date = new Date(year, month, 1);
    var days = [];
    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();
    var start = new Date(start_date);
    var end = new Date(end_date);
    while (date.getMonth() === month) {
        if(date.getDay() == weekday && (start <= date) && (end >= date)) {
            days.push(new Date(date));
        }
        date.setDate(date.getDate() + 1);
    }
    return days;
}


