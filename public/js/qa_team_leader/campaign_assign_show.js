

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

    $("#user_list").select2({
        placeholder: " --- Select User ---",
        dropdownParent: $('#modal-assign-campaign')
    });

});

$(function(){

    $("#form-submit-campaign").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'delivery_file' : {
                required : true,
                extension: "xlsx"
            },
        },
        messages: {
            'delivery_file' : {
                required : "Please upload file",
                extension: "Please upload valid file, (xlsx)",
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

    $('#form-submit-campaign-submit').on('click', function (e) {
        e.preventDefault();
        if($("#form-submit-campaign").valid()) {
            let form_data = new FormData($('#form-submit-campaign')[0]);
            $.ajax({
                url: URL +'/qa-team-leader/campaign-assign/submit-campaign/' + $('meta[name="ca-qatl-id"]').attr('content'),
                processData: false,
                contentType: false,
                data: form_data,
                type: 'post',
                success: function(response) {
                    if(response.status === true) {
                        $('#div-download-delivery-file').css('display', 'none');
                        $('#div-submit-campaign').css('display', 'none');
                        $('#modal-submit-campaign').modal('hide');
                        trigger_pnofify('success', 'Successful', response.message);
                    } else {
                        $('#modal-submit-campaign').modal('hide');
                        trigger_pnofify('error', 'Error while processing request', response.message);
                    }
                }
            });
        }
    });

    $('#form-assign-campaign-submit').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: URL + '/qa-team-leader/campaign-assign/assign-campaign',
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

function downloadDelivery(_id) {
    if(_id && confirm('Are you sure to download campaign NPF?')) {
        $.ajax({
            type: 'post',
            url: URL + '/quality-analyst/campaign/download-npf/' + _id,
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                    return window.location.href = URL + response.file_name;
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    } else {

    }
}

function viewAssignmentDetails(_id) {
    $.ajax({
        type: 'get',
        url: URL + '/qa-team-leader/campaign-assign/view-assignment-details/' + _id,
        success: function (response){
            if(response.status === true) {
                console.log(response.data);
                let data = null;
                let html = '';
                if(response.data.length) {
                    console.log(response.data);
                    $.each(response.data, function (key, value) {
                        let status = '-';
                        let buttons = '';
                        let ca_qa_id = $('meta[name="ca-qa-id"]').attr('content');
                        switch(parseInt(value.status)) {
                            case 1:
                                status = 'Active';
                                if(ca_qa_id.length > 0 && (parseInt(atob(ca_qa_id)) !== value.id)) {
                                    buttons += '<a href="javascript:void(0);" onclick="revokeCampaign(\''+btoa(value.id)+'\');" class="btn btn-outline-danger btn-sm btn-rounded mb-0" title="Revoke Campaign" style="padding: 5px 8px;"><i class="feather icon-refresh-cw mr-0"></i></a>';
                                }
                                break;
                            case 0:
                                status = 'Inactive';
                                break;
                            case 2:
                                status = 'Revoked';
                                if(ca_qa_id.length > 0 && (parseInt(atob(ca_qa_id)) !== value.id)) {

                                } else {
                                    buttons += '<a href="javascript:void(0);" onclick="reAssignCampaign(\''+btoa(value.id)+'\');" class="btn btn-outline-success btn-sm btn-rounded mb-0" title="Re-Assign Campaign" style="padding: 5px 8px;"><i class="feather icon-refresh-cw mr-0"></i></a>';
                                }
                                break;
                        }

                        html += '' +
                            '<tr>\n' +
                            '   <td>' + (key+1) + '</td>\n' +
                            '   <td>'+ value.user.full_name + '</td>\n' +
                            '   <td>'+ value.started_at + '</td>\n' +
                            '   <td>'+ ((value.submitted_at) ? 'Submitted' : 'Working') + '</td>\n' +
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

function revokeCampaign(_id) {
    if(_id && confirm('Are you sure to revoke campaign?')) {
        $("#modal-view-assignment-details").modal('hide');
        $.ajax({
            type: 'post',
            url: URL + '/qa-team-leader/campaign-assign/revoke-campaign/' + _id,
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
            url: URL + '/qa-team-leader/campaign-assign/re-assign-campaign/' + _id,
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
        $('#modal-assign-campaign').find('input[name="display_date"]').val($('#button-assign-campaign').data('display-date'));
        $("#modal-view-assignment-details").modal('hide');
        $("#modal-assign-campaign").modal('show');
    } else {

    }
}
