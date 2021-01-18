<div class="page-content">
        
  <div class="row1">
      @if(isset($info))
      <div class="panel w-100">
          <div class="panel-body">
              <div class="offset-1 col-10 h-min-400">
                  
                  @php
                      $year  = date('Y', strtotime(request()->month_year));
                      $month = date('m', strtotime(request()->month_year));
                      $lastMonth = date('m',strtotime("-1 month"));
                      $thisMonth = date('m');
                      
                      // check activity lock/unlock
                      $yearMonth = date('Y-m', strtotime('-1 month'));
                      $lock['month'] = date('m', strtotime($yearMonth));
                      $lock['year'] = date('Y', strtotime($yearMonth));
                      $lock['unit_id'] = $info->as_unit_id;
                      $lockActivity = monthly_activity_close($lock);
                  @endphp
                  <div class="iq-card" style="border: 1px solid #ccc; box-shadow: 1px 0px 3px;">
                      
                      @php 
                          $yearMonth = request()->month_year; 
                          $user  = auth()->user();
                          $flagStatus = 0;
                          $asStatus = emp_status_name($info->as_status);
                          $statusDate = $info->as_status_date;
                          if($statusDate != null && $info->as_status != 1){
                              $statusMonth = date('Ym', strtotime($info->as_status_date));
                              $requestMonth = date('Ym', strtotime($yearMonth));
                            
                              if($requestMonth > $statusMonth){
                                  $flagStatus = 1;
                              }
                          }
                      @endphp
                      <div class="iq-card-body pt-0">
                          <div class="result-data" id="result-data">
                              
                              <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:5px auto;">
                                  <div class="page-header" id="brand-head" style="border-bottom:2px double #666; text-align: center;">
                                      <h3 style="margin:4px 10px">
                                        {{ $info->unit }} 
                                        @if($flagStatus == 0)
                                          @if($user->can('Attendance Operation') || $user->hasRole('Super Admin'))
                                            @if(($lastMonth == $month && $lockActivity == 0)|| $month == date('m'))
                                            <div class="text-right" style="display: inline-block; float: right;">
                                              <h4 class="card-title capitalize inline">
                                              <a target="_blank" href='{{url("hr/timeattendance/attendance_bulk_manual?associate=$info->associate_id&month=$yearMonth")}}' class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Manual Edit Job Card">
                                                <i class="fa fa-edit bigger-120"></i>
                                              </a>
                                              </h4>
                                            </div>
                                            @endif
                                          @endif
                                      @endif
                                      </h3>
                                      <h5 style="margin:4px 10px">Job Card Report</h5>

                                      <h5 style="margin:4px 10px">For the month of {{date('F, Y', strtotime(request()->month_year))}}</h5>
                                  </div>
                                  <table class="table" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left"  cellpadding="5">
                                      <tr>
                                          <th style="width:32%">
                                             <p style="margin:0;padding:4px 10px"><strong>ID </strong> # {{ $info->associate_id }}</p>
                                             <p style="margin:0;padding:4px 10px"><strong>Name </strong>: {{ $info->as_name }}</p>
                                             <p style="margin:0;padding:4px 10px"><strong>DOJ </strong>: {{ date("d-m-Y", strtotime($info->as_doj)) }}</p>
                                          </th>
                                          <th>
                                              <p style="margin:0;padding:4px 10px"><strong>Oracle ID </strong>: {{ $info->as_oracle_code }} </p>
                                             <p style="margin:0;padding:4px 10px"><strong>Section </strong>: {{ $info->section }} <span>@if($info->pre_section != null) - Previous: {{ $pre_section }} @endif</span> </p>
                                             <p style="margin:0;padding:4px 10px"><strong>Designation </strong>: {{ $info->designation }} <span>@if($info->pre_designation != null) - Previous: {{ $pre_designation }} @endif</span></p>
                                          </th>
                                          <th>
                                             <p style="margin:0;padding:4px 10px"><strong>Total Present </strong>: <b >{{ $info->present }}</b> </p>
                                             <p style="margin:0;padding:4px 10px"><strong>Total Absent </strong>: <b >{{ $info->absent }}</b></p>
                                             <p style="margin:0;padding:4px 10px"><strong>Total OT </strong>: <b>{{ numberToTimeClockFormat($info->ot_hour) }}</b> </p>
                                          </th>
                                      </tr>
                                  </table>

                                  <table class="table" style="width:100%;border:1px solid #ccc;font-size:13px;display: block;overflow-x: auto;white-space: nowrap;"  cellpadding="2" cellspacing="0" border="1" align="center">
                                      <thead>
                                          <tr>
                                              <th width="20%">Date</th>
                                              <th width="20%">Attendance Status</th>
                                              <th width="20%">Floor</th>
                                              <th width="20%">Line</th>
                                              <th width="30%">In Time</th>
                                              <th width="30%">Out Time</th>
                                              <th width="30%">OT Hour</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          
                                          @if($flagStatus == 0)
                                          @foreach($attendance as $value)
                                          <tr>
                                              <td>
                                                  {{ $value['date'] }}
                                                  @if($joinExist)
                                                      @if($value['date'] == $info->as_doj)
                                                          <span class="label label-success arrowed-right arrowed-in pull-right">Joined</span>
                                                      @endif
                                                  @endif
                                                  @if($leftExist)
                                                      @if($value['date'] == $info->as_status_date)
                                                          @php
                                                              $flag = '';
                                                              if($info->as_status === 0) {
                                                                  $flag = 'Delete';
                                                              } else if($info->as_status === 2) {
                                                                  $flag = 'Resign';
                                                              } else if($info->as_status === 3) {
                                                                  $flag = 'Terminate';
                                                              } else if($info->as_status === 4) {
                                                                  $flag = 'Suspend';
                                                              } else if($info->as_status === 5) {
                                                                  $flag = 'Left';
                                                              }
                                                          @endphp
                                                          @if($flag != '')
                                                          <span class="label label-warning arrowed-right arrowed-in pull-right">
                                                              {{ $flag }}
                                                          </span>
                                                          @endif
                                                      @endif
                                                  @endif
                                              </td>
                                              <td @if($value['present_status'] == 'A' || $value['present_status'] == 'Weekend(General) - A') style="background: #ea9d99;color:#000;" @endif>
                                              {!! $value['present_status'] !!}

                                              @if($value['late_status']==1)
                                                  <span style="height: auto;float:right;" class="label label-warning pull-right">Late</span>
                                              @endif
                                              @if($value['remarks']== 'HD')
                                                  <span style="height: auto;float:right;" class="label label-danger pull-right">Half Day @if($value['late_status']==1) , @endif</span>

                                              @endif
                                              @if($value['outside'] != null)
                                              <span style="height: auto;float:right;cursor:pointer;" class="label label-success pull-right" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$value['outside_msg']}}" >{{$value['outside']}}</span>
                                              @endif
                                              </td>
                                              <td>{{ $value['floor'] }}</td>
                                              <td>{{ $value['line'] }}</td>
                                              <td>{{!empty($value['in_time'])?$value['in_time']:null}}</td>
                                              <td>{{!empty($value['out_time'])?$value['out_time']:null}}</td>
                                              <td>
                                              @if($info->as_ot==1)
                                                  {{numberToTimeClockFormat($value['overtime_time'])}}
                                              @endif
                                              </td>
                                          </tr>
                                          @endforeach
                                          @else
                                          <tr>
                                              <td colspan="7" class="text-center">This Employee is {{ $asStatus }}</td>
                                          </tr>
                                          @endif
                                      </tbody>



                                      <tfoot style="border-top:2px double #999">
                                          <input type="hidden" id="present" value="">
                                          <input type="hidden" id="absent" value="">
                                          <tr>
                                              <th style="text-align:right">Total present</th>
                                              <th>{{ $info->present }}</th>
                                              <th></th>
                                              <th></th>
                                              <th></th>
                                              <th style="text-align:right">Total Over Time</th>
                                              <th>
                                              @if($info->as_ot==1)

                                                  {{numberToTimeClockFormat($info->ot_hour)}}

                                                  <input type="hidden" id="ot" value="0">
                                              @endif
                                              </th>
                                          </tr>
                                      </tfoot>
                                  </table>
                              </div>
                          </div> 

                      </div>
                  </div>
                  
              </div>
          </div>
      </div>
      @endif
  </div>
</div><!-- /.page-content -->
@push('js')
<script type="text/javascript">
    function printMe1(divName)
    {
        var myWindow=window.open('','','width=800,height=800');
        myWindow.document.write('<style>.page-header{text-align:center;}</style>');
        myWindow.document.write(document.getElementById(divName).innerHTML);
        myWindow.document.close();
        myWindow.focus();
        myWindow.print();
        myWindow.close();
    }

    $(document).ready(function(){
        //total status show
        $('#total-present').html($('#present').val());
        $('#total-absent').html($('#absent').val());
        $('#total-ot').html($('#ot').val());
        //select 2 check
        function formatState (state) {
         //console.log(state.element);
            if (!state.id) {
                return state.text;
            }
            var $state = $(
                '<span><img /> <span></span></span>'
            );

            var targetName = state.text;
            $state.find("span").text(targetName);
            return $state;
        };

       $('#excel').click(function(){
        var url='data:application/vnd.ms-excel,' + encodeURIComponent($('#html-2-pdfwrapper').html())
        location.href=url
        return false
          })

    });
    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })
</script>
@endpush
  