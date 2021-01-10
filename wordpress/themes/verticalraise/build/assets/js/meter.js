var guageSize = '200';
var yaxis = 85;
var meterChart = [];
var meterObj = [
    {
        obj: 'meter1',
        max_value: 100,
        text: 'Participation Score'
    },
    {
        obj: 'meter2',
        max_value: 100,
        text: 'Email Quality Score'
    },
    {
        obj: 'meter3',
        max_value: 100,
        text: 'Participants with at least 1 donation'
    }
];

$(document).ready(function () {
    resizeScreen();
});
$(window).resize(function () {
    resizeScreen();
});
function resizeScreen() {
    if ($(window).width() <= 768) {
        Css_is_mobile = 1;
        guageSize = '150';
        yaxis = 70;
    } else {
        Css_is_mobile = 0;
        guageSize = '200';
        yaxis = 85;
    }
    DrawGuage();
}
function DrawGuage() {
    meterObj.forEach(function (item, index) {
        meterChart[index] = Highcharts.chart(item.obj, {
            chart: {
                type: 'gauge',
                plotBackgroundColor: null,
                plotBackgroundImage: null,
                plotBorderWidth: 0,
                plotShadow: false,
                backgroundColor: 'transparent'
            },
            credits: {
                enabled: false
            },
            title: {
                text: ''
            },

            //
            plotOptions: {
                gauge: {
                    dataLabels: {
                        enabled: false
                    },
                    dial: {
                        radius: '70%',
                        backgroundColor: '#fff',
                        topWidth: 1,
                        baseWidth: 7,
                        rearLength: '20%'
                    },
                    pivot: {
                        radius: 5,
                        borderWidth: 7,
                        borderColor: '#fff',
                        backgroundColor: '#fff'
                    }
                }
            },

            pane: {
                startAngle: -80,
                endAngle: 80,
                center: ['50%', '55%'],
                size: guageSize,
                startAngle: -80,
                endAngle: 80,

                background: {
                    backgroundColor: {
                        linearGradient: {
                            x1: 0,
                            x2: 1,
                            x3: 1,
                            y1: 1,
                            y2: 1,
                            y3: 1
                        },
                        stops: [[0, '#ed1c24'], [0.5, 'yellow'], [1, '#46ce53']],
                    },
                    borderColor: 'transparent',
                    innerRadius: '140%',
                    outerRadius: '100%',

                    shape: 'solid',
                    className: 'pane'

                },
            },

            // the value axis
            yAxis: {
                min: 0,
                max: item.max_value,

                minorTickInterval: 'auto',
                minorTickWidth: 1,
                minorTickLength: 5,
                minorTickPosition: 'inside',
                minorTickColor: '#fff',

                tickPixelInterval: 25,
                tickWidth: 2,
                tickPosition: 'inside',
                tickLength: 10,
                tickColor: '#fff',

                labels: {
                    step: 5,
                    rotation: 'auto'
                },
                title: {
                    align: 'middle',
                    enabled: 'middle',
//                    text: '<p class="' + item.obj + '_text" style="font-size:13px;color:white;text-align:center">' + item.text + ': 0%</p>',
//                    y: yaxis,
//                    style: {
//                        fontWeight: 'normal',
//                        textAlign: 'center'
//                    }
                },
                plotBands: []

            },

            series: [{
                    name: 'Meter',
                    data: [0]
                }]

        });
        
        
        meterChart[index].series[0].data[0].update(0);
        var meterText = '<p class="meter_text" style="">'+item.text+': ' + 0 + '%</p>';
        $("#meter"+(index+1)).append($(meterText))        

    })
}




