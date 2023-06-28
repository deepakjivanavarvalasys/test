/* ------------------------------------
    Campaign List Custom Javascript
------------------------------------ */

let CAMPAIGN_ISSUE_TABLE;
let URL = $('meta[name="base-path"]').attr('content');
let MONTHS = ['Jan','Feb','Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
 var filterissue = $("#filterissue").val();
if(filterissue=='all')
{
    datatablefun();
    // CAMPAIGN_ISSUE_TABLE.destroy();
} 
 $('#filterissue').on('change', function() {
    filterissue = $("#filterissue").val();

if(filterissue=='all')  
{
    datatablefun();

}

if(filterissue=='open')
{        console.log($("#filterissue").val());
         datatablefun();       
}

if(filterissue=='close')
{
         console.log($("#filterissue").val());
         datatablefun();
}

CAMPAIGN_ISSUE_TABLE.destroy();

});

  function datatablefun()
  {
$(function (){
    CAMPAIGN_ISSUE_TABLE = $('#table-campaign-issues').DataTable({
        "lengthMenu": [ [5,500,400,300,200,100,-1], [5,500,400,300,200,100,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": URL + '/manager/campaign-issue/get-issues',
             data: {
                 filterissue: filterissue,
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
                    if(row.status==0)
                     html += '<a href="javascript:void(0);" onclick="closeCampaignIssue(\''+btoa(row.id)+'\');" class="btn btn-outline-info btn-rounded btn-sm" title="Close Issue"><i class="feather icon-edit mr-0"></i></a>';
                    return html;
                }
            },
            {
                render: function (data, type, row) {
                    return '<a href="'+URL+'/manager/campaign/view-details/'+btoa(row.campaign.id)+'" class="text-dark double-click" title="' + row.campaign.name + '">'+row.campaign.campaign_id+'</a>';
                }
            },
            {
                render: function (data, type, row) {
                    switch (row.priority) {
                        case 'low': return '<span class="badge badge-pill badge-info" style="padding: 5px;min-width:50px;"> Low</span>';
                        case 'normal': return '<span class="badge badge-pill badge-warning" style="padding: 5px;min-width:50px;"> Normal</span>';
                        case 'high': return '<span class="badge badge-pill badge-danger" style="padding: 5px;min-width:50px;"> High</span>';
                    }
                }
            },
            {
                render: function (data, type, row) {
                    switch (parseInt(row.status)) {
                        case 1: return '<span class="badge badge-pill badge-success" style="padding: 5px;min-width:50px;"> Closed</span>';
                        case 0: return '<span class="badge badge-pill badge-warning" style="padding: 5px;min-width:50px;"> Open</span>';
                    }
                }
            },
            {
                render: function (data, type, row) {
                    return '<div style="white-space:normal;width:200px;">' + row.title + '</div>';
                }
            },
            {
                render: function (data, type, row) {
                    return '<div style="white-space:normal;width:200px;">' + row.description + '</div>';
                }
            },
            {
                render: function (data, type, row) {
                    if(row.response) {
                        return '<div style="white-space:normal;width:200px;">' + row.response + '</div>';
                    } else {
                        return '-';
                    }

                }
            },
            {
                data: 'user.full_name'
            },
            {
                render: function (data, type, row) {
                    let date = new Date(row.created_at);
                    return (date.getDate() <= 9 ? '0'+date.getDate() : date.getDate())+'/'+MONTHS[date.getMonth()]+'/'+date.getFullYear();
                }
            },
            {
                render: function (data, type, row) {
                    if (row.closed_by) {
                        return row.closed_by_user.full_name;
                    } else {
                        return ' - ';
                    }
                }
            },
            {
                render: function (data, type, row) {
                    if (row.closed_by) {
                        let date = new Date(row.updated_at);
                        return (date.getDate() <= 9 ? '0'+date.getDate() : date.getDate())+'/'+MONTHS[date.getMonth()]+'/'+date.getFullYear();
                    } else {
                        return ' - ';
                    }
                }
            },

        ],
        "fnDrawCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $('.dark-left-toolbar').each(function() {
                var id = $(this).data('id');
                $(this).toolbar({
                    content: '#toolbar-options-' + id,
                    position: 'left',
                    style: 'dark'
                });
            });

            $('.double-click').click(function() {
                return false;
            }).dblclick(function() {
                window.location = this.href;
                return false;
            });
        },
        "createdRow": function(row, data, dataIndex){

        },
        order:[]
    });

    //Validate Form
    $("#form-close-issue").validate({
        ignore: [],
        focusInvalid: false,
        rules: {
            'response' : { required : true },
        },
        messages: {
            'response' : { required : "Please enter response" },
        },
        errorPlacement: function errorPlacement(error, element) {
            var $parent = $(element).parents('.form-group');

            // Do not duplicate errors
            if ($parent.find('.jquery-validation-error').length) {
                return;
            }

            $parent.append(
                error.addClass('jquery-validation-error small form-text invalid-feedback')
            );
        },
        highlight: function(element) {
            var $el = $(element);
            var $parent = $el.parents('.form-group');

            $el.addClass('is-invalid');

            // Select2 and Tagsinput
            if ($el.hasClass('select2-hidden-accessible') || $el.attr('data-role') === 'tagsinput') {
                $el.parent().addClass('is-invalid');
            }
        },
        unhighlight: function(element) {
            $(element).parents('.form-group').find('.is-invalid').removeClass('is-invalid');
        }
    });

    $('#modal-form-button-submit').on('click', function (e) {
        e.preventDefault();
        if($("#modal-campaign-form").valid()) {
            let url = '';
            if($(this).text() === 'Save') {
                url = URL + '/manager/campaign/store';
            } else if ($(this).text() === 'Update') {
                url = URL + '/manager/campaign/update/'+$('#campaign_id').val();
            } else {
                resetModalForm();
                trigger_pnofify('error', 'Something went wrong', 'Please try again');
            }

            $.ajax({
                type: 'post',
                url: url,
                data: $('#modal-campaign-form').serialize(),
                success: function (response) {
                    if(response.status === true) {
                        resetModalForm();
                        $('#modalCampaign').modal('hide');
                        trigger_pnofify('success', 'Successful', response.message);
                    } else {
                        trigger_pnofify('error', 'Something went wrong', response.message);
                    }
                    CAMPAIGN_ISSUE_TABLE.ajax.reload();
                }
            });

        } else {
        }
    });

    $('#form-close-issue-submit').on('click', function (e) {
        e.preventDefault();
        if($("#form-close-issue").valid()) {
            $.ajax({
                type: 'post',
                url: URL + '/manager/campaign-issue/update/' + $('#modal-close-issue').find('input[name="id"]').val(),
                data: $('#form-close-issue').serialize(),
                async : true,
                success: function (response) {
                    if(response.status === true) {
                        trigger_pnofify('success', 'Successful', response.message);
                        $('#modal-close-issue').modal('hide');
                        CAMPAIGN_ISSUE_TABLE.ajax.reload();
                    } else {
                        trigger_pnofify('error', 'Something went wrong', response.message);
                    }
                }
            });
        } else {
        }
    });

});
}

function closeCampaignIssue(_issue_id) {
    if(confirm('Are you sure to close the issue?')) {
        $.ajax({
            type: 'post',
            url: URL + '/manager/campaign-issue/edit/'+_issue_id,
            dataType: 'json',
            success: function (response) {
                if(response.status === true) {
                    $('#modal-close-issue').find('input[name="id"]').val(_issue_id);
                    $('#modal-close-issue').find('textarea[name="response"]').val(response.data.response);
                    $('#modal-close-issue').modal('show');
                } else {
                    trigger_pnofify('error', 'Something went wrong', response.message);
                }
            }
        });
    } else {
        return true;
    }

}
