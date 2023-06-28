
let URL = $('meta[name="base-path"]').attr('content');
let DATA_TABLE;

$(function (){

    DATA_TABLE = $('#table-data').DataTable({
        "lengthMenu": [ [200,100,50,25,10,-1], [200,100,50,25,10,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": URL + '/vendor-manager/ra/data/get-agent-data',
            data: {
                filters: function (){
                    let obj = {
                    };
                    localStorage.setItem("filters", JSON.stringify(obj));
                    return JSON.stringify(obj);
                },
                ca_agent_id: $('meta[name="ca-agent-id"]').attr('content')
            },
            error: function(jqXHR, textStatus, errorThrown) { checkSession(jqXHR); }
        },
        "columns": [
            {
                orderable: false,
                render: function (data, type, row) {
                    return '';
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let html = '';
                    html += '<button type="button" class="btn btn-outline-secondary btn-rounded btn-sm" title="Edit/Nurture Data" onclick="editData(\''+ btoa(row.id) +'\')"><i class="feather icon-edit mr-0"></i></button>';
                    html += '<button type="button" class="btn btn-outline-success btn-rounded btn-sm" title="Take Lead" onclick="takeLead(\''+ btoa(row.id) +'\')"><i class="feather icon-share mr-0"></i></button>';
                    //html += '<a href="'+URL+'/vendor-manager/ra/data/take-lead-data/'+ $('meta[name="ca-agent-id"]').attr('content') + '/' + btoa(row.id) +'" class="btn btn-outline-success btn-rounded btn-sm" title="Take Lead"><i class="feather icon-share mr-0"></i></a>';
                    return html;
                }
            },
            {
                render: function (data, type, row) {
                    if(row.linkedin_profile_link.length) {
                        if(row.linkedin_profile_link.includes('http')) {
                            return '<a href="'+ row.linkedin_profile_link +'" target="_blank">Redirect <i class="fas fa-external-link-alt"></i></a>';
                        } else {
                            return '<a href="https://'+ row.linkedin_profile_link +'" target="_blank">Redirect <i class="fas fa-external-link-alt"></i></a>';
                        }
                    } else {
                        return 'Not Available';
                    }
                }
            },
            {
                data: 'first_name'
            },
            {
                data: 'last_name'
            },
            {
                data: 'company_name'
            },
            {
                data: 'email_address'
            },
            {
                data: 'specific_title'
            },
            {
                data: 'job_level'
            },
            {
                data: 'job_role'
            },
            {
                data: 'phone_number'
            },
            {
                data: 'address_1'
            },
            {
                data: 'address_2'
            },
            {
                data: 'city'
            },
            {
                data: 'state'
            },
            {
                data: 'zipcode'
            },
            {
                data: 'country'
            },
            {
                data: 'industry'
            },
            {
                data: 'employee_size'
            },
            {
                data: 'revenue'
            },
            {
                data: 'company_domain'
            },
            {
                render: function (data, type, row) {
                    if(row.website.length) {
                        if(row.website.includes('http')) {
                            return '<a href="'+ row.website +'" target="_blank">Redirect <i class="fas fa-external-link-alt"></i></a>';
                        } else {
                            return '<a href="https://'+ row.website +'" target="_blank">Redirect <i class="fas fa-external-link-alt"></i></a>';
                        }
                    } else {
                        return 'Not Available';
                    }
                }
            },
            {
                render: function (data, type, row) {
                    if(row.company_linkedin_url.length) {
                        if(row.company_linkedin_url.includes('http')) {
                            return '<a href="'+ row.company_linkedin_url +'" target="_blank">Redirect <i class="fas fa-external-link-alt"></i></a>';
                        } else {
                            return '<a href="https://'+ row.company_linkedin_url +'" target="_blank">Redirect <i class="fas fa-external-link-alt"></i></a>';
                        }
                    } else {
                        return 'Not Available';
                    }
                }
            },
            {
                render: function (data, type, row) {
                    if(row.linkedin_profile_sn_link.length) {
                        if(row.linkedin_profile_sn_link.includes('http')) {
                            return '<a href="'+ row.linkedin_profile_sn_link +'" target="_blank">Redirect <i class="fas fa-external-link-alt"></i></a>';
                        } else {
                            return '<a href="https://'+ row.linkedin_profile_sn_link +'" target="_blank">Redirect <i class="fas fa-external-link-alt"></i></a>';
                        }
                    } else {
                        return 'Not Available';
                    }
                }
            },

        ],
    });

    $('#form-edit-data-submit').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: URL + '/vendor-manager/ra/data/update/' + $('#data_id').val(),
            data: $('#form-edit-data').serialize(),
            async : true,
            success: function (response) {
                if(response.status === true) {
                    $("#modal-form-edit-data").modal('hide');
                    trigger_pnofify('success', 'Successful', response.message);
                    DATA_TABLE.ajax.reload();
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    });

});

function editData(_data_id) {
    $.ajax({
        type: 'get',
        url: URL + '/vendor-manager/ra/data/edit/' + _data_id,
        success: function (response){
            if(response.status === true) {
                for (var key in response.data) {

                    $("#data_id").val(_data_id);

                    if (response.data.hasOwnProperty(key)) {
                        $("#" + key).val(response.data[key]);
                    }
                }
                $("#modal-form-edit-data").modal('show');
            } else {
                trigger_pnofify('error', 'Error while processing request', response.message);
            }
        }
    });
}

function takeLead(_data_id) {
    if(confirm('Are you sure to take/utilize lead?')) {
        $.ajax({
            type: 'post',
            url: URL + '/vendor-manager/ra/data/take-lead-data',
            data: {
                data_id: _data_id,
                ca_agent_id: $('meta[name="ca-agent-id"]').attr('content')
            },
            success: function (response){
                if(response.status === true) {
                    trigger_pnofify('success', 'Successful', response.message);
                    DATA_TABLE.ajax.reload();
                } else {
                    trigger_pnofify('error', 'Error while processing request', response.message);
                }
            }
        });
    }
}
