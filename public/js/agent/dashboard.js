'use strict';

let URL = $('meta[name="base-path"]').attr('content');

$(function (){

    getData();
    getGuageChartData();
    getCountsByWorkTypeBarChartData();
    getLeadsGeneratedCountsBarChartData();
    getTopProductivityData();

    $('#datepicker_range').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
    });

    $('#filter_monthly').datepicker({
        format: 'M-yyyy',
        autoclose: true,
        startView: "months",
        minViewMode: "months"
    });
});

$(function (){

    $('#filter_monthly').change(function() {
        getLeadsGeneratedCountsBarChartData();
    });

    $('#filter_start_date, #filter_end_date').change(function() {
        getData();
        getGuageChartData();
        getCountsByWorkTypeBarChartData();
        getTopProductivityData();
    });

});

function getData() {
    $.ajax({
        url: URL + '/agent/dashboard/get-data',
        dataType: 'JSON',
        data: {
            'start_date': $('#filter_start_date').val(),
            'end_date': $('#filter_end_date').val()
        },
        beforeSend: function() {
            $('.dashboard-count').val(0);
        },
        success: function(response) {
            $('#campaign_processed_count').val(response.campaign_processed.count);
            $('#campaign_processed_percentage').val(response.campaign_processed.percentage);

            $('#leads_generated_count').val(response.leads_generated.count);
            $('#leads_generated_percentage').val(response.leads_generated.percentage);

            $('#leads_qualified_count').val(response.leads_qualified.count);
            $('#leads_qualified_percentage').val(response.leads_qualified.percentage);

            $('#leads_rejected_count').val(response.leads_rejected.count);
            $('#leads_rejected_percentage').val(response.leads_rejected.percentage);

            $("input.dial").trigger('change');
        }
    });
}

function getGuageChartData() {
    $.ajax({
        url: URL + '/agent/dashboard/get-guage-chart-data',
        dataType: 'JSON',
        data: {
            'start_date': $('#filter_start_date').val(),
            'end_date': $('#filter_end_date').val()
        },
        beforeSend: function() {

        },
        success: function(response) {
            initProductivityGaugeChart(parseInt(response.guage_chart.productivity));
            initQualityGaugeChart(parseInt(response.guage_chart.quality));
        }
    });
}

function getTopProductivityData() {
    $.ajax({
        url: URL + '/agent/dashboard/get-top-productivity-data',
        dataType: 'JSON',
        data: {
            'start_date': $('#filter_start_date').val(),
            'end_date': $('#filter_end_date').val()
        },
        beforeSend: function() {

        },
        success: function(response) {

            let user_image_path = $('meta[name="user-image-path"]').attr('content');
            let user_default_image_path = $('meta[name="user-default-image-path"]').attr('content');

            $.each(response.top_productivity, function (key, value) {
                $('#top_productivity_'+(key+1)).removeAttr('class');

                switch ((key+1)) {
                    case 1:
                        $('#top_productivity_1').attr('class', 'radial-bar radial-bar-lg radial-bar-success '+'radial-bar-'+value.round_productivity);
                        break;
                    case 2:
                        $('#top_productivity_2').attr('class', 'radial-bar radial-bar-md radial-bar-warning '+'radial-bar-'+value.round_productivity);
                        break;
                    case 3:
                        $('#top_productivity_3').attr('class', 'radial-bar radial-bar-sm radial-bar-danger '+'radial-bar-'+value.round_productivity);
                        break;
                }

                if(value.user.profile != null) {
                    $('#top_productivity_' + (key+1)).find('img').attr('src', user_image_path + '/' + value.user.employee_code + '/profile/' + value.user.profile);
                } else {
                    $('#top_productivity_' + (key+1)).find('img').attr('src', user_default_image_path);
                }

                $('#top_productivity_' + (key+1)).attr('data-label', value.round_productivity+'%');
                $('#top_productivity_' + (key+1)).attr('data-original-title', value.user.full_name + ' - ' + value.productivity + '%');
            });

            $.each(response.top_quality, function (key, value) {
                $('#top_quality_'+(key+1)).removeAttr('class');

                switch ((key+1)) {
                    case 1:
                        $('#top_quality_1').attr('class', 'radial-bar radial-bar-lg radial-bar-success '+'radial-bar-'+value.round_quality);
                        break;
                    case 2:
                        $('#top_quality_2').attr('class', 'radial-bar radial-bar-md radial-bar-warning '+'radial-bar-'+value.round_quality);
                        break;
                    case 3:
                        $('#top_quality_3').attr('class', 'radial-bar radial-bar-sm radial-bar-danger '+'radial-bar-'+value.round_quality);
                        break;
                }

                if(value.user.profile) {
                    $('#top_quality_' + (key+1)).find('img').attr('src', user_image_path + '/' + value.user.employee_code + '/profile/' + value.user.profile);
                } else {
                    $('#top_quality_' + (key+1)).find('img').attr('src', user_default_image_path);
                }

                $('#top_quality_' + (key+1)).attr('data-label', value.round_quality+'%');
                $('#top_quality_' + (key+1)).attr('data-original-title', value.user.full_name + ' - ' + value.quality + '%');
            });
        }
    });
}

function getCountsByWorkTypeBarChartData() {
    $.ajax({
        url: URL + '/agent/dashboard/get-counts-by-work-type-bar-chart-data',
        dataType: 'JSON',
        data: {
            'start_date': $('#filter_start_date').val(),
            'end_date': $('#filter_end_date').val()
        },
        success: function(response) {
            initCountsByWorkTypeBarChart(response.bar_chart);
        }
    });
}

function getLeadsGeneratedCountsBarChartData() {
    $.ajax({
        url: URL + '/agent/dashboard/get-leads-generated-counts-bar-chart-data',
        dataType: 'JSON',
        data: {
            'month': $('#filter_monthly').val(),
        },
        success: function(response) {
            initLeadsGeneratedCountsBarChart(response.bar_chart);
        }
    });
}

function initProductivityGaugeChart(_data) {
    setTimeout(function() {
        // [ Gauge-chart ] start
        let dom = document.getElementById("chart-gauge-productivity");
        let myChart = echarts.init(dom);
        let app = {};
        let option = null;
        option = {
            tooltip: {
                formatter: "{a} <br/>{b} : {c}%"
            },
            series: [{
                name: 'gauge Chart',
                type: 'gauge',
                axisLine: {
                    show: true,
                    lineStyle: {
                        color: [
                            [0.8, '#FF0000'],
                            [1, '#00A400']
                        ],
                        width: 10
                    }
                },
                detail: {
                    formatter: '{value}%'
                },
                data: [{
                    value: _data,
                    name: ''
                }]
            }]
        };
        if (option && typeof option === "object") {
            myChart.setOption(option, true);
        }
        // [ Gauge-chart ] end
    }, 700);
}

function initQualityGaugeChart(_data) {
    setTimeout(function() {
        // [ Gauge-chart ] start
        let dom2 = document.getElementById("chart-gauge-quality");
        let myChart2 = echarts.init(dom2);
        let app2 = {};
        let option2 = null;
        option2 = {
            tooltip: {
                formatter: "{a} <br/>{b} : {c}%"
            },
            series: [{
                // min: 0,
                // max: 240,
                // splitNumber: 12,
                name: 'gauge Chart',
                type: 'gauge',
                axisLine: {
                    show: true,
                    lineStyle: {
                        color: [
                            [0.8, '#FF0000'],
                            [1, '#00A400']
                        ],
                        width: 10
                    }
                },
                detail: {
                    formatter: '{value}%'
                },
                data: [{
                    value: _data,
                    name: ''
                }]
            }]
        };
        if (option2 && typeof option2 === "object") {
            myChart2.setOption(option2, true);
        }
        // [ Gauge-chart ] end
    }, 700);
}

function initCountsByWorkTypeBarChart(_data) {
    setTimeout(function() {
        // [ Bar-chart ] start
        let dom7 = document.getElementById("bar-counts-by-work-type");
        let myChart7 = echarts.init(dom7);
        let app7 = {};
        let option7 = null;
        option7 = {
            xAxis: {
                type: 'category',
                data: _data.xAxis
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    data: _data.data,
                    type: 'bar'
                }
            ]
        };

        if (option7 && typeof option7 === 'object') {
            myChart7.setOption(option7);
        }
        // [ Bar-chart ] end
    }, 700);
}

function initLeadsGeneratedCountsBarChart(_data) {
    setTimeout(function() {
        // [ Bar-chart ] start
        let dom8 = document.getElementById("bar-leads-generated-monthly");
        let myChart8 = echarts.init(dom8);
        let app8 = {};
        let option8 = null;
        option8 = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow',
                    label: {
                        show: true
                    }
                }
            },
            toolbox: {
                show: true,
                feature: {
                    mark: { show: true },
                    dataView: { show: true, readOnly: false },
                    magicType: { show: true, type: ['line', 'bar'] },
                    saveAsImage: { show: true }
                }
            },
            calculable: true,
            legend: {
                data: ['Month', 'Budget 2011'],
                itemGap: 5
            },
            grid: {
                top: '12%',
                left: '1%',
                right: '10%',
                containLabel: true
            },
            xAxis: [
                {
                    type: 'category',
                    data: _data.xAxis,
                }
            ],
            yAxis: [
                {
                    type: 'value',
                    name: 'Leads'
                }
            ],
            series: [
                {
                    name: $('#filter_monthly').val(),
                    type: 'bar',
                    data: _data.data,
                    //color: '#3C66E9',
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: '#83bff6' },
                            { offset: 0.5, color: '#188df0' },
                            { offset: 1, color: '#188df0' }
                        ])
                    },
                    emphasis: {
                        itemStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: '#2378f7' },
                                { offset: 0.7, color: '#2378f7' },
                                { offset: 1, color: '#83bff6' }
                            ])
                        }
                    },
                }
            ]
        };

        option8 = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow',
                    label: {
                        show: true
                    }
                }
            },
            toolbox: {
                show: true,
                feature: {
                    mark: { show: true },
                    dataView: { show: true, readOnly: false },
                    magicType: { show: true, type: ['line', 'bar'] },
                    saveAsImage: { show: true }
                }
            },
            calculable: true,
            legend: {
                data: ['Month', $('#filter_monthly').val()],
                itemGap: 5
            },
            grid: {
                top: '12%',
                left: '1%',
                right: '10%',
                containLabel: true
            },
            xAxis: [
                {
                    type: 'category',
                    data: _data.xAxis,
                }
            ],
            yAxis: [
                {
                    type: 'value',
                    name: 'Leads',
                }
            ],
            series: [
                {
                    name: $('#filter_monthly').val(),
                    type: 'bar',
                    data: _data.data,
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: '#83bff6' },
                            { offset: 0.5, color: '#188df0' },
                            { offset: 1, color: '#188df0' }
                        ])
                    },
                    emphasis: {
                        itemStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                                { offset: 0, color: '#2378f7' },
                                { offset: 0.7, color: '#2378f7' },
                                { offset: 1, color: '#83bff6' }
                            ])
                        }
                    },
                }
            ]
        };

        if (option8 && typeof option8 === 'object') {
            myChart8.setOption(option8);
        }
        // [ Bar-chart ] end
    }, 700);
}
