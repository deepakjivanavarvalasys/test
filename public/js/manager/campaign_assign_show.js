let URL = $('meta[name="base-path"]').attr('content');
let MONTHS = ['Jan','Feb','Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
let DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

//Initializations
$(function(){

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

    $('.toggle-agent-details').click(function (){
        if($(this).hasClass('icon-plus-square')) {
            $(this).removeClass('icon-plus-square').addClass('icon-minus-square');
            $(this).parents('tr').next('tr').show(1000);
        } else {
            $(this).removeClass('icon-minus-square').addClass('icon-plus-square');
            $(this).parents('tr').next('tr').hide(500);
        }
    });

    $("#user_list").select2({
        placeholder: " --- Select User(s) ---",
        dropdownParent: $('#modal-assign-campaign')
    });

});


$(function(){

    $('#form-update-delivery-details-submit').on('click', function (e) {
        e.preventDefault();
        let form_data = new FormData($('#form-update-delivery-details')[0]);

        $.ajax({
            url: URL +'/manager/campaign-assign/update-delivery-details',
            processData: false,
            contentType: false,
            data: form_data,
            type: 'post',
            success: function(response) {
                if(response.status === true) {
                    $('#modal-update-delivery-details').modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Error while processing request', response.message);
                }
            }
        });

        document.getElementById("modal-form-attach-specification").reset();

    });

    $('#form-close-issue-submit').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: URL + '/manager/campaign-issue/update/' + $('#modal-close-issue').find('input[name="id"]').val(),
            data: $('#form-close-issue').serialize(),
            async : true,
            success: function (response) {
                if(response.status === true) {
                    $("#modal-close-issue").modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                    window.location.reload();
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    });

    $('#form-assign-campaign-submit').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: URL + '/manager/campaign-assign/assign-campaign',
            data: $('#form-assign-campaign').serialize(),
            async : true,
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                    window.location.reload();
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    });

    $('#user_list').on('change', function (e) {
        if($(this).val()) {
            let designation_array = [];
            $.each($(this).val(), function (key, value) {
                designation_array[key] = $('#user_list_'+value).data('designation');
            });
            if(designation_array.includes('research_analyst') || designation_array.includes('email_marketing_executive')) {
                $('#div-select-work-type').css('display', 'block');
                $('#div-select-work-type').find('select[name="agent_work_type_id"]').removeAttr('disabled');
            } else {
                $('#div-select-work-type').css('display', 'none');
                $('#div-select-work-type').find('select[name="agent_work_type_id"]').attr('disabled', 'disabled');
            }
        }
    });

});

function viewAssignmentDetails(id) {
    $.ajax({
        type: 'get',
        url: $('meta[name="base-path"]').attr('content') + '/manager/campaign-assign/view-assignment-details/' + id,
        success: function (response){
            if(response.status === true) {

                let data = null;
                let html = '';

                if(response.data.resultRATLs.length) {
                    data = response.data.resultRATLs;
                    $.each(data, function (key, value) {
                        let status = '-';
                        let buttons = '';

                        switch(parseInt(value.status)) {
                            case 1:
                                status = 'Active';
                                buttons += '<a href="javascript:void(0);" onclick="revokeCampaign(\''+btoa(value.id)+'\');" class="btn btn-outline-danger btn-sm btn-rounded mb-0" title="Revoke Campaign" style="padding: 5px 8px;"><i class="feather icon-refresh-cw mr-0"></i></a>';
                                break;
                            case 0:
                                status = 'Inactive';
                                break;
                            case 2:
                                status = 'Revoked';
                                buttons += '<a href="javascript:void(0);" onclick="reAssignCampaign(\''+btoa(value.id)+'\');" class="btn btn-outline-success btn-sm btn-rounded mb-0" title="Re-Assign Campaign" style="padding: 5px 8px;"><i class="feather icon-refresh-cw mr-0"></i></a>';
                                break;
                        }

                        $('#button-assign-campaign').data('display-date', value.display_date);

                        html += '' +
                            '<tr>\n' +
                            '   <td><i class="feather icon-plus-square toggle-agent-details" style="cursor: pointer;font-size: 17px;" onclick="getAssignedAgents('+ value.id +', this);"></i></td>\n' +
                            '   <td>'+ value.user.first_name +' '+ value.user.last_name +'</td>\n' +
                            '   <td>'+ value.display_date +'</td>\n' +
                            '   <td>'+ value.allocation +'</td>\n' +
                            '   <td>'+ value.agents.length +'</td>\n' +
                            '   <td>'+ value.user_assigned_by.first_name +' '+ value.user_assigned_by.last_name +'</td>\n' +
                            '   <td>'+ status +'</td>\n' +
                            '   <td>'+ buttons +'</td>\n' +
                            '</tr>' +
                            '<tr id="agent-details-'+value.id+'" class="agent-details" style="display: none;">' +
                            '   <td colspan="7" class="bg-light text-left">' +
                            '       <table class="table table-hover foo-table text-center">\n' +
                            '           <thead>\n' +
                            '               <tr class="text-uppercase">\n' +
                            '                   <th class="text-center">Name</th>\n' +
                            '                   <th class="text-center">End Date</th>\n' +
                            '                   <th class="text-center">Allocation</th>\n' +
                            '                   <th class="text-center">Assigned By</th>\n' +
                            '                   <th class="text-center">Status</th>\n' +
                            '               </tr>\n' +
                            '           </thead>' +
                            '           <tbody>' +
                            '           </tbody>' +
                            '       </table>' +
                            '   </td>' +
                            '</tr>' +
                            '';
                    });
                }

                if(response.data.resultVMs.length) {
                    $('#button-assign-campaign').css('display', 'none');
                    data = response.data.resultVMs;
                    $.each(data, function (key, value) {
                        html += '' +
                            '<tr>\n' +
                            '   <td><i class="feather icon-plus-square toggle-pacing-details" style="cursor: pointer;font-size: 17px;" onclick="getAssignedVendors('+ value.id +', this)"></i></td>\n' +
                            '   <td>'+ value.user.first_name +' '+ value.user.last_name +'</td>\n' +
                            '   <td>'+ value.display_date +'</td>\n' +
                            '   <td>'+ value.allocation +'</td>\n' +
                            '   <td>'+ value.vendors.length +'</td>\n' +
                            '   <td>'+ value.user_assigned_by.first_name +' '+ value.user_assigned_by.last_name +'</td>\n' +
                            '   <td></td>\n' +
                            '<tr id="vendor-details-'+value.id+'" class="vendor-details" style="display: none;">' +
                            '   <td colspan="8" class="bg-light text-left pt-1">' +
                            '       <table class="table table-hover foo-table text-center border border-info">\n' +
                            '           <thead>\n' +
                            '               <tr class="text-uppercase table-info">\n' +
                            '                   <th class="text-center">Name</th>\n' +
                            '                   <th class="text-center">Designation</th>\n' +
                            '                   <th class="text-center">End Date</th>\n' +
                            '                   <th class="text-center">Allocation</th>\n' +
                            '                   <th class="text-center">Assigned By</th>\n' +
                            '                   <th class="text-center">Status</th>\n' +
                            '               </tr>\n' +
                            '           </thead>' +
                            '           <tbody>' +
                            '           </tbody>' +
                            '       </table>' +
                            '   </td>' +
                            '</tr>' +
                            '</tr>';
                        '';
                    });
                }

                $('#button-assign-campaign').data('campaign-id', id);


                $("#modal-view-assignment-details").find('tbody').html(html);

                $("#modal-view-assignment-details").modal('show');
            } else {
                trigger_pnofify('error', 'Error while processing request', response.message);
            }
        }
    });
}

function getAssignedAgents(_id, _this) {
    let html = '';
    if($(_this).hasClass('icon-plus-square')) {
        $.ajax({
            type: 'get',
            url: $('meta[name="base-path"]').attr('content') + '/manager/campaign-assign/view-assigned-agents/' + btoa(_id),
            success: function (response){
                if(response.status === true) {

                    if(response.data.length) {
                        $.each(response.data, function (key, value) {
                            let status;

                            switch(parseInt(value.status)) {
                                case 1:
                                    status = 'Active';
                                    break;
                                case 0:
                                    status = 'Inactive';
                                    break;
                                case 2:
                                    status = 'Revoked';
                                    break;
                                default:
                                    status = '-';
                                    break;
                            }

                            html += '' +
                                '<tr>\n' +
                                '   <td>'+ value.user.first_name +' '+ value.user.last_name +'</td>\n' +
                                '   <td>'+ value.display_date +'</td>\n' +
                                '   <td>'+ value.allocation +'</td>\n' +
                                '   <td>'+ value.user_assigned_by.first_name +' '+ value.user_assigned_by.last_name +'</td>\n' +
                                '   <td>'+ status +'</td>\n' +
                                '</tr>' +
                                '';
                        });
                        $("#agent-details-"+_id).find('tbody').html(html);
                    } else {
                        $("#agent-details-"+_id).find('tbody').html('<tr><td colspan="4">Campaign not assigned to agents.</td></tr>');
                    }

                } else {
                    trigger_pnofify('error', 'Error while processing request', response.message);
                }
            }
        });
        $(_this).removeClass('icon-plus-square').addClass('icon-minus-square');
        $(_this).parents('tr').next('tr').show(1000);
    } else {
        $(_this).removeClass('icon-minus-square').addClass('icon-plus-square');
        $(_this).parents('tr').next('tr').hide(500);
    }


    return html;
}

function getAssignedVendors(_id, _this) {
    let html = '';
    if($(_this).hasClass('icon-plus-square')) {
        $.ajax({
            type: 'get',
            url: $('meta[name="base-path"]').attr('content') + '/manager/campaign-assign/view-assigned-vendors/' + btoa(_id),
            success: function (response){
                if(response.status === true) {

                    if(response.data.length) {
                        $.each(response.data, function (key, value) {
                            html += '' +
                                '<tr>\n' +
                                '   <td>'+ value.user.name +'</td>\n' +
                                '   <td>'+ value.user.designation +'</td>\n' +
                                '   <td>'+ value.display_date +'</td>\n' +
                                '   <td>'+ value.allocation +'</td>\n' +
                                '   <td>'+ value.user_assigned_by.first_name +' '+ value.user_assigned_by.last_name +'</td>\n' +
                                '</tr>' +
                                '';
                        });
                        $("#vendor-details-"+_id).find('tbody').html(html);
                    } else {
                        $("#vendor-details-"+_id).find('tbody').html('<tr><td colspan="4">Campaign not assigned to agents.</td></tr>');
                    }

                } else {
                    trigger_pnofify('error', 'Error while processing request', response.message);
                }
            }
        });
        $(_this).removeClass('icon-plus-square').addClass('icon-minus-square');
        $(_this).parents('tr').next('tr').show(1000);
    } else {
        $(_this).removeClass('icon-minus-square').addClass('icon-plus-square');
        $(_this).parents('tr').next('tr').hide(500);
    }


    return html;
}

function updateDeliveryDetails(_campaign_id) {
    $.ajax({
        type: 'get',
        url: URL + '/manager/campaign-assign/get-campaign-details/' + _campaign_id,
        success: function (response){
            if(response.status === true) {
                $("#modal-update-delivery-details").find('#campaign_id').val(_campaign_id);
                if (response.data.delivery_detail) {
                    let delivery_detail = response.data.delivery_detail;
                    $("#form-update-delivery-details").find('input[name="id"]').val(btoa(delivery_detail.id));
                    $("#form-update-delivery-details").find('input[name="lead_sent"]').val(delivery_detail.lead_sent);
                    $("#form-update-delivery-details").find('input[name="lead_approved"]').val(delivery_detail.lead_approved);
                    $("#form-update-delivery-details").find('input[name="lead_available"]').val(delivery_detail.lead_available);
                }
                $("#modal-update-delivery-details").modal('show');
            } else {
                trigger_pnofify('error', 'Error while processing request', response.message);
            }
        }
    });
}

function closeCampaignIssue(_issue_id) {
    $('#modal-close-issue').find('input[name="id"]').val(_issue_id);
    $('#modal-close-issue').modal('show');
}

function revokeCampaign(_id) {
    if(_id && confirm('Are you sure to revoke campaign?')) {
        $("#modal-view-assignment-details").modal('hide');
        $.ajax({
            type: 'post',
            url: URL + '/manager/campaign-assign/revoke-campaign/' + _id,
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    } else {

    }
}

function reAssignCampaign(_id) {
    if(_id && confirm('Are you sure to re-assign campaign?')) {
        $("#modal-view-assignment-details").modal('hide');
        $.ajax({
            type: 'post',
            url: URL + '/manager/campaign-assign/re-assign-campaign/' + _id,
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                    location.reload();
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    } else {

    }
}

function assignCampaign() {
    if(confirm('Are you sure to assign campaign?')) {
        $('#modal-assign-campaign').find('input[name="campaign_id"]').val($('#button-assign-campaign').data('campaign-id'));
        $('#modal-assign-campaign').find('input[name="display_date"]').val($('#button-assign-campaign').data('display-date'));
        $("#modal-view-assignment-details").modal('hide');
        $("#modal-assign-campaign").modal('show');
    } else {

    }
}
