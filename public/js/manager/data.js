/* ------------------------------------
    Campaign List Custom Javascript
------------------------------------ */

let DATA_TABLE;
let URL = $('meta[name="base-path"]').attr('content');
let MONTHS = ['Jan','Feb','Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

$(function (){

    DATA_TABLE = $('#table-data').DataTable({
        "lengthMenu": [ [50,500,400,300,200,100,-1], [50,500,400,300,200,100,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": URL + '/manager/data/get-data',
            data: {
                filters: function (){
                    let obj = {
                    };
                    localStorage.setItem("filters", JSON.stringify(obj));
                    return JSON.stringify(obj);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) { checkSession(jqXHR); }
        },
        "columns": [
            {
                data: 'first_name'
            },
            {
                data: 'last_name'
            },
            {
                data: 'company_name'
            },
            {
                data: 'email_address'
            },
            {
                data: 'specific_title'
            },
            {
                data: 'job_level'
            },
            {
                data: 'job_role'
            },
            {
                data: 'phone_number'
            },
            {
                data: 'address_1'
            },
            {
                data: 'address_2'
            },
            {
                data: 'city'
            },
            {
                data: 'state'
            },
            {
                data: 'zipcode'
            },
            {
                data: 'country'
            },
            {
                data: 'employee_size'
            },
            {
                data: 'revenue'
            },
            {
                data: 'company_domain'
            },
            {
                data: 'website'
            },
            {
                data: 'company_linkedin_url'
            },
            {
                data: 'linkedin_profile_link'
            },
            {
                data: 'linkedin_profile_sn_link'
            },
            {
                render: function (data, type, row) {
                    return moment(row.created_at).format('DD-MMM-YYYY');
                }
            },

        ],
    });

    $("#modal-form-import-data").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'data_file' : {
                required : true,
                extension: "xlsx",
                //filesize: 500000
            },
        },
        messages: {
            'data_file' : {
                required : "Please upload file",
                extension: "Please upload valid file, [xlsx]",
                //filesize: "File size should be less than 500 kb"
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

    $('#modal-form-import-data-submit').on('click', function (e) {
        e.preventDefault();

        if($("#modal-form-import-data").valid()) {
            let form_data = new FormData($('#modal-form-import-data')[0]);

            $('#modal-loader').modal('show');

            $.ajax({
                url: URL +'/manager/data/import-data',
                processData: false,
                contentType: false,
                data: form_data,
                type: 'post',
                success: function(response) {
                    console.log(response);
                    console.log(response.data);
                    let message = response.message;
                    if(response.status === true) {
                        let type = 'success';
                        if(typeof response.data !== 'undefined' && typeof response.data.failed_data !== 'undefined') {
                            message = response.message + '\n' + 'Total Number of Row Inserted '+response.data.success_count +'\n'+response.data.failed_data.length + ' row not inserted.';
                            type = 'warning';
                        }
                        $('#modal-import-data').modal('hide');
                        trigger_pnofify(type, 'Successful', message);
                    } else {
                        if(typeof response.data !== 'undefined' && typeof response.data.failed_data !== 'undefined') {
                            message = response.message + '\n' + response.data.failed_data.length + ' row not inserted.test2';
                        }
                        trigger_pnofify('error', 'Error while processing request', response.message);
                    }

                    $('#modal-loader').modal('hide');
                    DATA_TABLE.ajax.reload();

                }
            });

        }

    });

});
