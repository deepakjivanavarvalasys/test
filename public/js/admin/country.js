
let COUNTRY_TABLE;

$(function (){

    COUNTRY_TABLE = $('#table-countries').DataTable({
        "lengthMenu": [ [100,50,25,10,-1], [100,50,25,10,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": $('meta[name="base-path"]').attr('content') + '/admin/geo/country/get-countries',
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
                data: 'region.name',
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
                    html += '<button class="btn btn-outline-dark btn-sm" title="Edit Country" onclick="editCountry('+row.id+')"><i class="feather icon-edit mr-0"></i></button>';
                    html += '<button class="btn btn-outline-danger btn-sm" title="Delete Country" onclick="deleteCountry('+row.id+')"><i class="feather icon-trash-2 mr-0"></i></button>';
                    return html;
                }
            },
        ]
    });

    $('#modal-form-button-submit').on('click', function(e) {
        e.preventDefault();
        let url = '';
        if ($(this).text() === 'Save') {
            url = $('meta[name="base-path"]').attr('content') +'/admin/geo/country/store';
        } else if ($(this).text() === 'Update') {
            url = $('meta[name="base-path"]').attr('content') +'/admin/geo/country/update/' + $('#country_id').val();
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
                    $('#modalCountry').modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                COUNTRY_TABLE.ajax.reload();
            }
        });

    });
});

function addCountry() {
    resetModalForm();
    $('#modalCountry').modal('show');
}



function editCountry(id) {
    $.ajax({
        type: 'post',
        url: $('meta[name="base-path"]').attr('content') +'/admin/geo/country/edit/' + btoa(id),
        dataType: 'json',
        success: function(response) {
            if (response.status === true) {
                $('#modal-heading').text('Edit Country');
                $('#modal-form-button-submit').text('Update');
                $('#country_id').val(btoa(response.data.id));
                $('#name').val(response.data.name);
                $('#region_id').val(response.data.region_id);
                $('#status').val(response.data.status);
                $('#modalCountry').modal('show');
            } else {
                resetModalForm();
                trigger_pnofify('error', 'Something went wrong', response.message);
            }

        }
    });
}

function deleteCountry(id) {
    if (confirm('Are you sure to delete this country?')) {
        $.ajax({
            type: 'post',
            url: $('meta[name="base-path"]').attr('content') +'/admin/geo/country/destroy/' + btoa(id),
            dataType: 'json',
            success: function(response) {
                if (response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
                COUNTRY_TABLE.ajax.reload();
            }
        });
    } else {
        return true;
    }

}

function resetModalForm() {
    $('#modal-heading').text('Add new Country');
    $('#modal-form-button-submit').text('Save');
    $('#country_id').val('');
    $('#name').val('');
    $('#region_id').val('');
    $('#status').val('1');
}
