

<div id="salary-comparison"></div>


<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('assets/js/apexcharts.js') }}"></script>
<script src="{{ asset('assets/js/lottie.js') }}"></script>
<!-- am core JavaScript -->
<script src="{{ asset('assets/js/core.js') }}"></script>
<!-- am charts JavaScript -->
<script src="{{ asset('assets/js/charts.js') }}"></script>

<script src="{{ asset('assets/js/animated.js') }}"></script>
<script src="{{ asset('assets/js/highcharts.js')}}"></script>

<script type="text/javascript">
	if (jQuery('#salary-comparison').length) {
            Highcharts.chart('salary-comparison', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: ''
                },
                xAxis: [{
                    categories: @php echo json_encode(array_keys($chart_data)); @endphp,
                    crosshair: true
                }],
                yAxis: [{ // Primary yAxis
                    labels: {
                        format: '{value} ৳',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },

                    title: {
                        text: 'BDT',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    }
                }],
                tooltip: {
                    shared: true
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    x: 120,
                    verticalAlign: 'top',
                    y: 100,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                        'rgba(255,255,255,0.25)'
                },
                series: [{
                    name: 'Salary',
                    type: 'area',
                    data: @php echo json_encode(array_values($chart_data)); @endphp,
                    color: '#FC9F5B',
                    tooltip: {
                        valueSuffix: ' H'
                    }
                }]
            });
        }
</script>