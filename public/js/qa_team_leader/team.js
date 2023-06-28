
let USER_TABLE;
let URL = $('meta[name="base-path"]').attr('content');

$(function (){

    USER_TABLE = $('#table-users').DataTable({
        "lengthMenu": [ [10,20,50,100,-1], [10,20,50,100,'All'] ],
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": URL + '/qa-team-leader/team/get-team-members',
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
                data: 'employee_code',
            },
            {
                render: function (data, type, row) {
                    return row.first_name + ' ' + row.last_name;
                }
            },
            {
                data: 'email',
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    return row.department.name;
                }
            },
            {
                orderable: false,
                render: function (data, type, row) {
                    return row.designation.name;
                }
            },
        ],
        "fnDrawCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {

        },
    });

});
