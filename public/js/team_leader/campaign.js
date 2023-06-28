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
            "url": URL + '/team-leader/campaign/get-campaigns',
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
                data: 'campaign.campaign_id'
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    return '<a href="'+URL+'/team-leader/campaign/view-details/'+btoa(row.id)+'" class="text-dark double-click" title="View campaign details">'+row.campaign.name+'</a>';
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let deliver_count = parseInt(row.agent_lead_total_count);
                    let allocation = parseInt(row.allocation);
                    let percentage = 0;

                    if(allocation > 0) {
                        percentage = (deliver_count/allocation)*100;
                    }

                    percentage = percentage.toFixed(2);
                    return '<div class="progress" style="height: 20px;width:100px;border:1px solid lightgrey;"><div class="progress-bar '+ (parseInt(percentage) < 100 ? 'bg-warning text-dark' : 'bg-success text-light' ) +'" role="progressbar" aria-valuenow="'+percentage+'" aria-valuemin="0" aria-valuemax="100" style="width: '+percentage+'%;font-weight:bold;">&nbsp;'+percentage+'%</div></div>';
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let date = new Date(row.campaign.start_date);
                    return (date.getDate() <= 9 ? '0'+date.getDate() : date.getDate())+'/'+MONTHS[date.getMonth()]+'/'+date.getFullYear();
                }
            },
            {
                render: function (data, type, row) {
                    let date = new Date(row.display_date);
                    return (date.getDate() <= 9 ? '0'+date.getDate() : date.getDate())+'/'+MONTHS[date.getMonth()]+'/'+date.getFullYear();
                }
            },
            {
                render: function (data, type, row) {
                    let deliver_count = row.agent_lead_total_count;
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
                render: function (data, type, row) {
                    let status_id  = row.campaign.campaign_status_id;
                    let campaign_type = '';
                      if(row.campaign.children.length) {
                status_id = parseInt(row.campaign.children[0].campaign_status_id);
                    }

            
                    if(row.campaign.parent_id) {
                        campaign_type = ' (Incremental)'
                    }
                    switch (parseInt(status_id)) {
                        case 1: return '<span class="badge badge-pill badge-success" style="padding: 5px;min-width:50px;"> Live'+campaign_type+' </span>';
                        case 2: return '<span class="badge badge-pill badge-warning" style="padding: 5px;min-width:50px;"> Paused'+campaign_type+' </span>';
                        case 3: return '<span class="badge badge-pill badge-danger" style="padding: 5px;min-width:50px;"> Cancelled'+campaign_type+' </span>';
                        case 4: return '<span class="badge badge-pill badge-primary" style="padding: 5px;min-width:50px;"> Delivered'+campaign_type+' </span>';
                        case 5: return '<span class="badge badge-pill badge-success" style="padding: 5px;min-width:50px;"> Reactivated'+campaign_type+' </span>';
                        case 6: return '<span class="badge badge-pill badge-secondary" style="padding: 5px;min-width:50px;"> Shortfall'+campaign_type+' </span>';
                    }
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    switch (parseInt(row.status)) {
                        case 1: return '<span class="badge badge-success" style="padding: 5px;min-width:50px;"> Active </span>';
                        case 2: return '<span class="badge badge-danger" style="padding: 5px;min-width:50px;"> Revoked </span>';
                    }
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    let html = '';
                    html += '<a href="'+URL+'/team-leader/campaign/view-details/'+btoa(row.id)+'" class="btn btn-outline-info btn-rounded btn-sm" title="View Campaign Details"><i class="feather icon-eye mr-0"></i></a>';
                    return html;
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
