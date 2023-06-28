/* ------------------------------------
    Campaign Create Custom Javascript
------------------------------------ */
//Declarations
let URL = $('meta[name="base-path"]').attr('content');

// Initializations
$(function (){

    $('body').on("input", ".only-non-zero-number", function (){
        if(this.value < 1) {
            $(this).val('');
        } else {
            $(this).val(parseInt(this.value));
        }
    });

});


$(function (){

    //Validator Function nonEmptyValue
    jQuery.validator.addMethod("non_empty_value", function(value, element) {

        if(value.length>0) {
            if(value.trim().length>0) {
                //$(element).val(value.trim())
                return true;
            } else {
                $(element).val('');
                return false;
            }
        } else {
            return true;
        }
    }, "Please enter data");

    //Validator Function check valid email address
    jQuery.validator.addMethod("is_email_address", function(value, element) {

        /* Define the recommended regular expression. */
        var emailExp = new RegExp(/^\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,15}\b$/i);

        /* Test the email given against the expression and return the result. */
        return emailExp.test(value);

    }, "Please enter valid email address");

    //Validator f() Target Domain
    $.validator.addMethod(
        "remote_suppression_domain",
        function(value, element) {
            var data;
            $.ajax({
                url: URL + '/email-marketing-executive/lead/check-suppression-domain/' + $('#ca_agent_id').val(),
                data: {
                    company_domain: value
                },
                dataType: 'json',
                async: false,
                success: function(response) {
                    data = response;
                }
            });
            if(data === true || data === 'true') {
                return true;
            } else {
                return false;
            }
        }
    );
    $.validator.addMethod(
        "remote_target_domain",
        function(value, element) {
            let data;
            $.ajax({
                url: URL + '/email-marketing-executive/lead/check-target-domain/' + $('#ca_agent_id').val(),
                data: {
                    company_domain: value
                },
                dataType: 'json',
                async: false,
                success: function(response) {
                    data = response;
                }
            });
            if(data === true || data === 'true') {
                return true;
            } else {
                return false;
            }
        }
    );

    //Validate Form
    $("#form-lead-create").validate({
        onfocusout: false,
        onkeyup: false,
        onclick: false,
        ignore: [],
        focusInvalid: false,
        rules: {
            'first_name' : { required : true,non_empty_value: true },
            'last_name' : { required : true,non_empty_value: true },
            'company_name' : {
                required : true,
                non_empty_value: true,
                remote : {
                    url : URL + '/email-marketing-executive/lead/check-suppression-account-name/' + $('#ca_agent_id').val(),
                    data : {
                        company_name : function(){
                            return $("#company_name").val();
                        },
                    }
                }
            },
            'email_address' : {
                email: true,
                required : true,
                non_empty_value: true,
                remote : {
                    url : URL + '/email-marketing-executive/lead/check-suppression-email/' + $('#ca_agent_id').val(),
                    data : {
                        email_address : function(){
                            return $("#email_address").val();
                        },
                    }
                }
            },
            'specific_title' : { required : true,non_empty_value: true },
            'phone_number' : { required : true,non_empty_value: true },
            'address_1' : { required : true,non_empty_value: true },
            'city' : { required : true,non_empty_value: true },
            'state' : { required : true,non_empty_value: true },
            'zipcode' : { required : true,non_empty_value: true },
            'country' : { required : true,non_empty_value: true },
            'industry' : { required : true,non_empty_value: true },
            'employee_size' : { required : true,non_empty_value: true },
            'revenue' : { required : true,non_empty_value: true },
            'company_domain' : {
                required : true,
                non_empty_value: true,
                remote_suppression_domain: true,
                remote_target_domain: true
            },
            'company_linkedin_url' : { required : true,non_empty_value: true },
            'linkedin_profile_link' : { required : true,non_empty_value: true },

        },
        messages: {
            'first_name' : { required : "Please enter first name" },
            'last_name' : { required : "Please enter last name" },
            'company_name' : {
                required : "Please enter company name",
                remote: "Client suppression! Target another prospect"
            },
            'email_address' : {
                required : "Please enter email address",
                remote: "Client suppression! Target another prospect"
            },
            'specific_title' : { required : "Please enter title" },
            'phone_number' : { required : "Please enter phone number" },
            'address_1' : { required : "Please enter address" },
            'city' : { required : "Please enter city" },
            'state' : { required : "Please enter state" },
            'zipcode' : { required : "Please enter zipcode" },
            'country' : { required : "Please enter country" },
            'industry' : { required : "Please enter industry" },
            'employee_size' : { required : "Please enter size" },
            'revenue' : { required : "Please enter revenue" },
            'company_domain' : {
                required : "Please enter company domain",
                remote_suppression_domain: "Client suppression! Target another prospect",
                remote_target_domain: "Target Mismatch"
            },
            'company_linkedin_url' : { required : "Please enter url" },
            'linkedin_profile_link' : { required : "Please enter link" },

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

});
