@extends('hr.layout')
@section('title', 'HR Dashboard')
@section('main-content')

@php $user = auth()->user(); @endphp
   @include('hr.common.employee_count')
  <div class="row">
      
      
      
      @if($user->can('Monthly Attendance') || $user->hasRole('Super Admin'))
      <div class="col-lg-6">
        <div class="panel iq-card-block iq-card-stretch iq-card-height">
            <div class="panel-heading d-flex justify-content-between">
                  <h6>Attendance: {{date('F')}}</h6>
            </div>
            <div class="panel-body">
                <div id="att-chart"></div>
               
            </div>
        </div>
         <!--  -->
      </div>  
      @endif

      @if($user->can('Today Attendance') || $user->hasRole('Super Admin'))
      <div class="col-md-6">
         <div class="panel iq-card-block iq-card-stretch iq-card-height">
            <div class="panel-heading d-flex justify-content-between">
                  <h6>Today's Attendance</h6>
            </div>
            <div class="iq-card-body text-center">
               @if($today_att_chart['present'] > 0 )
                   <div id="today-att" style="width: 100%; height: 400px;"></div>
                @else
                    <p class=" text-danger p-5">Today's Attendance has not been uploaded! Please make sure this step's should done before attendance file upload.</p>

                    <ul class="iq-timeline" style="width:200px;margin:0 auto;">
                      <li>
                         <div class="timeline-dots"></div>
                         <h6 class="mb-1"><a target="_blank" href="{{ url('hr/operation/shift')}}" >Add Shift</a></h6>
                      </li>
                      <li>
                         <div class="timeline-dots border-success"></div>
                         <h6 class="mb-1"><a target="_blank" href="{{ url('hr/operation/shift_assign')}}" >Shift Assign</a></h6>
                      </li>
                      <li>
                         <div class="timeline-dots border-primary"></div>
                         <h6 class="mb-1"><a target="_blank" href="{{ url('hr/operation/holiday-roster')}}"  style="width:200px;">Holiday Roster</a></h6>
                      </li>
                      <li>
                         <div class="timeline-dots border-warning"></div>
                         <h6 class="mb-1"><a target="_blank" href="{{ url('hr/timeattendance/attendance-upload')}}" >Upload Attendance</a></h6>
                      </li>
                   </ul>
                    
                    
                    
                    
                @endif
            </div>
         </div>
      </div>
      @endif
      @if($user->can('Monthly Salary Comparison') || $user->hasRole('Super Admin'))
       <div class="col-lg-6">
         <div class="panel iq-card-block iq-card-stretch iq-card-height">
            <div class="panel-heading d-flex justify-content-between">
                  <h6>Monthly Salary</h6>
            </div>
            <div class="iq-card-body">
               <div id="monthly-salary-chart"></div>
            </div>
         </div>
      </div>
      @endif
      @php  $today = date('Y-m-d'); @endphp
      @if($user->can('Monthly OT') || $user->hasRole('Super Admin'))               
      <div class="col-md-6">
         <div class="panel iq-card-block iq-card-stretch iq-card-height">
            <div class="panel-heading d-flex justify-content-between">
                  <h6>Monthly Overtime</h6>
            </div>
            <div class="iq-card-body">
               <div id="ot-comparison"></div>
            </div>
         </div>
      </div>
      @endif
      @if($user->can('Holiday Roster') || $user->hasRole('Super Admin'))
      <div class="col-sm-6">
        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
          <div class="iq-card-header d-flex justify-content-between">
             <div class="iq-header-title">
                <h4 class="card-title">Holiday Employee Records </h4>
             </div>
             <div class="iq-card-header-toolbar d-flex align-items-center">
                
                <a href='{{ url("hr/operation/undeclared-employee?date=$today&report_type=1") }}' target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa fa-list"></i> See All</a>
             </div>
          </div>
          <div class="iq-card-body pt-0">
             <div class="table-responsive">
                <table class="table table-hover table-head">
                   <thead>
                      <tr>
                         <th scope="col">Associate ID </th>
                         <th scope="col">Name</th>
                         <th scope="col">Date</th>
                         <th scope="col">Time</th>
                         <th scope="col"></th>

                      </tr>
                   </thead>
                   <tbody>
                      @if(count($getHolidayRecord) > 0)
                      @foreach($getHolidayRecord as $record)
                      @php $associateId = $record->employee['associate_id']; @endphp
                        <tr>
                          <td><a href='{{ url("hr/operation/holiday-roster?associate=$associateId")}}' target="_blank">{{ $record->employee['associate_id'] }}</a></td>
                          <td>{{ $record->employee['as_name'] }}</td>
                          <td>{{ $record->punch_date }}</td>
                          <td>{{ (($record->punch_time != null && $record->punch_time != '00:00:00')?date('H:i:s', strtotime($record->punch_time)):'') }}</td>
                          <td></td>
                        </tr>
                      @endforeach
                      @else
                      <tr>
                        <td class="text-center" colspan="5">No Record Found!</td>
                      </tr>
                      @endif
                   </tbody>
                </table>
             </div>
          </div>
       </div>
      </div>
      <div class="col-sm-6">
        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
          <div class="iq-card-header d-flex justify-content-between">
             <div class="iq-header-title">
                <h4 class="card-title">Leave Employee Records </h4>
             </div>
             <div class="iq-card-header-toolbar d-flex align-items-center">
                <a href='{{ url("hr/operation/undeclared-employee?date=$today&report_type=2") }}' class="btn btn-sm btn-outline-primary"><i class="fa fa-list"></i> See All</a>
             </div>
          </div>
          <div class="iq-card-body pt-0">
             <div class="table-responsive">
                <table class="table table-hover table-head">
                   <thead>
                      <tr>
                         <th scope="col">Associate ID </th>
                         <th scope="col">Name</th>
                         <th scope="col">Date</th>
                         <th scope="col"></th>

                      </tr>
                   </thead>
                   <tbody>
                      @if(count($getLeaveRecord) > 0)
                      @foreach($getLeaveRecord as $record)
                        <tr>
                          <td>{{ $record->employee['associate_id'] }}</td>
                          <td>{{ $record->employee['as_name'] }}</td>
                          <td>{{ $record->punch_date }}</td>
                          <td>{{ (($record->punch_time != null && $record->punch_time != '00:00:00')?date('H:i:s', strtotime($record->punch_time)):'') }}</td>
                          <td></td>
                        </tr>
                      @endforeach
                      @else
                      <tr>
                        <td class="text-center" colspan="5">No Record Found!</td>
                      </tr>
                      @endif
                      
                   </tbody>
                </table>
             </div>
          </div>
       </div>
      </div>
      @endif
      
   </div>
   @push('js')
      <script src="{{ asset('assets/js/apexcharts.js') }}"></script>
      <script src="{{ asset('assets/js/lottie.js') }}"></script>
      <!-- am core JavaScript -->
      <script src="{{ asset('assets/js/core.js') }}"></script>
      <!-- am charts JavaScript -->
      <script src="{{ asset('assets/js/charts.js') }}"></script>
      
      <script src="{{ asset('assets/js/animated.js') }}"></script>
      <script src="{{ asset('assets/js/highcharts.js')}}"></script>
      <!-- Chart Custom JavaScript -->
      

      <script type="text/javascript">

        @if($user->can('Monthly Salary Comparison') || $user->hasRole('Super Admin')) 
        jQuery("#monthly-salary-chart").length && am4core.ready(function() {
            var options = {
                  series: [{
                  name: 'Salary (Lakh)',
                  data: @php echo json_encode(array_values($salary_chart['salary'])); @endphp
                }, {
                  name: 'OT Payment (Lakh)',
                  data: @php echo json_encode(array_values($salary_chart['ot'])); @endphp
                }],
                colors: ['#089bab','#FC9F5B'],
                  chart: {
                  type: 'bar',
                  height: 350,
                  stacked: true,
                  toolbar: {
                    show: true
                  },
                  zoom: {
                    enabled: true
                  }
                },
                responsive: [{
                  breakpoint: 480,
                  options: {
                    legend: {
                      position: 'bottom',
                      offsetX: -10,
                      offsetY: 0
                    }
                  }
                }],
                plotOptions: {
                  bar: {
                    horizontal: false,
                  },
                },
                xaxis: {
                  categories: @php echo json_encode(array_values($salary_chart['category'])); @endphp,
                },
                /*yaxis:{
                    labels: {
                        formatter: function() {
                            return this.value / 1000 + 'K'; // clean, unformatted number for year
                        }
                    },
                },*/
                legend: {
                  position: 'right', 
                  offsetY: 40
                },
                fill: {
                  opacity: 1
                }
                };

                var chart = new ApexCharts(document.querySelector("#monthly-salary-chart"), options);
                chart.render();
        })
        @endif

        @if($user->can('Monthly OT') || $user->hasRole('Super Admin')) 
        if (jQuery('#ot-comparison').length) {
            Highcharts.chart('ot-comparison', {
                chart: {
                    zoomType: 'xy'
                },
                title: {
                    text: ''
                },
                xAxis: [{
                    categories: @php echo json_encode(array_keys($ot_chart)); @endphp,
                    crosshair: true
                }],
                yAxis: [{ // Primary yAxis
                    labels: {
                        format: '{value} H',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },

                    title: {
                        text: 'Overtime',
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
                    name: 'Overtime',
                    type: 'area',
                    data: @php echo json_encode(array_values($ot_chart)); @endphp,
                    color: '#FC9F5B',
                    tooltip: {
                        valueSuffix: ' H'
                    }
                }]
            });
        }
        @endif

        @if($user->can('Monthly Attendance') || $user->hasRole('Super Admin')) 
        if (jQuery('#att-chart').length) {
           Highcharts.chart('att-chart', {
               chart: {
                   type: 'area'
               },
               accessibility: {
                   description: ''
               },
               title: {
                   text: ''
               },
               subtitle: {
                   text: ''
               },
               xAxis: {
                   allowDecimals: false,
                   labels: {
                       formatter: function() {
                           return this.value+' {{date("M")}}'; // clean, unformatted number for year
                       }
                   },
                   accessibility: {
                       rangeDescription: 'This month'
                   }
               },
               yAxis: {
                   title: {
                       text: 'Employee'
                   },
                   /*labels: {
                       formatter: function() {
                           return this.value / 1000 + 'k';
                       }
                   }*/
               },
               tooltip: {
                   pointFormat: '{series.name} had present <b>{point.y:,.0f}</b><br/>employees at {point.x} {{date("F")}}'
               },
               plotOptions: {
                   area: {
                       pointStart: 1,
                       marker: {
                           enabled: false,
                           symbol: 'circle',
                           radius: 2,
                           states: {
                               hover: {
                                   enabled: true
                               }
                           }
                       }
                   }
               },
               series: [
               @if(in_array(1, auth()->user()->unit_permissions()))
               {
                   name: 'MBM',
                   data: @php echo json_encode(array_values($att_chart['mbm'])); @endphp,
                   color: '#089bab'
               },
               @endif 
               @if(in_array(5, auth()->user()->unit_permissions()))
               {
                   name: 'MBM2',
                   data: @php echo json_encode(array_values($att_chart['mbm2'])); @endphp,
                   color: '#5834eb'
               }, 
               @endif
               @if(in_array(4, auth()->user()->unit_permissions()))
               {
                   name: 'MFW',
                   data: @php echo json_encode(array_values($att_chart['mfw'])); @endphp,
                   color: '#242036'
               }, 
               @endif
               @if(in_array(2, auth()->user()->unit_permissions()))
               {
                   name: 'CEIL',
                   data: @php echo json_encode(array_values($att_chart['ceil'])); @endphp,
                   color: '#FC9F5B'
               },
               @endif
               @if(in_array(3, auth()->user()->unit_permissions())) 
               {
                   name: 'AQL',
                   data: @php echo json_encode(array_values($att_chart['aql'])); @endphp,
                   color: '#0abb78'
               }
               @endif
               ]
           });
        }
        @endif
        @if($user->can('Today Attendance') || $user->hasRole('Super Admin')) 
          if (jQuery('#today-att').length) {
             am4core.ready(function() {

                 // Themes begin
                 am4core.useTheme(am4themes_animated);
                 // Themes end

                 var chart = am4core.create("today-att", am4charts.PieChart3D);
                 chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

                 chart.legend = new am4charts.Legend();

                 chart.data = [ {
                     title: "Present",
                     employee: {{($today_att_chart['present']??0)}}
                 }, {
                     title: "Leave",
                     employee: {{$today_att_chart['leave']??0}}
                 }, {
                     title: "Absent",
                     employee: {{$today_att_chart['absent']??0}}
                 },{
                     title: "Holiday",
                     employee: {{$today_att_chart['holiday']??0}}
                 }];

                 var series = chart.series.push(new am4charts.PieSeries3D());
                 series.colors.list = [am4core.color("#089bab"), am4core.color("#208207"), am4core.color("#f45b5b"),
                     am4core.color("#FC9F5B")
                 ];
                 series.dataFields.value = "employee";
                 series.dataFields.category = "title";

             }); // end am4core.ready()
          }
        @endif
      </script>
   @endpush 
@endsection

