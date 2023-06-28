
let HOLIDAY_TABLE;

$(function (){

    HOLIDAY_TABLE = $('#table-holidays').DataTable({
        "lengthMenu": [ [100,50,25,10,-1], [100,50,25,10,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": $('meta[name="base-path"]').attr('content') + '/admin/holiday/get-holidays',
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
                data: 'title',
            },
            {
                data: 'date',
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
                    html += '<button class="btn btn-outline-dark btn-sm" title="Edit Holiday" onclick="editHoliday('+row.id+')"><i class="feather icon-edit mr-0"></i></button>';
                    html += '<button class="btn btn-outline-danger btn-sm" title="Delete Holiday" onclick="deleteHoliday('+row.id+')"><i class="feather icon-trash-2 mr-0"></i></button>';
                    return html;
                }
            },
        ]
    });

    $('#modal-form-button-submit').on('click', function (e) {
        e.preventDefault();
        let url = '';
        if($(this).text() === 'Save') {
            url = $('meta[name="base-path"]').attr('content') + '/admin/holiday/store';
        } else if ($(this).text() === 'Update') {
            url = $('meta[name="base-path"]').attr('content') + '/admin/holiday/update/'+$('#holiday_id').val();
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
                    $('#modalHoliday').modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                HOLIDAY_TABLE.ajax.reload();
            }
        });

    });
});

function addHoliday()
{
    resetModalForm();
    $('#modalHoliday').modal('show');
}

function editHoliday(id)
{
    $.ajax({
        type: 'post',
        url: $('meta[name="base-path"]').attr('content') + '/admin/holiday/edit/'+btoa(id),
        dataType: 'json',
        success: function (response) {
            if(response.status === true) {
                $('#modal-heading').text('Edit holiday');
                $('#modal-form-button-submit').text('Update');
                $('#holiday_id').val(btoa(response.data.id));
                $('#title').val(response.data.title);
                $('#date').val(response.data.date);
                $('#status').val(response.data.status);
                $('#modalHoliday').modal('show');
            } else {
                resetModalForm();
                trigger_pnofify('error', 'Something went wrong', response.message);
            }

        }
    });
}

function deleteHoliday(id)
{
    if(confirm('Are you sure to delete this holiday?')) {
        $.ajax({
            type: 'post',
            url: $('meta[name="base-path"]').attr('content') + '/admin/holiday/destroy/'+btoa(id),
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                HOLIDAY_TABLE.ajax.reload();
            }
        });
    } else {
        return true;
    }

}

function resetModalForm()
{
    $('#modal-heading').text('Add new holiday');
    $('#modal-form-button-submit').text('Save');
    $('#holiday_id').val('');
    $('#tite').val('');
    $('#date').val('');
    $('#status').val('1');
}
