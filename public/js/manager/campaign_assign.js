/* ------------------------------------
    Campaign List Custom Javascript
------------------------------------ */

let CAMPAIGN_TABLE;
let URL = $('meta[name="base-path"]').attr('content');
let MONTHS = ['Jan','Feb','Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

$(function (){

    $("#campaign_list").select2({
        placeholder: " --- Select Campaign ---",
    });

    $("#user_list").select2({
        placeholder: " --- Select User(s) ---",
    });

});


$(function (){

    CAMPAIGN_TABLE = $('#table-campaigns').DataTable({
        "lengthMenu": [ [25,500,250,100,50,-1], [25,500,250,100,50,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": URL + '/manager/campaign-assign/get-assigned-campaigns',
            data: {
                filters: function (){
                    let obj = {
                        start_date: $("#filter_start_date").val(),
                        end_date: $("#filter_end_date").val(),
                        campaign_status_id: $("#filter_campaign_status_id").val(),
                        delivery_day: $("#filter_delivery_day").val(),
                        due_in: $("#filter_due_in").val(),
                        country_id: $("#filter_country_id").val(),
                        region_id: $("#filter_region_id").val(),
                        campaign_type_id: $("#filter_campaign_type_id").val(),
                        campaign_filter_id: $("#filter_campaign_filter_id").val()
                    };
                    localStorage.setItem("filters", JSON.stringify(obj));
                    return JSON.stringify(obj);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) { checkSession(jqXHR); }
        },
        "columns": [
            {
                data: 'campaign_id'
            },
            {
                render: function (data, type, row) {
                    return '<a href="'+URL+'/manager/campaign-assign/view-details/'+btoa(row.id)+'" class="text-dark double-click" title="View campaign details">'+row.name+'</a>';
                }
            },
            {
                render: function (data, type, row) {
                    let total_users = 0;

                    if(row.children.length) {
                        if(row.children[0].assigned_ratls.length) {
                            total_users = row.children[0].assigned_ratls.length;
                        }
                        if(row.children[0].assigned_vendor_managers.length) {
                            total_users = row.children[0].assigned_vendor_managers.length;
                        }
                    } else {
                        if(row.assigned_ratls.length) {
                            total_users = row.assigned_ratls.length;
                        }
                        if(row.assigned_vendor_managers.length) {
                            total_users = row.assigned_vendor_managers.length;
                        }
                    }

                    return total_users;
                }
            },
            {
                render: function (data, type, row) {
                    let date = new Date(row.start_date);
                    return (date.getDate() <= 9 ? '0'+date.getDate() : date.getDate())+'/'+MONTHS[date.getMonth()]+'/'+date.getFullYear();
                }
            },
            {
                render: function (data, type, row) {
                    let date = new Date(row.end_date);
                    if(row.children.length) {
                        date = new Date(row.children[0].end_date);
                    }
                    return (date.getDate() <= 9 ? '0'+date.getDate() : date.getDate())+'/'+MONTHS[date.getMonth()]+'/'+date.getFullYear();
                }
            },
            {
                render: function (data, type, row) {
                    let completed_count = parseInt(row.completed_count);
                    let allocation = parseInt(row.allocation);
                    let shortfall_count = parseInt(row.shortfall_count);

                    if(row.children.length) {
                        $.each(row.children, function (key, value) {
                            allocation = allocation + parseInt(value.allocation);
                            completed_count = completed_count + parseInt(value.completed_count);
                            if(parseInt(value.campaign_status_id) === 6) {
                                shortfall_count = parseInt(value.shortfall_count);
                            }
                        });
                    }

                    if(shortfall_count) {
                        return completed_count + ' <span class="text-danger" title="Shortfall Count">('+ shortfall_count +')</span>'+' / '+ allocation;
                    } else {
                        return completed_count + ' / '+ allocation;
                    }

                }
            },
            {
                render: function (data, type, row) {
                    let status_id  = row.campaign_status_id;
                    let campaign_type = '';
                    if(row.children.length) {
                        status_id = row.children[0].campaign_status_id;
                        campaign_type = ' (Incremental)'
                    }
                    switch (parseInt(status_id)) {
                        case 1: return '<span class="badge badge-pill badge-success" style="padding: 5px;min-width:50px;"> Live'+campaign_type+' </span>';
                        case 2: return '<span class="badge badge-pill badge-warning" style="padding: 5px;min-width:50px;"> Paused'+campaign_type+' </span>';
                        case 3: return '<span class="badge badge-pill badge-danger" style="padding: 5px;min-width:50px;"> Cancelled'+campaign_type+' </span>';
                        case 4: return '<span class="badge badge-pill badge-primary" style="padding: 5px;min-width:50px;"> Delivered'+campaign_type+' </span>';
                        case 5: return '<span class="badge badge-pill badge-success" style="padding: 5px;min-width:50px;"> Reactivated'+campaign_type+' </span>';
                        case 6: return '<span class="badge badge-pill badge-secondary" style="padding: 5px;min-width:50px;"> Shortfall'+campaign_type+' </span>';
                    }
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let html = '';

                    html += '<a href="'+URL+'/manager/campaign-assign/view-details/'+btoa(row.id)+'" class="btn btn-outline-info btn-rounded btn-sm" title="View Campaign Details"><i class="feather icon-eye mr-0"></i></a>';

                    return html;
                }
            },
        ],
        "fnDrawCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $('.dark-left-toolbar').each(function() {
                var id = $(this).data('id');
                $(this).toolbar({
                    content: '#toolbar-options-' + id,
                    position: 'left',
                    style: 'dark'
                });
            });

            $('.double-click').click(function() {
                return false;
            }).dblclick(function() {
                window.location = this.href;
                return false;
            });
        },
        "createdRow": function(row, data, dataIndex){
            let status_id  = data.campaign_status_id;
            if(data.children.length) {
                status_id = data.children[0].campaign_status_id;
            }
            switch (parseInt(status_id)) {
                case 1:
                    $(row).addClass('border-live');
                    break;
                case 2:
                    $(row).addClass('border-paused');
                    break;
                case 3:
                    $(row).addClass('border-cancelled');
                    break;
                case 4:
                    $(row).addClass('border-delivered');
                    break;
                case 5:
                    $(row).addClass('border-reactivated');
                    break;
                case 6:
                    $(row).addClass('border-shortfall');
                    break;
            }
        },
        order:[]
    });

    //Validate Form
    $("#form-campaign-assign").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'campaign_id' : { required : true },
            'user_list[]' : { required : true },
        },
        messages: {
            'campaign_id' : { required : "Please select campaign" },
            'user_list[]' : { required : "Please select user(s)" },
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

    $("#form-campaign-user-assignment").validate({
        ignore: [],
        focusInvalid: false,
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

    $('#button-campaign-assign').on('click', function(e) {

        if($("#form-campaign-assign").valid()) {
            let campaign_list = $("#campaign_list").val();
            let user_list = $("#user_list").val();
            let html = '';

            if(!Array.isArray(campaign_list)) {
                campaign_list = [campaign_list];
            }

            $("#modal-campaign-assign").find('.modal-body').html(html);

            html = getCampaignCard_html(campaign_list, user_list);

            $("#modal-campaign-assign").find('.modal-body').html(html);

            $("#modal-campaign-assign").modal('show');

            $.each(user_list, function (key, value){
                //Dynamic Validation
                $("#form-campaign-user-assignment").find('input[name="users['+key+'][allocation]"]').rules( "add", {
                    required: true,
                    min: 1,
                    number: true,
                    messages: {
                        required: "Please enter allocation",
                        min: "Please enter allocation greater than 0",
                        number: "Please enter valid allocation"
                    }
                });

            });
        }

    });

    $("#button-reset-form-campaign-assign").on('click', function(){
        $("#form-campaign-assign").find('input').val('');
        $("#form-campaign-assign").find('select').val('').trigger('change');
    });

});


function getCampaignCard_html(_campaign_list, _user_list) {
    let html ='';

    $.each(_campaign_list, function (key, value){

        let allocation = $("#campaign_list_"+value).data('allocation')/(_user_list.length);
        let balance_allocation = $("#campaign_list_"+value).data('allocation')%(_user_list.length);

        let end_date = new Date($("#campaign_list_"+value).data('end-date'));
        let display_date = new Date(end_date);
        display_date.setDate(display_date.getDate() - 2);

        let display_date_day = display_date.getDay();

        while(display_date_day === 0 || display_date_day === 6) {
            display_date.setDate(display_date.getDate() - 1);
            display_date_day = display_date.getDay();
        }

        html += '' +
            '<div class="card border border-info rounded">' +
            '   <h5 class="card-header" style="padding: 10px 25px;">'+$("#campaign_list_"+value).data('name')+'</h5>' +
            '   <input type="hidden" name="campaign_id" value="'+value+'">' +
            '   <div class="card-body" style="padding: 15px 25px;">' +
            '       <div class="row">' +
            '           <div class="col-md-5">' +
            '               <div class="row">' +
            '                   <div class="col-md-6"><h6 class="card-title">Allocation</h6></div>' +
            '                   <div class="col-md-6"><h6 class="card-title">: '+$("#campaign_list_"+value).data('allocation')+'</h6></div>' +
            '               </div>' +
            '               <div class="row">' +
            '                   <div class="col-md-6"><h6 class="card-title">End Date</h6></div>' +
            '                   <div class="col-md-6"><h6 class="card-title">: '+$("#campaign_list_"+value).data('end-date')+'</h6></div>' +
            '               </div>' +
            '               <div class="row">' +
            '                   <div class="col-md-6"><h6 class="card-title">Display Date</h6></div>' +
            '                   <div class="col-md-6"><h6 class="card-title">: <input type="date" name="display_date" placeholder="DD/MMM/YYY" value="'+ moment(display_date). format('YYYY-MM-DD') +'"> </h6></div>' +
            '               </div>' +
            '           </div>' +
            '           <div class="col-md-7 border-left">' +
            '               <h5 class="card-title mb-2">User(s) to Assign</h5>' +
            '               <hr class="m-0" style="margin-bottom: 5px !important;">' +
                            getUserAssignCard_html(key, _user_list, allocation, balance_allocation) +
            '           </div>' +
            '       </div>' +
            '   </div>' +
            '</div>';

    });

    return html;
}

function getUserAssignCard_html(_key, _user_list, allocation, balance_allocation) {
    let html = '';
    let is_research_analyst = 0;

    $.each(_user_list, function (key, value){

        html += '<div class="row p-1">' +
            '   <div class="col-md-5"><h6 class="card-title">'+$("#user_list_"+value).data('name')+'</h6></div>' +
            '   <input type="hidden" name="users['+key+'][user_id]" value="'+value+'">' +
            '   <div class="col-md-7 form-group">' +
            '       <input type="number" id="user_allocation_'+key+'" name="users['+key+'][allocation]" class="form-control form-control-sm" required value="'+ ( (key === (_user_list.length -1)) ? Math.floor((allocation + balance_allocation)) : Math.floor(allocation) ) +'" style="height: 30px;">' +
            '   </div>' +
            '</div>';

        if($("#user_list_"+value).data('designation') === 'research_analyst') {
            is_research_analyst = 1;
        }
    });

    if(is_research_analyst) {
        html += '<div class="row p-1">' +
            '   <div class="col-md-5"><h6 class="card-title">Select Work Type: </h6></div>' +
            '   <div class="col-md-7">' +
                    $('#div-select-agent-work-types').html() +
            '   </div>' +
            '</div>';
    }

    return html;
}
