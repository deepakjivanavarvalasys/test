
let REGION_TABLE;

$(function (){

    REGION_TABLE = $('#table-regions').DataTable({
        "lengthMenu": [ [100,50,25,10,-1], [100,50,25,10,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": $('meta[name="base-path"]').attr('content') + '/admin/geo/region/get-regions',
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
                data: 'abbreviation',
            },
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
                    html += '<button class="btn btn-outline-dark btn-sm" title="Edit Region" onclick="editRegion('+row.id+')"><i class="feather icon-edit mr-0"></i></button>';
                    html += '<button class="btn btn-outline-danger btn-sm" title="Delete Region" onclick="deleteRegion('+row.id+')"><i class="feather icon-trash-2 mr-0"></i></button>';
                    return html;
                }
            },
        ]
    });

    $('#modal-form-button-submit').on('click', function(e) {
        e.preventDefault();
        let url = '';
        if ($(this).text() === 'Save') {
            url = $('meta[name="base-path"]').attr('content') +'/admin/geo/region/store';
        } else if ($(this).text() === 'Update') {
            url = $('meta[name="base-path"]').attr('content') +'/admin/geo/region/update/' + $('#region_id').val();
        } else {
            resetModalForm();
            trigger_pnofify('error', 'Something went wrong', 'Please try again');
        }

        $.ajax({
            type: 'post',
            url: url,
            data: $('#modal-role-form').serialize(),
            success: function(response) {
                if (response.status === true) {
                    resetModalForm();
                    $('#modalRegion').modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                REGION_TABLE.ajax.reload();
            }
        });

    });
});

function addRegion() {
    resetModalForm();
    $('#modalRegion').modal('show');
}



function editRegion(id) {
    $.ajax({
        type: 'post',
        url: $('meta[name="base-path"]').attr('content') +'/admin/geo/region/edit/' + btoa(id),
        dataType: 'json',
        success: function(response) {
            if (response.status === true) {
                $('#modal-heading').text('Edit Region');
                $('#modal-form-button-submit').text('Update');
                $('#region_id').val(btoa(response.data.id));
                $('#name').val(response.data.name);
                $('#abbreviation').val(response.data.abbreviation);
                $('#status').val(response.data.status);
                $('#modalRegion').modal('show');
            } else {
                resetModalForm();
                trigger_pnofify('error', 'Something went wrong', response.message);
            }

        }
    });
}

function deleteRegion(id) {
    if (confirm('Are you sure to delete this region?')) {
        $.ajax({
            type: 'post',
            url: $('meta[name="base-path"]').attr('content') +'/admin/geo/region/destroy/' + btoa(id),
            dataType: 'json',
            success: function(response) {
                if (response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                REGION_TABLE.ajax.reload();
            }
        });
    } else {
        return true;
    }

}

function resetModalForm() {
    $('#modal-heading').text('Add new region');
    $('#modal-form-button-submit').text('Save');
    $('#region_id').val('');
    $('#name').val('');
    $('#abbreviation').val('');
    $('#status').val('1');
}
