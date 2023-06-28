

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

function submitCampaign(_id) {
    if(_id && confirm('Are you sure to submit campaign?')) {
        $.ajax({
            type: 'post',
            url: URL + '/vendor-manager/campaign-assign/submit-campaign/' + _id,
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    $('#div-submit-campaign').css('display', 'none');
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

