let URL = $('meta[name="base-path"]').attr('content');
let MONTHS = ['Jan','Feb','Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
let DAYS = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

//Initializations
$(function(){

    $("#filter_job_level").select2({
        placeholder: " -- Select Job Level(s) --"
    });

    $("#filter_job_role").select2({
        placeholder: " -- Select Job Role(s) --"
    });

    $("#filter_employee_size").select2({
        placeholder: " -- Select Employee Size(s) --"
    });

    $("#filter_revenue").select2({
        placeholder: " -- Select Revenue(s) --"
    });

    $("#filter_country").select2({
        placeholder: " -- Select Country(s) --"
    });

    $("#filter_state").select2({
        placeholder: " -- Select State(s) --"
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

    $("#user_list").select2({
        placeholder: " --- Select User(s) ---",
        dropdownParent: $('#modal-assign-campaign')
    });

});


$(function(){

    $('#form-get-data-submit').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: URL + '/team-leader/campaign-assign/get-data',
            data: $('#form-get-data').serialize(),
            async : true,
            success: function (response) {
                if(response.status === true) {
                    if(response.data.length) {
                        $('#div-result-get-data').css('display','block');
                        $('#div-result-get-data').parent('div.card-footer').css('display','block');
                        $('#result-record-found').html(response.data.length);
                        $('#data_ids').val(response.data);
                    } else {
                        $('#data_ids').val('');
                    }
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    });

    $('#form-assign-data-submit').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: URL + '/team-leader/campaign-assign/assign-data',
            data: $('#form-assign-data').serialize(),
            async : true,
            success: function (response) {
                if(response.status === true) {
                    $('#result-record-found').html('');
                    $('#div-result-get-data').css('display','none');
                    $('#count-agent-data').html(response.countAgentData);
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    });

    $("#form-raise-issue").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'title' : { required : true },
            'description' : { required : true },
            'priority' : { required : true },
        },
        messages: {
            'title' : { required : 'Please enter title' },
            'description' : { required : "Please enter description" },
            'priority' : { required : "Please enter priority" },
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

    $('#form-raise-issue-submit').on('click', function (e) {
        if($("#form-raise-issue").valid()) {
            $(this).attr('disabled', 'disabled');
            $(this).html('<span class="spinner-border spinner-border-sm" role="status"></span> Processing...');
            $("#form-raise-issue").submit();
        }
    });

    $('#form-close-issue-submit').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: URL + '/team-leader/campaign-issue/update/' + $('#modal-close-issue').find('input[name="id"]').val(),
            data: $('#form-close-issue').serialize(),
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

    $('#form-assign-campaign-submit').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: URL + '/team-leader/campaign-assign/assign-campaign',
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

});

function viewAssignmentDetails(id) {
    $.ajax({
        type: 'get',
        url: URL + '/team-leader/campaign-assign/view-assignment-details/' + id,
        success: function (response){
            if(response.status === true) {
                let data = null;
                let html = '';
                if(response.data.length) {
                    $.each(response.data, function (key, value) {
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
                        $('#button-assign-campaign').data('campaign-id', btoa(parseInt(value.campaign_id)));

                        html += '' +
                            '<tr>\n' +
                            //'   <td><i class="feather icon-plus-square toggle-pacing-details" style="cursor: pointer;font-size: 17px;"></i></td>\n' +
                            '   <td> ' + (key+1) + ' </td>\n' +
                            '   <td>'+ value.user.first_name +' '+ value.user.last_name +'</td>\n' +
                            '   <td>'+ value.display_date +'</td>\n' +
                            '   <td>'+ value.allocation +'</td>\n' +
                            '   <td>'+ value.user_assigned_by.first_name +' '+ value.user_assigned_by.last_name +'</td>\n' +
                            '   <td>'+ status +'</td>\n' +
                            '   <td>'+ buttons +'</td>\n' +
                            '</tr>';
                        '';
                    });

                    $("#modal-view-assignment-details").find('tbody').html(html);

                    $("#modal-view-assignment-details").modal('show');

                } else {
                    trigger_pnofify('warning', 'Error while processing request', 'Data Not Found');
                }

            } else {
                trigger_pnofify('error', 'Error while processing request', response.message);
            }
        }
    });
}

function viewAgentLeadDetails(_ca_agent_id) {
    $.ajax({
        type: 'get',
        url: $('meta[name="base-path"]').attr('content') + '/team-leader/campaign/view-agent-lead-details/' + _ca_agent_id,
        success: function (response){
            if(response.status === true) {
                let data = null;
                let html = '';

                if(response.data.length) {
                    $.each(response.data, function (key, value) {
                        let status = '-';

                        switch (parseInt(value.status)) {
                            case 0: status = 'Rejected'; break;
                            case 1: status = 'OK'; break;
                        }
                        html += '' +
                            '<tr>\n' +
                            '   <td>' + (key + 1) + '</td>\n' +
                            '   <td>' + value.first_name + '</td>\n' +
                            '   <td>' + value.last_name + '</td>\n' +
                            '   <td>' + value.company_name + '</td>\n' +
                            '   <td>' + value.email_address + '</td>\n' +
                            '   <td>' + value.phone_number + '</td>\n' +
                            '   <td>' + value.address_1 + '</td>\n' +
                            '   <td>' + value.address_2 + '</td>\n' +
                            '   <td>' + value.city + '</td>\n' +
                            '   <td>' + value.state + '</td>\n' +
                            '   <td>' + value.zipcode + '</td>\n' +
                            '   <td>' + value.country + '</td>\n' +
                            '   <td>' + value.employee_size + '</td>\n' +
                            '   <td>' + ((value.employee_size_2) ? value.employee_size_2 : '-') + '</td>\n' +
                            '   <td>' + value.revenue + '</td>\n' +
                            '   <td>' + value.company_domain + '</td>\n' +
                            '   <td>' + value.website + '</td>\n' +
                            '   <td>' + value.company_linkedin_url + '</td>\n' +
                            '   <td>' + value.linkedin_profile_link + '</td>\n' +
                            '   <td>' + value.linkedin_profile_sn_link + '</td>\n' +
                            '   <td>' + ((value.comment) ? value.comment : '-') + '</td>\n' +
                            '   <td>' + ((value.comment_2) ? value.comment_2 : '-') + '</td>\n' +
                            '   <td>' + ((value.qc_comment) ? value.qc_comment : '-') + '</td>\n' +
                            '   <td>' + status + '</td>\n' +
                            '   <td>' + moment(value.created_at).format('DD-MMM-YYYY') + '</td>\n' +
                            '</tr>';
                        '';
                    });

                    $("#modal-view-lead-details").find('tbody').html(html);

                    $("#modal-view-lead-details").modal('show');

                } else {
                    trigger_pnofify('warning', 'Error while processing request', 'Data Not Found');
                }
            } else {
                trigger_pnofify('error', 'Error while processing request', response.message);
            }
        }
    });
}

function submitCampaign(_id) {
    if(_id && confirm('Are you sure to submit campaign?')) {
        $.ajax({
            type: 'post',
            url: URL + '/team-leader/campaign/submit-campaign/' + _id,
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    location.reload();
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    } else {

    }
}

function sendForQualityCheck(_id) {
    if(_id && confirm('Are you sure to send for quality check?')) {
        $.ajax({
            type: 'post',
            url: URL + '/team-leader/campaign-assign/send-for-quality-check/' + _id,
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

function closeCampaignIssue(_issue_id) {
    $('#modal-close-issue').find('input[name="id"]').val(_issue_id);
    $('#modal-close-issue').modal('show');
}

function revokeCampaign(_id) {
    if(_id && confirm('Are you sure to revoke campaign?')) {
        $("#modal-view-assignment-details").modal('hide');
        $.ajax({
            type: 'post',
            url: URL + '/team-leader/campaign-assign/revoke-campaign/' + _id,
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

function reAssignCampaign(_id) {
    if(_id && confirm('Are you sure to re-assign campaign?')) {
        $("#modal-view-assignment-details").modal('hide');
        $.ajax({
            type: 'post',
            url: URL + '/team-leader/campaign-assign/re-assign-campaign/' + _id,
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
