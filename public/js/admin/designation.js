
let DESIGNATION_TABLE;

$(function (){

    DESIGNATION_TABLE = $('#table-designations').DataTable({
        "lengthMenu": [ [5,10,20,30,-1], [5,10,20,30,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": $('meta[name="base-path"]').attr('content') + '/admin/user-settings/designation/get-designations',
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
                data: 'name',
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
                render: function (data, type, row) {
                    return moment(row.created_at).format('YYYY-MM-DD HH:mm:ss');
                }
            },
            {
                render: function (data, type, row) {
                    return moment(row.updated_at).format('YYYY-MM-DD HH:mm:ss');
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let html = '';
                    html += '<button class="btn btn-outline-dark btn-sm" title="Edit Designation" onclick="editDesignation('+row.id+')"><i class="feather icon-edit mr-0"></i></button>';
                    html += '<button class="btn btn-outline-danger btn-sm" title="Delete Designation" onclick="deleteDesignation('+row.id+')"><i class="feather icon-trash-2 mr-0"></i></button>';
                    return html;
                }
            },
        ]
    });

    $('#modal-form-button-submit').on('click', function (e) {
        e.preventDefault();
        let url = '';
        if($(this).text() === 'Save') {
            url = $('meta[name="base-path"]').attr('content') + '/admin/user-settings/designation/store';
        } else if ($(this).text() === 'Update') {
            url = $('meta[name="base-path"]').attr('content') + '/admin/user-settings/designation/update/'+$('#designation_id').val();
        } else {
            resetModalForm();
            trigger_pnofify('error', 'Something went wrong', 'Please try again');
        }

        $.ajax({
            type: 'post',
            url: url,
            data: $('#modal-designation-form').serialize(),
            success: function (response) {
                if(response.status === true) {
                    resetModalForm();
                    $('#modalDesignation').modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                DESIGNATION_TABLE.ajax.reload();
            }
        });

    });
});

function addDesignation()
{
    resetModalForm();
    $('#modalDesignation').modal('show');
}

function editDesignation(id)
{
    $.ajax({
        type: 'post',
        url: $('meta[name="base-path"]').attr('content') + '/admin/user-settings/designation/edit/'+btoa(id),
        dataType: 'json',
        success: function (response) {
            if(response.status === true) {
                $('#modal-heading').text('Edit designation');
                $('#modal-form-button-submit').text('Update');
                $('#designation_id').val(btoa(response.data.id));
                $('#name').val(response.data.name);
                $('#status').val(response.data.status);
                $('#modalDesignation').modal('show');
            } else {
                resetModalForm();
                trigger_pnofify('error', 'Something went wrong', response.message);
            }

        }
    });
}

function deleteDesignation(id)
{
    if(confirm('Are you sure to delete this designation?')) {
        $.ajax({
            type: 'post',
            url: $('meta[name="base-path"]').attr('content') + '/admin/user-settings/designation/destroy/'+btoa(id),
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                DESIGNATION_TABLE.ajax.reload();
            }
        });
    } else {
        return true;
    }

}

function resetModalForm()
{
    $('#modal-heading').text('Add new designation');
    $('#modal-form-button-submit').text('Save');
    $('#designation_id').val('');
    $('#name').val('');
    $('#status').val('1');
}
