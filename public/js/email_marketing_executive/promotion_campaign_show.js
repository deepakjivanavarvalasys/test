

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

    $("#form-upload-ebb").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'ebb_file' : {
                required : true,
                extension: "xlsx"
            },
        },
        messages: {
            'ebb_file' : {
                required : "Please upload file",
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

    $('#form-upload-ebb-submit').on('click', function (e) {
        e.preventDefault();

        if($("#form-upload-ebb").valid()) {
            let form_data = new FormData($('#form-upload-ebb')[0]);
            $.ajax({
                url: URL +'/email-marketing-executive/promotion-campaign/upload-ebb-file/' + $('meta[name="ca-eme-id"]').attr('content'),
                processData: false,
                contentType: false,
                data: form_data,
                type: 'post',
                success: function(response) {
                    if(response.status === true) {
                        $('#modal-upload-ebb').modal('hide');
                        trigger_pnofify('success', 'Successful', response.message);
                    } else {
                        $('#modal-upload-ebb').modal('hide');
                        trigger_pnofify('error', 'Error while processing request', response.message);
                    }
                }
            });
        }

    });

});

function submitCampaign(_id) {
    if(_id && confirm('Are you sure to submit campaign?')) {
        $.ajax({
            type: 'post',
            url: URL + '/email-marketing-executive/promotion-campaign/submit-campaign/' + _id,
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    $('#div-upload-ebb').css('display', 'none');
                    $('#div-submit-campaign').css('display', 'none');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    } else {

    }
}
