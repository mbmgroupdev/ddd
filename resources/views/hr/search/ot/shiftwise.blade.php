<style type="text/css">
    .shift-div{margin-bottom: 10px;
    background: #f9f9f9;
    padding: 10px;}

    
</style>
<div class="panel panel-info col-sm-12">
    <div class="panel-body">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-angle-double-right"></i>
                    <a href="#" class="search_all" data-category="{{ $request['category'] }}" data-type="{{ $request['type'] }}"> MBM Group </a>
                </li>
                @if(isset($request1['unit']))
                    <li>
                        <a href="#" class="search_area" data-unit="{{ $request1['unit'] }}">
                            {{ $data['unit']->hr_unit_name }}
                        </a>
                    </li>
                @endif
                @if(isset($request1['area_id']))
                    <li>
                         <a href="#" class="search_dept" data-area="{{ $request1['area'] }}">
                            {{ $data['area']->hr_area_name }}
                        </a>
                    </li>
                @endif
                @if(isset($request1['department']))
                    <li>
                        <a href="#" class="search_floor" data-department="{{ $request1['department'] }}">
                            {{ $data['department']->hr_department_name }}
                        </a>
                    </li>
                @endif
                @if(isset($request1['floor']))
                    <li>
                        <a href="#" class="search_section" data-floor="{{ $request1['floor'] }}">
                            {{ $data['floor']->hr_floor_name }}
                        </a>
                    </li>
                @endif
                @if(isset($request1['section']))
                    <li>
                        <a href="#" class="search_subsection" data-section="{{ $request1['section'] }}">
                            {{ $data['section']->hr_section_name }}
                        </a>
                    </li>
                @endif
                <li>  All Shift </li>
            </ul>
             <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($ot_data)}},"{{$showTitle}}")'>Print</a>
        </div>

        <hr>
        <p class="search-title">Search results of  {{ $showTitle }}</p>
        <div class="col-sm-12">
            <div class="row">
            @php $label=array(); $dataset=array(); @endphp
            @foreach($ot_data as $k=> $ot) 

            @php
                if($ot['dayot'] > 0) {

                    array_push($label, $ot['name']."(".$k.")");
                    array_push($dataset, $ot['dayot']);
                }
             @endphp  
             @if($ot['dayot'] >0 || $ot['dayot1'] > 0 || $ot['dayot'] > 0)
                <div class="col-sm-6">
                    <div class="col-sm-12 shift-div">
                        <center><h4>Shift: {{$k}} {{ $ot['name'] }}</h4></center>
                        <div class="row">
                            <div class="col-sm-6 pricing-box" >
                                <div class="widget-box widget-color-orange search_emp" data-shiftcode="{{$k}}">
                                    <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">
                                                Total Employee
                                            </h5>
                                    </div>

                                    <div class="widget-body">
                                        <div class="widget-main center">
                                            <span class="infobox-data-number"> {{ $ot['count'] }} </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 pricing-box" >
                                <div class="search_ot_hour widget-box widget-color-green2 " data-shiftcode="{{$k}}">
                                    <div class="widget-header">
                                            <h5 class="widget-title bigger lighter">Total OT</h5>
                                    </div>

                                    <div class="widget-body">
                                        <div class="widget-main center">
                                            <span class="infobox-data-number"> {{ $ot['dayot'] }} Hour(s)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="">                            
                            <div class="search_ot_hour widget-box widget-color-green2 " data-shiftcode="{{$k}}">
                                <div class="widget-header">
                                    <h5 class="widget-title bigger lighter">Over Time (Hour)</h5>
                                </div>

                                <div class="widget-body">
                                    <div class="widget-main center">
                                        <canvas id="otgraph-{{$k}}" style="height:300px!important;"></canvas>
                                        <script type="text/javascript">
                                            $(function(){
                                                var ctxL = document.getElementById("otgraph-{{$k}}").getContext('2d');

                                                var myLineChart = new Chart(ctxL, {
                                                    type: 'line',
                                                    data: {

                                                        labels: [ '{{ $ot['day2'] }}','{{ $ot['day1'] }}','{{ $ot['day'] }}'],
                                                        datasets: [{ 
                                                        data: [{{ $ot['dayot2'] }},{{ $ot['dayot2'] }},{{ $ot['dayot'] }}],
                                                        label: "Over Time (Hour)",
                                                        borderColor: "#3e95cd",
                                                        fill: false
                                                      }
                                                    ]
                                                    },
                                                    options: {
                                                        scales: {
                                                            yAxes: [{
                                                                ticks: {
                                                                    beginAtZero: true,
                                                                    steps: 10
                                                                },
                                                                afterFit: function(scale) {
                                                                   scale.width = 80  //<-- set value as you wish 
                                                                },
                                                                scaleLabel: {
                                                                    display: true,
                                                                    labelString: 'Hour(s)'
                                                                  }
                                                            }],
                                                            xAxes: [{
                                                                scaleLabel: {
                                                                    display: true,
                                                                    labelString: 'Date'
                                                                  }
                                                            }]
                                                        },
                                                        layout: {
                                                            padding: {
                                                                left: 0,
                                                                right: 50,
                                                                top: 0,
                                                                bottom: 0
                                                            }
                                                        },
                                                        legend: {
                                                            display: true,
                                                            position: 'bottom'
                                                        }
                                                    }
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @endforeach
            </div>
            @if(count($ot_data)>1) 
            <hr>
            <div class="row">
                <div class="col-sm-12">
                                               
                    <div class="widget-box ">
                        <div class="widget-header">
                            <h5 class="widget-title bigger lighter">Shiftwise Compariosn (OT)</h5>
                        </div>

                        <div class="widget-body">

                            <div class="widget-main center">
                               <div class="row">
                                    <div class="col-sm-offset-2 col-sm-8 col-xs-12"> 
                                        <canvas id="otgraph" ></canvas>
                                        <script type="text/javascript">
                                            $(function(){
                                                var ctxL = document.getElementById("otgraph").getContext('2d');


                                                var myBarChart = new Chart(ctxL, {
                                                    type: 'horizontalBar',
                                                    data: {

                                                        labels: <?php echo json_encode($label);?>,
                                                        datasets: [{ 
                                                        data: <?php echo json_encode($dataset);?>,
                                                        label: "Over Time Comparison (Shift)",
                                                        backgroundColor: [
                                                              "#f38b4a",
                                                              "#56d798",
                                                              "#ff8397",
                                                              "#6970d5" 
                                                            ],
                                                        fill: true
                                                      }
                                                    ]
                                                    },
                                                    options: {
                                                            scales: {
                                                                yAxes: [{
                                                                    ticks: {
                                                                        beginAtZero: true,
                                                                        stepValue:1
                                                                    },
                                                                    afterFit: function(scale) {
                                                                       scale.width = 100  //<-- set value as you wish 
                                                                    },
                                                                    scaleLabel: {
                                                                        display: true,
                                                                        labelString: 'Shift'
                                                                      }
                                                                }],
                                                                xAxes: [{
                                                                    ticks: {
                                                                        beginAtZero: true,
                                                                        stepValue:1
                                                                    },
                                                                    scaleLabel: {
                                                                        display: true,
                                                                        labelString: 'Hour(s)'
                                                                      }
                                                                }]
                                                            },
                                                            layout: {
                                                                padding: {
                                                                    left: 0,
                                                                    right: 5,
                                                                    top: 0,
                                                                    bottom: 0
                                                                }
                                                            },
                                                            legend: {
                                                                display: true,
                                                                position: 'bottom'
                                                            }
                                                        }
                                                });
                                            });
                                        </script>
                                    </div>
                                   
                               </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            @endif           
        </div>
    </span>
</div>
<div id="printOutputSection" style="display: none;"></div>
<script>
function printDiv(result,pagetitle) {
        $.ajax({
            url: '{{url('hr/search/hr_ot_search_print_page')}}',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                data: result,
                type: 'Shift',
                title: pagetitle,
                unit: '{{ isset($request1['unit'])?$request1['unit']:'' }}',
                area: '{{ isset($request1['area'])?$request1['area']:'' }}',
                department: '{{ isset($request1['department'])?$request1['department']:'' }}',
                section: '{{ isset($request1['section'])?$request1['section']:'' }}',
                floor: '{{ isset($request1['floor'])?$request1['floor']:'' }}',
                subsection: '{{ isset($request1['subsection'])?$request1['subsection']:'' }}'
            },
            success: function(data) {
                $('#printOutputSection').html(data);
                var divToPrint=document.getElementById('printOutputSection');
                var newWin=window.open('','Print-Window');
                newWin.document.open();
                newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
                newWin.document.close();
                setTimeout(function(){newWin.close();},10);
            }
        });
    }
</script>