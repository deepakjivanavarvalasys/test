
let VENDOR_TABLE;
let BASE_URL = $('meta[name="base-path"]').attr('content');

$(function (){

    VENDOR_TABLE = $('#table-roles').DataTable({
        "lengthMenu": [ [10,20,50,100,-1], [10,20,50,100,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": BASE_URL + '/vendor-manager/vendor/get-vendors',
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
                data: 'vendor_id',
            },
            {
                data: 'name',
            },
            {
                data: 'email',
            },
            {
                data: 'designation',
            },
            {
                render: function (data, type, row) {
                    switch (parseInt(row.status)) {
                        case 1: return '<span class="badge badge-pill badge-success" style="padding: 5px;min-width:50px;">Active</span>';
                        case 0: return '<span class="badge badge-pill badge-danger" style="padding: 5px;min-width:50px;">Inactive</span>';
                    }
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let html = '';
                    html += '<button class="btn btn-outline-dark btn-sm" title="Edit Vendor" onclick="editVendor('+row.id+')"><i class="feather icon-edit mr-0"></i></button>';
                    html += '<button class="btn btn-outline-danger btn-sm" title="Delete Vendor" onclick="deleteVendor('+row.id+')"><i class="feather icon-trash-2 mr-0"></i></button>';
                    return html;
                }
            },
        ]
    });

    $('#modal-form-button-submit').on('click', function (e) {
        e.preventDefault();
        if($("#modal-vendor-form").valid()) {
        let url = '';
        if($(this).text() === 'Save') {
            url = BASE_URL + '/vendor-manager/vendor/store';
        } else if ($(this).text() === 'Update') {
            url = BASE_URL + '/vendor-manager/vendor/update/'+$('#id').val();
        } else {
            resetModalForm();
            trigger_pnofify('error', 'Something went wrong', 'Please try again');
        }
        //console.log(url);console.log($('#modal-vendor-form').serialize()); return false;
        $.ajax({
            type: 'post',
            url: url,
            data: $('#modal-vendor-form').serialize(),
            success: function (response) {
                if(response.status === true) {
                    resetModalForm();
                    $('#modalVendor').modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                VENDOR_TABLE.ajax.reload();
            }
        });
    } else {
    }

    });

    jQuery.validator.addMethod("nonEmptyValue", function(value, element) {
        if(value.length>0) {
            if(value.trim().length>0) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }, "Please enter valid data");

    $("#modal-vendor-form").validate({
        focusInvalid: false,
        rules: {
            'vendor_id' : {
                required : true,
                nonEmptyValue: true
            },
            'name' : {
                nonEmptyValue: true
            },
            'email' : {
                required : true,
                email : true
            },
            'designation' : { required : true },
            'status' : { required : true }
        },
        messages: {
            'vendor_id' : { required: "Please enter vendor Id" },
            'name' : { required: "Please enter vendor name" },
            'employee_code' : {
                required: "Please enter employee code" },
            'email' : { required: "Please enter email" },
            'designation' : { required : "Please select reporting user" }
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
            if($(element).attr('aria-invalid') === 'false') {
                $(element).parents('.form-group').find('.is-invalid').removeClass('is-invalid');
            }
        }
    });
});


function addVendor()
{
    resetModalForm();
    $('#modalVendor').modal('show');
}

function editVendor(id)
{
    $.ajax({
        type: 'post',
        url: BASE_URL + '/vendor-manager/vendor/edit/'+btoa(id),
        dataType: 'json',
        success: function (response) {
            if(response.status === true) {
                $('#modal-heading').text('Edit vendor');
                $('#modal-form-button-submit').text('Update');
                $('#id').val(btoa(response.data.id));
                $('#vendor_id').val(response.data.vendor_id);
                $('#name').val(response.data.name);
                $('#email').val(response.data.email);
                $('#designation').val(response.data.designation);
                $('#status').val(response.data.status);
                $('#modalVendor').modal('show');
            } else {
                resetModalForm();
                trigger_pnofify('error', 'Something went wrong', response.message);
            }

        }
    });
}

function deleteVendor(id)
{
    if(confirm('Are you sure to delete this vendor?')) {
        $.ajax({
            type: 'post',
            url: BASE_URL + '/vendor-manager/vendor/destroy/'+btoa(id),
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                VENDOR_TABLE.ajax.reload();
            }
        });
    } else {
        return true;
    }

}

function resetModalForm()
{
    $('#modal-heading').text('Add new vendor');
    $('#modal-form-button-submit').text('Save');
    $('#vendor_id').val('');
    $('#name').val('');
    $('#email').val('');
    $('#designation').val('');
    $('#status').val('');
}
