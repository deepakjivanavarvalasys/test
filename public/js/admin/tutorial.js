
let TUTORIAL_TABLE;
let URL = $('meta[name="base-path"]').attr('content');

$(function (){

    TUTORIAL_TABLE = $('#table-tutorials').DataTable({
        "lengthMenu": [ [10,20,50,-1], [10,20,50,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": URL + '/admin/tutorial/get-tutorials',
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
                orderable: false,
                render: function (data, type, row) {
                    let html = '';
                    html += '<button class="btn btn-outline-dark btn-sm" title="Edit Tutorial" onclick="editTutorial('+row.id+')"><i class="feather icon-edit mr-0"></i></button>';
                    html += '<button class="btn btn-outline-danger btn-sm" title="Delete Tutorial" onclick="deleteTutorial('+row.id+')"><i class="feather icon-trash-2 mr-0"></i></button>';
                    return html;
                }
            },
            {
                data: 'title',
            },
            {
                data: 'role.name',
            },
            {
                data: 'description',
            },
            {
                render: function (data, type, row) {
                    return '<a href="' + row.link + '" class="btn btn-link btn-sm"> View Video </a>';
                }
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
        ],
        order:[]
    });

    $('#modal-form-button-submit').on('click', function (e) {
        e.preventDefault();
        let url = '';
        if($(this).text() === 'Save') {
            url = URL + '/admin/tutorial/store';
        } else if ($(this).text() === 'Update') {
            url = URL + '/admin/tutorial/update/'+$('#tutorial_id').val();
        } else {
            resetModalForm();
            trigger_pnofify('error', 'Something went wrong', 'Please try again');
        }

        $.ajax({
            type: 'post',
            url: url,
            data: $('#modal-tutorial-form').serialize(),
            success: function (response) {
                if(response.status === true) {
                    resetModalForm();
                    $('#modalTutorial').modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                TUTORIAL_TABLE.ajax.reload();
            }
        });

    });
});

function addTutorial()
{
    resetModalForm();
    $('#modalTutorial').modal('show');
}

function editTutorial(id)
{
    $.ajax({
        type: 'post',
        url: URL + '/admin/tutorial/edit/'+btoa(id),
        dataType: 'json',
        success: function (response) {
            if(response.status === true) {
                $('#modal-heading').text('Edit tutorial');
                $('#modal-form-button-submit').text('Update');
                $('#tutorial_id').val(btoa(response.data.id));
                $('#role_id').val(response.data.role_id);
                $('#title').val(response.data.name);
                $('#description').val(response.data.description);
                $('#link').val(response.data.link);
                $('#status').val(response.data.status);
                $('#modalTutorial').modal('show');
            } else {
                resetModalForm();
                trigger_pnofify('error', 'Something went wrong', response.message);
            }

        }
    });
}

function deleteTutorial(id)
{
    if(confirm('Are you sure to delete this tutorial?')) {
        $.ajax({
            type: 'post',
            url: URL + '/admin/tutorial/destroy/'+btoa(id),
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                TUTORIAL_TABLE.ajax.reload();
            }
        });
    } else {
        return true;
    }

}

function resetModalForm()
{
    $('#modal-heading').text('Add new tutorial');
    $('#modal-form-button-submit').text('Save');
    $('#tutorial_id').val('');
    $('#role_id').val('');
    $('#title').val('');
    $('#description').val('');
    $('#link').val('');
    $('#status').val('1');
}
