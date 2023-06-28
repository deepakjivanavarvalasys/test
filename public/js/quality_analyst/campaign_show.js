

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

    $("#form-upload-npf").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'npf_file[]': {
                required: true,
                extension: "xlsx",
            },
            'user_id': {
                required: true,
            }
        },
        messages: {
            'npf_file[]' : {
                required : "Please upload file(s)",
                extension: "Please upload valid file(s), (xlsx)",
            },
            'user_id' : {
                required : "Please select user",
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

    $('#form-upload-npf-submit').on('click', function (e) {
        e.preventDefault();
        if($("#form-upload-npf").valid()) {
            let form_data = new FormData($('#form-upload-npf')[0]);
            $.ajax({
                url: URL +'/quality-analyst/campaign/upload-npf-file/' + $('meta[name="ca-qa-id"]').attr('content'),
                processData: false,
                contentType: false,
                data: form_data,
                type: 'post',
                success: function(response) {
                    if(response.status === true) {
                        $('#modal-upload-npf').modal('hide');
                        trigger_pnofify('success', 'Successful', response.message);
                    } else {
                        $('#modal-upload-npf').modal('hide');
                        trigger_pnofify('error', 'Error while processing request', response.message);
                    }
                }
            });
        }
    });

    $("#form-submit-campaign").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'final_file': {
                required: true,
                extension: "xlsx",
            }
        },
        messages: {
            'final_file' : {
                required : "Please upload file",
                extension: "Please upload valid file(s), (xlsx)",
            }
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
                url: URL +'/quality-analyst/campaign/submit-campaign/' + $('meta[name="ca-qa-id"]').attr('content'),
                processData: false,
                contentType: false,
                data: form_data,
                type: 'post',
                success: function(response) {
                    if(response.status === true) {
                        $('#modal-submit-campaign').modal('hide');
                        trigger_pnofify('success', 'Successful', response.message);

                        window.location.reload();
                    } else {
                        $('#modal-submit-campaign').modal('hide');
                        trigger_pnofify('error', 'Error while processing request', response.message);
                    }
                }
            });
        }
    });

});

function downloadFile(_id) {
    if(_id && confirm('Are you sure to download campaign file?')) {
        $.ajax({
            type: 'post',
            url: URL + '/quality-analyst/campaign/download-file/' + _id,
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

function downloadNPF(_id) {
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
