/* ------------------------------------
    Campaign List Custom Javascript
------------------------------------ */

let CAMPAIGN_TABLE;
let URL = $('meta[name="base-path"]').attr('content');
let MONTHS = ['Jan','Feb','Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

$(function (){

    CAMPAIGN_TABLE = $('#table-campaigns').DataTable({
        "lengthMenu": [ [25,500,250,100,50,-1], [25,500,250,100,50,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": URL + '/agent/campaign/get-campaigns',
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
                data: 'campaign.campaign_id'
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    return '<div style="white-space:normal;font-size: 12px">' +
                        '<a href="'+URL+'/agent/campaign/view-details/'+btoa(row.id)+'" class="text-dark double-click" title="View campaign details">'+row.campaign.name+'</a>' +
                        '</div>';
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let deliver_count = row.agent_lead_count;
                    let allocation = row.allocation;
                    let percentage = (deliver_count/allocation)*100;

                    percentage = percentage.toFixed(2);
                    return '<div class="progress" style="height: 20px;width:85px;border:1px solid lightgrey;"><div class="progress-bar '+ (parseInt(percentage) < 100 ? 'bg-warning text-dark' : 'bg-success text-light' ) +'" role="progressbar" aria-valuenow="'+percentage+'" aria-valuemin="0" aria-valuemax="100" style="width: '+percentage+'%;font-weight:bold;">&nbsp;'+percentage+'%</div></div>';
                }
            },
            {
                orderable: false,
                className: 'text-center font-size-11',
                render: function (data, type, row) {
                    let date = new Date(row.campaign.start_date);
                    return (date.getDate() <= 9 ? '0'+date.getDate() : date.getDate())+'/'+MONTHS[date.getMonth()]+'/'+date.getFullYear();
                }
            },
            {
                orderable: false,
                className: 'text-center font-size-11',
                render: function (data, type, row) {
                    let date = new Date(row.display_date);
                    return (date.getDate() <= 9 ? '0'+date.getDate() : date.getDate())+'/'+MONTHS[date.getMonth()]+'/'+date.getFullYear();
                }
            },
            {
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    let deliver_count = row.agent_lead_count;
                    let allocation = row.allocation;
                    let shortfall_count = 0;

                    if(shortfall_count) {
                        return deliver_count + ' <span class="text-danger" title="Shortfall Count">('+ shortfall_count +')</span>'+' / '+ allocation;
                    } else {
                        return deliver_count + ' / '+ allocation;
                    }

                }
            },
            {
                orderable: false,
                className: 'text-center',
                data: 'agent_work_type.name'
            },
            {
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    let status_id  = row.campaign.campaign_status_id;
                    let campaign_type = '';
                    if(row.campaign.parent_id) {
                        campaign_type = '<br>(Incremental)'
                    }
                    switch (parseInt(status_id)) {
                        case 1: return '<span class="badge badge-success" style="padding: 5px;min-width:50px;"> Live'+campaign_type+' </span>';
                        case 2: return '<span class="badge badge-warning" style="padding: 5px;min-width:50px;"> Paused'+campaign_type+' </span>';
                        case 3: return '<span class="badge badge-danger" style="padding: 5px;min-width:50px;"> Cancelled'+campaign_type+' </span>';
                        case 4: return '<span class="badge badge-primary" style="padding: 5px;min-width:50px;"> Delivered'+campaign_type+' </span>';
                        case 5: return '<span class="badge badge-success" style="padding: 5px;min-width:50px;"> Reactivated'+campaign_type+' </span>';
                        case 6: return '<span class="badge badge-secondary" style="padding: 5px;min-width:50px;"> Shortfall'+campaign_type+' </span>';
                    }
                }
            },
            {
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    switch (parseInt(row.status)) {
                        case 0: return '<div class="text-warning font-weight-bold">Inactive</div>';
                        case 1: return '<div class="text-success font-weight-bold">Active</div>';
                        case 2: return '<div class="text-danger font-weight-bold">Revoked</div>';
                    }
                }
            },
            {
                orderable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    let html = '<div class="text-warning">';
                    html += '<a href="'+URL+'/agent/campaign/view-details/'+btoa(row.id)+'" class="btn btn-outline-info btn-rounded btn-sm" style="padding: 0px 5px;" title="View Campaign Details"><i class="feather icon-eye mr-0"></i></a>';
                    return html+'</div>';
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
            let status_id  = parseInt(data.campaign.campaign_status_id);
            if(data.campaign.children.length) {
                status_id = parseInt(data.campaign.children[0].campaign_status_id);
            }
            if(parseInt(data.status) === 2) {
                status_id = 'revoked';
            }
            switch (status_id) {
                case 1:
                    $(row).addClass('border-live');
                    break;
                case 2:
                    $(row).addClass('border-paused');
                    break;
                case 3:
                case 'revoked':
                    $(row).addClass('border-cancelled');
                    break;
                case 4:
                    $(row).addClass('border-delivered');
                    break;
                case 5:
                    $(row).addClass('border-reactivated');
                    break;
                case 6:
                    $(row).addClass('border-shortfall');
                    break;
            }
        },
        order:[]
    });

});
