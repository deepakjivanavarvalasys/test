let URL = $('meta[name="base-path"]').attr('content');
let AGENT_WORK_TYPE_TABLE;

$(function (){

    AGENT_WORK_TYPE_TABLE = $('#table-agent-work-types').DataTable({
        "lengthMenu": [ [10,20,50,-1], [10,20,50,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": URL + '/admin/campaign-settings/agent-work-type/get-agent-work-types',
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
                data: 'slug',
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
                    html += '<button class="btn btn-outline-dark btn-sm" title="Edit Agent Work Type" onclick="editAgentWorkType('+row.id+')"><i class="feather icon-edit mr-0"></i></button>';
                    html += '<button class="btn btn-outline-danger btn-sm" title="Delete Agent Work Type" onclick="deleteAgentWorkType('+row.id+')"><i class="feather icon-trash-2 mr-0"></i></button>';
                    return html;
                }
            },
        ]
    });

    $('#form-agent-work-type-submit').on('click', function (e) {
        e.preventDefault();
        let url = '';
        if($(this).text() === 'Save') {
            url = URL + '/admin/campaign-settings/agent-work-type/store';
        } else if ($(this).text() === 'Update') {
            url = URL + '/admin/campaign-settings/agent-work-type/update/'+$('#agent_work_type_id').val();
        } else {
            resetModalForm();
            trigger_pnofify('error', 'Something went wrong', 'Please try again');
        }

        $.ajax({
            type: 'post',
            url: url,
            data: $('#form-agent-work-type').serialize(),
            success: function (response) {
                if(response.status === true) {
                    resetModalForm();
                    $('#modal-agent-work-type').modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                AGENT_WORK_TYPE_TABLE.ajax.reload();
            }
        });

    });
});

function addAgentWorkType()
{
    resetModalForm();
    $('#modal-agent-work-type').modal('show');
}

function editAgentWorkType(id)
{
    $.ajax({
        type: 'post',
        url: URL + '/admin/campaign-settings/agent-work-type/edit/'+btoa(id),
        dataType: 'json',
        success: function (response) {
            if(response.status === true) {
                $('#modal-heading').text('Edit work type');
                $('#form-agent-work-type-submit').text('Update');
                $('#agent_work_type_id').val(btoa(response.data.id));
                $('#name').val(response.data.name);
                $('#status').val(response.data.status);
                $('#modal-agent-work-type').modal('show');
            } else {
                resetModalForm();
                trigger_pnofify('error', 'Something went wrong', response.message);
            }

        }
    });
}

function deleteAgentWorkType(id)
{
    if(confirm('Are you sure to delete this agent work type?')) {
        $.ajax({
            type: 'post',
            url: URL + '/admin/campaign-settings/agent-work-type/destroy/'+btoa(id),
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                AGENT_WORK_TYPE_TABLE.ajax.reload();
            }
        });
    } else {
        return true;
    }

}

function resetModalForm()
{
    $('#modal-heading').text('Add new work type');
    $('#form-agent-work-type-submit').text('Save');
    $('#agent_work_type_id').val('');
    $('#name').val('');
    $('#status').val('1');
}
