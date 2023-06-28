/* ------------------------------------
    Campaign List Custom Javascript
------------------------------------ */

let LEAD_TABLE;
let URL = $('meta[name="base-path"]').attr('content');
let MONTHS = ['Jan','Feb','Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

$(function (){

    LEAD_TABLE = $('#table-leads').DataTable({
        "lengthMenu": [ [50,500,400,300,200,100,-1], [50,500,400,300,200,100,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": URL + '/agent/lead/get-leads',
            data: {
                filters: function (){
                    let obj = {
                    };
                    localStorage.setItem("filters", JSON.stringify(obj));
                    return JSON.stringify(obj);
                },
                ca_agent_id: $('meta[name="ca-agent-id"]').attr('content')
            },
            error: function(jqXHR, textStatus, errorThrown) { checkSession(jqXHR); }
        },
        "columns": [
            {
                orderable: false,
                render: function (data, type, row) {
                    let html = '';
                    html += '<a href="'+URL+'/agent/lead/edit/'+btoa(row.id)+'" class="btn btn-outline-secondary btn-rounded btn-sm" title="Edit lead Details" style="padding: 2px 5px;"><i class="feather icon-edit mr-0" ></i></a>';
                    return html;
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
                render: function (data, type, row) {
                    return "<a href='tel:"+row.phone_number+"'>"+row.phone_number+"</a>";
                }
                // data: 'phone_number'
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
                render: function (data, type, row) {
                    if(row.employee_size_2) {
                        return row.employee_size_2;
                    } else {
                        return 'NA';
                    }
                }
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
                    if(row.comment) {
                        return row.comment;
                    } else {
                        return 'NA';
                    }
                }
            },
            {
                render: function (data, type, row) {
                    if(row.comment_2) {
                        return row.comment_2;
                    } else {
                        return 'NA';
                    }
                }
            },
            {
                render: function (data, type, row) {
                    if(row.qc_comment) {
                        return row.qc_comment;
                    } else {
                        return 'NA';
                    }
                }
            },
            {
                render: function (data, type, row) {
                    switch (parseInt(row.status)) {
                        case 1: return '<span class="badge badge-pill badge-success" style="padding: 5px;min-width:50px;"> OK </span>';
                        case 0: return '<span class="badge badge-pill badge-danger" style="padding: 5px;min-width:50px;"> Rejected </span>';
                    }
                }
            },
            {
                render: function (data, type, row) {
                    return moment(row.created_at).format('YYYY-MM-DD HH:mm:ss');
                }
            }
        ],
        "fnDrawCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $('.dark-left-toolbar').each(function() {
                var id = $(this).data('id');
                $(this).toolbar({
                    content: '#toolbar-options-' + id,
                    position: 'left',
                    style: 'dark'
                });
            });

            $('.double-click').click(function() {
                return false;
            }).dblclick(function() {
                window.location = this.href;
                return false;
            });
        },
        "createdRow": function(row, data, dataIndex){
            switch (parseInt(data.status)) {
                case 0:
                    $(row).addClass('border-cancelled');
                    break;
            }
        },
        order:[]
    });

    $("#form-import-leads").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'leads_file' : {
                required : true,
                extension: "xlsx",
                filesize: 500000
            },
        },
        messages: {
            'leads_file' : {
                required : "Please upload file",
                extension: "Please upload valid file, (xlsx)",
                filesize: "File size should be less than 500 kb"
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

    $('#form-import-leads-submit').on('click', function (e) {
        e.preventDefault();
        if($("#form-import-leads").valid()) {
            let form_data = new FormData($('#form-import-leads')[0]);

            //$('#modal-loader').modal('show');

            $.ajax({
                url: URL +'/agent/lead/import-leads',
                processData: false,
                contentType: false,
                data: form_data,
                type: 'post',
                success: function(response) {
                    console.log(response);
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
        }
    });

});
