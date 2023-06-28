/* ------------------------------------
    Campaign Create Custom Javascript
------------------------------------ */
//Declarations
let URL = $('meta[name="base-path"]').attr('content');

// Initializations
$(function (){
    $("#country_id").select2({
        placeholder: " -- Select Country(s) --",
    });

    $("#region_id").select2({
        placeholder: " -- Region(s) --",
    });

    $('body').on("input", ".only-non-zero-number", function (){
        if(this.value < 1) {
            $(this).val('');
        } else {
            $(this).val(parseInt(this.value));
        }
    });

});


$(function (){

    // classic editor
    ClassicEditor.create(document.querySelector('.classic-editor')).catch(error => {
        console.error(error);
    });

    //Auto select regions
    $("#country_id").change(function () {
        let regions = [];
        $.each($(this).children('option:selected'), function () {
            regions.push($(this).data('region-id'));
        });
        $("#region_id").val(regions);
        $("#region_id").select2('destroy').select2({
            placeholder: " -- Region(s) --",
        });
    });

    //Validate Form
    $("#form-campaign-edit").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'name' : {
                required : true,
                remote : {
                    url : URL + '/email-marketing-executive/campaign-management/validate-campaign-name',
                    data : {
                        campaign_id : $('meta[name="campaign-id"]').attr('content')
                    }
                }
            },
            'v_mail_campaign_id' : {
                required: false,
                remote : {
                    url : URL + '/email-marketing-executive/campaign-management/validate-v-mail-campaign-id',
                    data: {
                        campaign_id : $('meta[name="campaign-id"]').attr('content')
                    }
                }
            },
            'campaign_filter_id' : { required : true },
            'campaign_type_id' : { required : true },
            'country_id[]' : { required : true },
        },
        messages: {
            'name' : {
                required : "Please enter campaign name",
                remote : "Campaign name Invalid or already exists"
            },
            'v_mail_campaign_id' : {
                remote : "V-Mail Campaign Id Invalid or already exists"
            },
            'campaign_filter_id' : { required : "Please select campaign filter" },
            'campaign_type_id' : { required : "Please select campaign tye" },
            'country_id[]' : { required : "Please select country(s)" },

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

    $("#form-campaign-edit-submit").on('click',function (e) {
        e.preventDefault();
        if($("#form-campaign-edit").valid()) {
            $('#form-campaign-edit').submit();
        }

    });

});
