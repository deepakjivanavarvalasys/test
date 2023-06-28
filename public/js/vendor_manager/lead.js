
let LEAD_TABLE;
let URL = $('meta[name="base-path"]').attr('content');

$(function (){

    LEAD_TABLE = $('#table-leads').DataTable({
        "lengthMenu": [ [500,400,300,200,100,-1], [500,400,300,200,100,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": URL + '/vendor-manager/lead/get-vendor-leads',
            data: {
                filters: function (){
                    let obj = {
                    };
                    localStorage.setItem("filters", JSON.stringify(obj));
                    return JSON.stringify(obj);
                },
                ca_vm_id: $('meta[name="ca-vm-id"]').attr('content')
            },
            error: function(jqXHR, textStatus, errorThrown) { checkSession(jqXHR); }
        },
        "columns": [
            /*{
                orderable: false,
                render: function (data, type, row) {
                    let html = '';
                    //html += '<a href="'+URL+'/vendor-manager/lead/edit/'+btoa(row.id)+'" class="btn btn-outline-secondary btn-rounded btn-sm" title="Edit lead details" style="padding: 2px 5px;"><i class="feather icon-edit mr-0" ></i></a>';
                    html += '<a href="javascript:void(0);" class="btn btn-outline-danger btn-rounded btn-sm" onclick="rejectLead(\''+btoa(row.id)+'\')" title="Reject lead" style="padding: 2px 5px;"><i class="feather icon-x mr-0" ></i></a>';
                    return html;
                }
            },*/
            {
                render: function (data, type, row) {
                    return row.vendor.name;
                }
            },
            {
                render: function (data, type, row) {
                    return moment(row.created_at).format('DD-MMM-YYYY');
                }
            },
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
                data: 'industry'
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
                data: 'comment'
            }
        ],
        "createdRow": function(row, data, dataIndex){
            switch (parseInt(data.status)) {
                case 0:
                    $(row).addClass('border-cancelled');
                    break;
            }
        }
    });

    $('#form-reject-lead-submit').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: URL + '/vendor-manager/lead/reject/' + $('#form-reject-lead').find('input[name="vendor_lead_id"]').val(),
            data: $('#form-reject-lead').serialize(),
            async : true,
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', 'Vendor\'s lead rejected successfully');
                    $('#modal-reject-lead').modal('hide');
                    LEAD_TABLE.ajax.reload();
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    });

    $("#form-upload-leads").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'lead_file' : {
                required : true,
                extension: "xlsx"
            },
        },
        messages: {
            'lead_file' : {
                required : "Please upload file",
                extension: "Please upload valid file [xlsx]."
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

    $('#form-upload-leads-submit').on('click', function (e) {
        e.preventDefault();
        if($("#form-upload-leads").valid()) {
            let form_data = new FormData($('#form-upload-leads')[0]);
            let url = '';
            $.ajax({
                url: URL +'/vendor-manager/lead/import-leads',
                processData: false,
                contentType: false,
                data: form_data,
                type: 'post',
                success: function(response) {

                    let _message = response.message;

                    if(response.status === true) {
                        let _type = 'success';
                        if(typeof response.data !== 'undefined' && typeof response.data.failed_data_file !== 'undefined') {

                            _message = response.message + '\n' + response.data.success_count + ' row inserted' + '\n' + response.data.failed_count + ' row not inserted';
                            _type = 'warning';
                            trigger_pnofify(_type, 'Request processed', _message);
                            $('#modal-import-leads').modal('hide');
                            LEAD_TABLE.ajax.reload();
                            return window.location.href = URL + response.data.failed_data_file;

                        } else {

                            _message = response.message + '\n' + response.data.success_count + ' row inserted';
                            _type = 'success';
                            trigger_pnofify(_type, 'Request processed successfully', _message);
                            $('#modal-import-leads').modal('hide');
                            LEAD_TABLE.ajax.reload();

                        }

                    } else {
                        if(typeof response.data !== 'undefined' && typeof response.data.failed_data_file !== 'undefined') {
                            trigger_pnofify('error', 'Error while processing request', response.message);
                            $('#modal-loader').modal('hide');
                            LEAD_TABLE.ajax.reload();
                            return window.location.href = URL + response.data.failed_data_file;
                        } else {
                            trigger_pnofify('error', 'Error while processing request', response.message);
                            $('#modal-loader').modal('hide');
                            LEAD_TABLE.ajax.reload();
                        }
                    }
                }
            });
            document.getElementById("form-upload-leads").reset();
        }
    });

});

function rejectLead(_lead_id) {
    if(confirm('Are you sure to reject this lead?')) {
        $('#form-reject-lead').find('input[name="vendor_lead_id"]').val(_lead_id)
        $('#modal-reject-lead').modal('show');
    }
}
