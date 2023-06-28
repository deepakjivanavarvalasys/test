
let ROLE_TABLE;

$(function (){

    ROLE_TABLE = $('#table-roles').DataTable({
        "lengthMenu": [ [5,10,20,30,-1], [5,10,20,30,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": BASE_PATH + '/admin/role/get-roles',
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
                orderable: false,
                render: function (data, type, row) {
                    let html = '';
                    html += '<button class="btn btn-outline-dark btn-sm" title="Edit Role" onclick="editRole('+row.id+')"><i class="feather icon-edit mr-0"></i></button>';
                    html += '<button class="btn btn-outline-danger btn-sm" title="Delete Role" onclick="deleteRole('+row.id+')"><i class="feather icon-trash-2 mr-0"></i></button>';
                    return html;
                }
            },
        ]
    });

    $('#modal-form-button-submit').on('click', function (e) {
        e.preventDefault();
        let url = '';
        if($(this).text() === 'Save') {
            url = '../../admin/role/store';
        } else if ($(this).text() === 'Update') {
            url = '../../admin/role/update/'+$('#role_id').val();
        } else {
            resetModalForm();
            trigger_pnofify('error', 'Something went wrong', 'Please try again');
        }

        $.ajax({
            type: 'post',
            url: url,
            data: $('#modal-role-form').serialize(),
            success: function (response) {
                if(response.status === true) {
                    resetModalForm();
                    $('#modalRole').modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                ROLE_TABLE.ajax.reload();
            }
        });

    });
});

function addRole()
{
    resetModalForm();
    $('#modalRole').modal('show');
}

function editRole(id)
{
    $.ajax({
        type: 'post',
        url: '../../admin/role/edit/'+btoa(id),
        dataType: 'json',
        success: function (response) {
            if(response.status === true) {
                $('#modal-heading').text('Edit role');
                $('#modal-form-button-submit').text('Update');
                $('#role_id').val(btoa(response.data.id));
                $('#name').val(response.data.name);
                $('#status').val(response.data.status);
                $('#modalRole').modal('show');
            } else {
                resetModalForm();
                trigger_pnofify('error', 'Something went wrong', response.message);
            }

        }
    });
}

function deleteRole(id)
{
    if(confirm('Are you sure to delete this role?')) {
        $.ajax({
            type: 'post',
            url: '../../admin/role/destroy/'+btoa(id),
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                ROLE_TABLE.ajax.reload();
            }
        });
    } else {
        return true;
    }

}

function resetModalForm()
{
    $('#modal-heading').text('Add new role');
    $('#modal-form-button-submit').text('Save');
    $('#role_id').val('');
    $('#name').val('');
    $('#status').val('1');
}
