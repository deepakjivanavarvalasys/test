
let URL = $('meta[name="base-path"]').attr('content');
var startdate,enddate;
var fstartdate,fenddate;
$(function (){
          
            
    $('#start_date,#end_date').on('change',function(){
        startdate =new Date($('#start_date').val());
        enddate =new Date($('#end_date').val());
        fstartdate=[startdate.getFullYear(),startdate.getMonth()+1,startdate.getDate()].join('-');
        fenddate=[enddate.getFullYear(),enddate.getMonth()+1,enddate.getDate()].join('-');
           //console.log(fstartdate+fenddate);
           initCounts(); 
           getRadialChartData();
           initChartcampaignprogress();
           });
                    
});

  
$(function (){
    initCounts();
    getRadialChartData();
    initChartcampaignprogress();
});

function initCounts() {
     $.ajax({
        url: URL + '/team-leader/dashboard/get-counts',
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


function getRadialChartData() {
    $.ajax({
        url: URL + '/team-leader/dashboard/get-radial-chart-data',
        dataType: 'JSON',
        data: {
            startdate:fstartdate,
             enddate:fenddate
            /*'month': $('#campaign-status-month-select').val(),
            'start_date': $('#campaign-status-start-date').val(),
            'end_date': $('#campaign-status-end-date').val()*/
        },
        beforeSend: function() {
            $("#lead-counts").text(0);
        },
        success: function(response) {
            if (Object.keys(response.chartData).length > 0) {
                //initChart(response.chartData);
                initChartRadial(response.chartData);
            }
        }
    });
}

// function initChart(chartData) {
//     var chart = am4core.create("am-pie-2", am4charts.PieChart);
//     var chartArray = [];
//     $.each(chartData, function(key, value) {
//         chartArray.push(value)
//     });
//     console.log(chartArray);
//     chart.data = chartArray;
//     var pieSeries = chart.series.push(new am4charts.PieSeries());
//     pieSeries.dataFields.value = "count";
//     pieSeries.dataFields.category = "status";
//     pieSeries.slices.template.stroke = am4core.color("#fff");
//     pieSeries.slices.template.strokeWidth = 2;
//     pieSeries.slices.template.strokeOpacity = 1;
//     chart.legend = new am4charts.Legend();
//     $('body').find("[aria-labelledby='id-61-title']").remove();
// }

function initChartRadial(chartData) {
    am4core.ready(function() {
        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end
        var container = am4core.create("chartdiv", am4core.Container);
        container.logo.disabled = true;

        container.width = am4core.percent(100);
        container.height = am4core.percent(100);
        container.layout = "horizontal";
        var chart = container.createChild(am4charts.PieChart);
        // Add data
        var chartArray = [];
        $.each(chartData, function(key, value) {
            chartArray.push(value);
        });
        chart.data = chartArray;
        // Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "count";
        pieSeries.dataFields.category = "status";
        pieSeries.slices.template.states.getKey("active").properties.shiftRadius = 0;
        pieSeries.labels.template.text = "{category}\n{value.percent.formatNumber('#.#')}%";
        pieSeries.slices.template.events.on("hit", function(event) {
            selectSlice(event.target.dataItem);
        });
        chart.legend = new am4charts.Legend();
        pieSeries.colors.list = [
            am4core.color("#28a745"), //Live
            am4core.color("#ffc107"), //Paused
            am4core.color("#dc3545"), //Cancelled
            am4core.color("#17a2b8"), //Delivered
            am4core.color("#33c754"), //Reactivated
            am4core.color("#6c757d"), //Shortfall
        ];

        var chart2 = container.createChild(am4charts.PieChart);
        chart2.width = am4core.percent(30);
        chart2.radius = am4core.percent(80);
        // Add and configure Series
        var pieSeries2 = chart2.series.push(new am4charts.PieSeries());
        pieSeries2.dataFields.value = "value";
        pieSeries2.dataFields.category = "name";
        pieSeries2.slices.template.states.getKey("active").properties.shiftRadius = 0;
        //pieSeries2.labels.template.radius = am4core.percent(100);
        //pieSeries2.labels.template.inside = true;
        //pieSeries2.labels.template.fill = am4core.color("#ffffff");
        pieSeries2.labels.template.disabled = true;
        pieSeries2.ticks.template.disabled = true;
        pieSeries2.alignLabels = false;
        pieSeries2.events.on("positionchanged", updateLines);
        var interfaceColors = new am4core.InterfaceColorSet();
        var line1 = container.createChild(am4core.Line);
        line1.strokeDasharray = "2,2";
        line1.strokeOpacity = 0.5;
        line1.stroke = interfaceColors.getFor("alternativeBackground");
        line1.isMeasured = false;
        var line2 = container.createChild(am4core.Line);
        line2.strokeDasharray = "2,2";
        line2.strokeOpacity = 0.5;
        line2.stroke = interfaceColors.getFor("alternativeBackground");
        line2.isMeasured = false;
        var selectedSlice;

        function selectSlice(dataItem) {
            selectedSlice = dataItem.slice;
            var fill = selectedSlice.fill;
            var count = dataItem.dataContext.subData.length;
            pieSeries2.colors.list = [];
            for (var i = 0; i < count; i++) {
                pieSeries2.colors.list.push(fill.brighten(i * 2 / count));
            }
            chart2.data = dataItem.dataContext.subData;
            pieSeries2.appear();
            var middleAngle = selectedSlice.middleAngle;
            var firstAngle = pieSeries.slices.getIndex(0).startAngle;
            var animation = pieSeries.animate([{
                property: "startAngle",
                to: firstAngle - middleAngle
            }, {
                property: "endAngle",
                to: firstAngle - middleAngle + 360
            }], 600, am4core.ease.sinOut);
            animation.events.on("animationprogress", updateLines);
            selectedSlice.events.on("transformed", updateLines);
            //  var animation = chart2.animate({property:"dx", from:-container.pixelWidth / 2, to:0}, 2000, am4core.ease.elasticOut)
            //  animation.events.on("animationprogress", updateLines)
        }

        function updateLines() {
            if (selectedSlice) {
                var p11 = {
                    x: selectedSlice.radius * am4core.math.cos(selectedSlice.startAngle),
                    y: selectedSlice.radius * am4core.math.sin(selectedSlice.startAngle)
                };
                var p12 = {
                    x: selectedSlice.radius * am4core.math.cos(selectedSlice.startAngle + selectedSlice
                        .arc),
                    y: selectedSlice.radius * am4core.math.sin(selectedSlice.startAngle + selectedSlice
                        .arc)
                };
                p11 = am4core.utils.spritePointToSvg(p11, selectedSlice);
                p12 = am4core.utils.spritePointToSvg(p12, selectedSlice);
                var p21 = {
                    x: 0,
                    y: -pieSeries2.pixelRadius
                };
                var p22 = {
                    x: 0,
                    y: pieSeries2.pixelRadius
                };
                p21 = am4core.utils.spritePointToSvg(p21, pieSeries2);
                p22 = am4core.utils.spritePointToSvg(p22, pieSeries2);
                line1.x1 = p11.x;
                line1.x2 = p21.x;
                line1.y1 = p11.y;
                line1.y2 = p21.y;
                line2.x1 = p12.x;
                line2.x2 = p22.x;
                line2.y1 = p12.y;
                line2.y2 = p22.y;
            }
        }
        chart.events.on("datavalidated", function() {
            setTimeout(function() {
                selectSlice(pieSeries.dataItems.getIndex(0));
            }, 1000);
        });
        $('body').find("[aria-labelledby='id-43-title']").remove();
    }); // end am4core.ready()
}

function initChartcampaignprogress(chartData) {

    am4core.ready(function() {
    
    // Themes begin
    am4core.useTheme(am4themes_animated);
    // Themes end
    
    // Create chart instance
    var chart = am4core.create("chartdivprogress", am4charts.PieChart);
    chart.logo.disabled = true;


    // Let's cut a hole in our Pie chart the size of 40% the radius
    chart.innerRadius = am4core.percent(40);
    
    // Add data
    var chartArray = [];
    $.each(chartData, function(key, value) {
        chartArray.push(value);
    });
    chart.data = chartArray;
    
    // Add and configure Series
    var pieSeries = chart.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "value";
    pieSeries.dataFields.category = "category";
    pieSeries.slices.template.stroke = am4core.color("#fff");
    pieSeries.innerRadius = 10;
    pieSeries.slices.template.fillOpacity = 0.5;
    
    pieSeries.slices.template.propertyFields.disabled = "labelDisabled";
    pieSeries.labels.template.propertyFields.disabled = "labelDisabled";
    pieSeries.ticks.template.propertyFields.disabled = "labelDisabled";
    
    
    // Add data
    // pieSeries.data = [{
    //   "category": "First + Second",
    //   "value": 60
    // }, {
    //   "category": "Unused",
    //   "value": 30,
    //   "labelDisabled":true
    // }];
    
    // Disable sliding out of slices
    pieSeries.slices.template.states.getKey("hover").properties.shiftRadius = 0;
    pieSeries.slices.template.states.getKey("hover").properties.scale = 1;
    
    // Add second series
    var pieSeries2 = chart.series.push(new am4charts.PieSeries());
    pieSeries2.dataFields.value = "value";
    pieSeries2.dataFields.category = "category";
    pieSeries2.slices.template.states.getKey("hover").properties.shiftRadius = 0;
    pieSeries2.slices.template.states.getKey("hover").properties.scale = 1;
    pieSeries2.slices.template.propertyFields.fill = "fill";
    
    // Add data
    pieSeries2.data = [{
      "category": "Campaign Processed",
      "value": 140
    }, {
      "category": "Other",
      "value": 30
    }];
    
    
    pieSeries.adapter.add("innerRadius", function(innerRadius, target){
      return am4core.percent(40);
    })
    
    pieSeries2.adapter.add("innerRadius", function(innerRadius, target){
      return am4core.percent(60);
    })
    
    pieSeries.adapter.add("radius", function(innerRadius, target){
      return am4core.percent(100);
    })
    
    pieSeries2.adapter.add("radius", function(innerRadius, target){
      return am4core.percent(80);
    })
    
    }); // end am4core.ready()
}