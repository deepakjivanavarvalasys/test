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
        placeholder: " --- Select User ---",
    });

});


$(function (){

    CAMPAIGN_TABLE = $('#table-campaigns').DataTable({
        "lengthMenu": [ [25,500,250,100,50,-1], [25,500,250,100,50,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": URL + '/qa-team-leader/campaign-assign/get-assigned-campaigns',
            data: {
                filters: function (){
                    let obj = {
                    };
                    localStorage.setItem("filters", JSON.stringify(obj));
                    return JSON.stringify(obj);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) { checkSession(jqXHR); }
        },
        "columns": [
            {
                orderable: false,
                data: 'campaign.campaign_id'
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    return '<a href="'+URL+'/qa-team-leader/campaign-assign/view-details/'+btoa(row.id)+'" class="text-dark double-click" title="View campaign details">'+row.campaign.name+'</a>';
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let user_names = [];
                    $.each(row.quality_analysts, function (key, value) {
                        user_names[key] = value.user.full_name;
                    });

                    return user_names.join(', <br>');

                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let date = new Date(row.campaign.start_date);
                    return (date.getDate() <= 9 ? '0'+date.getDate() : date.getDate())+'/'+MONTHS[date.getMonth()]+'/'+date.getFullYear();
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let date = new Date(row.display_date);
                    return (date.getDate() <= 9 ? '0'+date.getDate() : date.getDate())+'/'+MONTHS[date.getMonth()]+'/'+date.getFullYear();
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    return row.campaign.allocation

                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let status_id  = row.campaign.campaign_status_id;
                    let campaign_type = '';
                    if(row.campaign.parent_id) {
                        status_id = row.campaign.campaign_status_id;
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

                    html += '<a href="'+URL+'/qa-team-leader/campaign-assign/view-details/'+btoa(row.id)+'" class="btn btn-outline-info btn-rounded btn-sm" title="View Campaign Details"><i class="feather icon-eye mr-0"></i></a>';

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
            let status_id  = data.campaign.campaign_status_id;
            if(data.campaign.children.length) {
                status_id = data.campaign.children[0].campaign_status_id;
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
            'campaign_list' : { required : true },
            'user_list' : { required : true },
        },
        messages: {
            'campaign_list' : { required : "Please select campaign" },
            'user_list' : { required : "Please select user" },
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

    $('#button-campaign-assign').on('click', function(e) {
        e.preventDefault();
        if($("#form-campaign-assign").valid()) {
            let campaign_id = $("#campaign_list").val();
            let user_id = $("#user_list").val();
            let html = '';

            if(campaign_id.length !== 0 || user_id.length !== 0) {

                $("#modal-campaign-assign").find('.modal-body').html(html);

                html = '<div class="card border border-info rounded">' +
                    '   <h5 class="card-header" style="padding: 10px 25px;">'+$("#campaign_list_"+campaign_id).data('name')+'</h5>' +
                    '   <input type="hidden" name="ca_qatl_id" value="'+$("#campaign_list_"+campaign_id).data('caqatl-id')+'">' +
                    '   <input type="hidden" name="campaign_id" value="'+campaign_id+'">' +
                    '   <input type="hidden" name="display_date" value="'+ $("#campaign_list_"+campaign_id).data('end-date') +'">' +
                    '   <input type="hidden" name="user_id" value="'+user_id+'">' +
                    '   <div class="card-body" style="padding: 15px 25px;">' +
                    '       <div class="row">' +
                    '           <div class="col-md-6">' +
                    '               <div class="row">' +
                    '                   <div class="col-md-5"><h6 class="card-title">Allocation</h6></div>' +
                    '                   <div class="col-md-7"><h6 class="card-title">: '+$("#campaign_list_"+campaign_id).data('allocation')+'</h6></div>' +
                    '               </div>' +
                    '               <div class="row">' +
                    '                   <div class="col-md-5"><h6 class="card-title">End Date</h6></div>' +
                    '                   <div class="col-md-7"><h6 class="card-title">: '+$("#campaign_list_"+campaign_id).data('end-date')+'</h6></div>' +
                    '               </div>' +
                    '           </div>' +
                    '           <div class="col-md-6 border-left">' +
                    '               <h5 class="card-title mb-2">User to Assign</h5>' +
                    '               <hr class="m-0" style="margin-bottom: 5px !important;">' +
                    '               <div class="row p-1">' +
                    '                   <div class="col-md-12"><h6 class="card-title">'+$("#user_list_"+user_id).data('name')+'</h6></div>' +
                    '               </div>' +
                    '           </div>' +
                    '       </div>' +
                    '   </div>' +
                    '</div>';
                $("#modal-campaign-assign").find('.modal-body').html(html);

                $("#modal-campaign-assign").modal('show');
            } else {
                trigger_pnofify('error', 'Invalid data', 'Please select campaign and user to assign');
            }
        }
    });

    $("#form-campaign-assign-reset").on('click', function(){
        $("#form-campaign-assign").find('input').val('');
        $("#form-campaign-assign").find('select').val('').trigger('change');
    });

});
