

let URL = $('meta[name="base-path"]').attr('content');

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
    $('#form-submit-campaign-submit').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: URL + '/vendor-manager/ra/campaign/submit-campaign/' + $('#modal-submit-campaign').find('input[name="ca_agent_id"]').val(),
            data: $('#form-submit-campaign').serialize(),
            async : true,
            success: function (response) {
                if(response.status === true) {
                    $("#modal-submit-campaign").modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                    window.location.reload();
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });

        document.getElementById("modal-form-attach-specification").reset();

    });
});

function startCampaign(_id) {
    if(_id && confirm('Are you sure to start campaign?')) {
        $.ajax({
            type: 'post',
            url: URL + '/vendor-manager/ra/campaign/start-campaign/' + _id,
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    $('#div-start-campaign').css('display', 'none');
                    $('#div-submit-campaign').css('display', 'block');
                    $('#div-manage-leads').css('display', 'block');
                    trigger_pnofify('success', 'Successful', 'Campaign Started.');
                    window.location.reload();
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    } else {

    }
}

function submitCampaign(_id) {
    if(_id && confirm('Are you sure to submit campaign?')) {
        $.ajax({
            type: 'post',
            url: URL + '/vendor-manager/ra/campaign/submit-campaign/' + _id,
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    $('#div-start-campaign').css('display', 'none');
                    $('#div-submit-campaign').css('display', 'none');
                    $('#div-start-again-campaign').css('display', 'block');
                    $('#div-manage-leads').css('display', 'none');
                    $('#div-raise-issue').css('display', 'none');
                    trigger_pnofify('success', 'Successful', 'Campaign submitted successfully.');
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    } else {

    }
}

function startAgainCampaign(_id) {
    if(_id && confirm('Are you sure to restart campaign?')) {
        $.ajax({
            type: 'post',
            url: URL + '/vendor-manager/ra/campaign/restart-campaign/' + _id,
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    $('#div-start-campaign').css('display', 'none');
                    $('#div-submit-campaign').css('display', 'block');
                    $('#div-start-again-campaign').css('display', 'none');
                    $('#div-manage-leads').css('display', 'block');
                    trigger_pnofify('success', 'Successful', 'Campaign started successfully.');
                    window.location.reload();
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    } else {

    }
}
