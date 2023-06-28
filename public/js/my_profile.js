

$(function (){

    $("#form-upload-profile").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'profile' : {
                required : true,
                extension: "png|jpe?g",
                filesize: 256000
            },
        },
        messages: {
            'profile' : {
                required : "Please upload file",
                extension: "Please upload valid file, [png, jpg, jpeg]",
                filesize: "File size should be less than 256 kb"
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

    $.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    });

    $('#form-submit-upload-profile').on('click', function (e) {
        e.preventDefault();
        if($("#form-upload-profile").valid()) {
            let form_data = new FormData($('#form-upload-profile')[0]);
            $.ajax({
                type: 'post',
                url: URL + '/user/update-profile',
                processData: false,
                contentType: false,
                data: form_data,
                success: function (response) {
                    if(response.status === true) {
                        trigger_pnofify('success', 'Successful', response.message);
                        window.location.reload();
                    } else {
                        trigger_pnofify('error', 'Something went wrong', response.message);
                    }
                }
            });
        }
    });

});
