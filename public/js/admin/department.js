
let DEPARTMENT_TABLE;

$(function (){

    DEPARTMENT_TABLE = $('#table-departments').DataTable({
        "lengthMenu": [ [5,10,20,30,-1], [5,10,20,30,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": $('meta[name="base-path"]').attr('content') + '/admin/user-settings/department/get-departments',
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
                    html += '<button class="btn btn-outline-dark btn-sm" title="Edit Department" onclick="editDepartment('+row.id+')"><i class="feather icon-edit mr-0"></i></button>';
                    html += '<button class="btn btn-outline-danger btn-sm" title="Delete Department" onclick="deleteDepartment('+row.id+')"><i class="feather icon-trash-2 mr-0"></i></button>';
                    return html;
                }
            },
        ]
    });

    $('#modal-form-button-submit').on('click', function (e) {
        e.preventDefault();
        let url = '';
        if($(this).text() === 'Save') {
            url = $('meta[name="base-path"]').attr('content') + '/admin/user-settings/department/store';
        } else if ($(this).text() === 'Update') {
            url = $('meta[name="base-path"]').attr('content') + '/admin/user-settings/department/update/'+$('#department_id').val();
        } else {
            resetModalForm();
            trigger_pnofify('error', 'Something went wrong', 'Please try again');
        }

        $.ajax({
            type: 'post',
            url: url,
            data: $('#modal-department-form').serialize(),
            success: function (response) {
                if(response.status === true) {
                    resetModalForm();
                    $('#modalDepartment').modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                DEPARTMENT_TABLE.ajax.reload();
            }
        });

    });
});

function addDepartment()
{
    resetModalForm();
    $('#modalDepartment').modal('show');
}

function editDepartment(id)
{
    $.ajax({
        type: 'post',
        url: $('meta[name="base-path"]').attr('content') + '/admin/user-settings/department/edit/'+btoa(id),
        dataType: 'json',
        success: function (response) {
            if(response.status === true) {
                $('#modal-heading').text('Edit department');
                $('#modal-form-button-submit').text('Update');
                $('#department_id').val(btoa(response.data.id));
                $('#name').val(response.data.name);
                $('#status').val(response.data.status);
                $('#modalDepartment').modal('show');
            } else {
                resetModalForm();
                trigger_pnofify('error', 'Something went wrong', response.message);
            }

        }
    });
}

function deleteDepartment(id)
{
    if(confirm('Are you sure to delete this department?')) {
        $.ajax({
            type: 'post',
            url: $('meta[name="base-path"]').attr('content') + '/admin/user-settings/department/destroy/'+btoa(id),
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                DEPARTMENT_TABLE.ajax.reload();
            }
        });
    } else {
        return true;
    }

}

function resetModalForm()
{
    $('#modal-heading').text('Add new department');
    $('#modal-form-button-submit').text('Save');
    $('#department_id').val('');
    $('#name').val('');
    $('#status').val('1');
}
