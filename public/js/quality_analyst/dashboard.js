
let URL = $('meta[name="base-path"]').attr('content');
var startdate,enddate;
var fstartdate,fenddate;
$(function (){
          
            
    $('#start_date,#end_date').on('change',function(){
        startdate =new Date($('#start_date').val());
        enddate =new Date($('#end_date').val());
        fstartdate=[startdate.getFullYear(),startdate.getMonth()+1,startdate.getDate()].join('-');
        fenddate=[enddate.getFullYear(),enddate.getMonth()+1,enddate.getDate()].join('-');
           console.log(fstartdate+fenddate);
           initCounts();     
          }); 
          
});

  
initCounts();   

function initCounts() {
    $.ajax({
        url: URL + '/quality-analyst/dashboard/get-counts',
        data:{
            startdate:fstartdate,
            enddate:fenddate
       },
        dataType: 'JSON',
        success: function(response) {
            console.log(response);
            $(".lead-counts").text(0);
            $.each(response, function(key, value) {
                $('#count-' + key).text(value);
            });
        }
    });
}
