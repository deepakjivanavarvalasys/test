

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

    $("#form-submit-campaign").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'accounts_utilized' : { required : true },
        },
        messages: {
            'accounts_utilized' : { required : 'Please enter accounts utilized' },
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
            $.ajax({
                type: 'post',
                url: URL + '/agent/campaign/submit-campaign/' + $('#modal-submit-campaign').find('input[name="ca_agent_id"]').val(),
                data: $('#form-submit-campaign').serialize(),
                async : true,
                success: function (response) {
                    if(response.status === true) {
                        $('#div-manage-leads').css('display', 'block');
                        $('#div-view-data').css('display', 'none');
                        $('#div-submit-campaign').css('display', 'block');
                        $('#div-raise-issue').css('display', 'block');
                        $("#modal-submit-campaign").modal('hide');
                        trigger_pnofify('success', 'Successful', response.message);
                        window.location.reload();
                    } else {
                        trigger_pnofify('error', 'Something went wrong', response.message);
                    }
                }
            });

            document.getElementById("modal-form-attach-specification").reset();
        }

    });
});

function startCampaign(_id) {
    if(_id && confirm('Are you sure to start campaign?')) {
        $.ajax({
            type: 'post',
            url: URL + '/agent/campaign/start-campaign/' + _id,
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
            url: URL + '/agent/campaign/submit-campaign/' + _id,
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
            url: URL + '/agent/campaign/restart-campaign/' + _id,
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
