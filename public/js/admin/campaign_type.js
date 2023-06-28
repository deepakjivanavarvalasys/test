
let CAMPAIGN_TYPE_TABLE;

$(function (){

    CAMPAIGN_TYPE_TABLE = $('#table-campaign-types').DataTable({
        "lengthMenu": [ [10,20,50,-1], [10,20,50,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": $('meta[name="base-path"]').attr('content') + '/admin/campaign-settings/campaign-type/get-campaign-types',
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
                data: 'full_name',
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
                    return moment(row.created_at).format('DD MMM, YYYY [at] HH:mm A');
                }
            },
            {
                render: function (data, type, row) {
                    return moment(row.updated_at).format('DD MMM, YYYY [at] HH:mm A');
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let html = '';
                    html += '<button class="btn btn-outline-dark btn-sm" title="Edit Campaign Type" onclick="editCampaignType('+row.id+')"><i class="feather icon-edit mr-0"></i></button>';
                    html += '<button class="btn btn-outline-danger btn-sm" title="Delete Campaign Type" onclick="deleteCampaignType('+row.id+')"><i class="feather icon-trash-2 mr-0"></i></button>';
                    return html;
                }
            },
        ]
    });

    $('#modal-form-button-submit').on('click', function (e) {
        e.preventDefault();
        let url = '';
        if($(this).text() === 'Save') {
            url = $('meta[name="base-path"]').attr('content') + '/admin/campaign-settings/campaign-type/store';
        } else if ($(this).text() === 'Update') {
            url = $('meta[name="base-path"]').attr('content') + '/admin/campaign-settings/campaign-type/update/'+$('#campaign_type_id').val();
        } else {
            resetModalForm();
            trigger_pnofify('error', 'Something went wrong', 'Please try again');
        }

        $.ajax({
            type: 'post',
            url: url,
            data: $('#modal-campaign-type-form').serialize(),
            success: function (response) {
                if(response.status === true) {
                    resetModalForm();
                    $('#modalCampaignType').modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                CAMPAIGN_TYPE_TABLE.ajax.reload();
            }
        });

    });
});

function addCampaignType()
{
    resetModalForm();
    $('#modalCampaignType').modal('show');
}

function editCampaignType(id)
{
    $.ajax({
        type: 'post',
        url: $('meta[name="base-path"]').attr('content') + '/admin/campaign-settings/campaign-type/edit/'+btoa(id),
        dataType: 'json',
        success: function (response) {
            if(response.status === true) {
                $('#modal-heading').text('Edit campaign type');
                $('#modal-form-button-submit').text('Update');
                $('#campaign_type_id').val(btoa(response.data.id));
                $('#name').val(response.data.name);
                $('#full_name').val(response.data.full_name);
                $('#status').val(response.data.status);
                $('#modalCampaignType').modal('show');
            } else {
                resetModalForm();
                trigger_pnofify('error', 'Something went wrong', response.message);
            }

        }
    });
}

function deleteCampaignType(id)
{
    if(confirm('Are you sure to delete this campaign type?')) {
        $.ajax({
            type: 'post',
            url: $('meta[name="base-path"]').attr('content') + '/admin/campaign-settings/campaign-type/destroy/'+btoa(id),
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                CAMPAIGN_TYPE_TABLE.ajax.reload();
            }
        });
    } else {
        return true;
    }

}

function resetModalForm()
{
    $('#modal-heading').text('Add new campaign type');
    $('#modal-form-button-submit').text('Save');
    $('#campaign_type_id').val('');
    $('#name').val('');
    $('#full_name').val('');
    $('#status').val('1');
}
