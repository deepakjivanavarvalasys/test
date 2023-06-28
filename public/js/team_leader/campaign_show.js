

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

});


$(function(){

});

function viewAssignmentDetails(id) {
    $.ajax({
        type: 'get',
        url: $('meta[name="base-path"]').attr('content') + '/team-leader/campaign-assign/view-assignment-details/' + id,
        success: function (response){
            if(response.status === true) {
                console.log(response.data);
                let data = null;
                let html = '';
                if(response.data.length) {
                    $.each(response.data, function (key, value) {
                        let status;

                        switch(parseInt(value.status)) {
                            case 1: status = 'Active';break;
                            case 0: status = 'Inactive';break;
                            case 2: status = 'Revoked';break;
                            default: status = '-';
                        }

                        html += '' +
                            '<tr>\n' +
                            //'   <td><i class="feather icon-plus-square toggle-pacing-details" style="cursor: pointer;font-size: 17px;"></i></td>\n' +
                            '   <td> ' + (key+1) + ' </td>\n' +
                            '   <td>'+ value.user.first_name +' '+ value.user.last_name +'</td>\n' +
                            '   <td>'+ value.display_date +'</td>\n' +
                            '   <td>'+ value.allocation +'</td>\n' +
                            '   <td>'+ value.user_assigned_by.first_name +' '+ value.user_assigned_by.last_name +'</td>\n' +
                            '   <td>'+ status +'</td>\n' +
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
                console.log(response.data);
                let data = null;
                let html = '';

                if(response.data.length) {
                    $.each(response.data, function (key, value) {
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
                            '   <td>' + value.revenue + '</td>\n' +
                            '   <td>' + value.company_domain + '</td>\n' +
                            '   <td>' + value.website + '</td>\n' +
                            '   <td>' + value.company_linkedin_url + '</td>\n' +
                            '   <td>' + value.linkedin_profile_link + '</td>\n' +
                            '   <td>' + value.linkedin_profile_sn_link + '</td>\n' +
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
                    //$('#div-start-campaign').css('display', 'none');
                    $('#div-submit-campaign').css('display', 'none');
                    //$('#div-start-again-campaign').css('display', 'block');
                    $('#div-manage-leads').css('display', 'none');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    } else {

    }
}
