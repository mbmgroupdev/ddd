<div class="panel w-100">
    <div class="panel-body">
        <div>
            @php
                $associate = $info['associate_id'];
                $nextMonth = date('Y-m', strtotime($info['yearMonth'].' +1 month'));
                $user  = auth()->user();
                $yearMonth = $info['yearMonth']; 
                $year = $info['year']; 
                $month = $info['month'];
                // check activity lock/unlock
                $lock['month'] = date('m', strtotime($yearMonth));
                $lock['year'] = date('Y', strtotime($yearMonth));
                $lock['unit_id'] = $info['as_unit_id'];
                $lockActivity = monthly_activity_close($lock);
                // 
                $start = date('d', strtotime($info['firstDayMonth']));
                $end = date('d', strtotime($info['lastDayMonth']));
                $yearMonthD = $yearMonth.'-';
                $urlSegment = explode('/', url()->previous());
            @endphp
            <div class="iq-card">
                <div class="iq-card-header d-flex mb-0">
                   <div class="iq-header-title w-100">
                      <div class="row">
                        <div class="col-3">
                            <div class="action-section">
                                <h4 class="card-title capitalize inline">
                                    <button type="button" onClick="printDiv('result-data-section')" class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Job Card">
                                       <i class="fa fa-print"></i>
                                    </button>
                                    
                                </h4>
                            </div>
                        </div>
                        <div class="col-6 text-center">
                          <h4 class="card-title capitalize inline">

                            @if(in_array('job_card', $urlSegment))
                                @if($user->can('Attendance Operation') || $user->hasRole('Super Admin'))
                                <a class="btn view prev_btn" >
                                  <i class="las la-chevron-left"></i>
                                </a>
                                @endif
                                <b class="f-16" id="result-head">{{ date('F, Y', strtotime($info['yearMonth'])) }} </b>
                                @if($user->can('Attendance Operation') || $user->hasRole('Super Admin'))
                                    @if(strtotime(date('Y-m')) >= strtotime($nextMonth))
                                    <a class="btn view next_btn" >
                                      <i class="las la-chevron-right"></i>
                                    </a>
                                    @endif
                                @endif
                            @else
                            <b class="f-16" id="result-head">{{ date('F, Y', strtotime($info['yearMonth'])) }} </b>
                            @endif
                          </h4>
                        </div>
                        <div class="col-3">
                        @if($user->can('Attendance Operation') || $user->hasRole('Super Admin'))
                          @if($lockActivity == 0 && $info['as_status'] == 1)
                          <div class="text-right">
                            <h4 class="card-title capitalize inline">
                            <a href='{{url("hr/timeattendance/attendance_bulk_manual?associate=$associate&month=$yearMonth")}}' class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Manual Edit Job Card">
                              <i class="fa fa-edit bigger-120"></i>
                            </a>
                            </h4>
                          </div>
                          @endif
                        @endif
                        </div>
                      </div>
                   </div>
                </div>
                <div class="iq-card-body p-0">
                    <ul class="color-bar">
                            <li><span class="color-label bg-default"></span><span class="lib-label"> Present </span></li>
                            <li><span class="color-label bg-danger"></span><span class="lib-label"> Absent </span></li>
                            <li><span class="color-label bg-warning"></span><span class="lib-label"> Weekend </span></li>
                            <li><span class="color-label bg-primary"></span><span class="lib-label"> Leave</span></li>
                            <li><span class="color-label bg-success"></span><span class="lib-label"> Outside</span></li>
                            <li><span class="color-label bg-dark"></span><span class="lib-label"> Extra OT</span></li>
                        </ul>
                    <div class="result-data-section" id="result-data-section">
                        <div id="html-2-pdfwrapper" class="col-sm-12" style="margin:0px auto;">
                            <div class="page-header" id="brand-head" style="border-bottom:2px double #666; text-align: center;">
                                <h3 style="margin:4px 10px">
                                  {{ $unit[$info['as_unit_id']]['hr_unit_name']??'' }}
                                </h3>
                                <h5 style="margin:4px 10px">Job Card Report</h5>
                                <h5 style="margin:4px 10px">For the month of {{date('F, Y', strtotime($info['yearMonth']))}}</h5>
                            </div>
                            <table class="table" style="width:100%;border:1px solid #ccc;margin-bottom:0;padding:10px 0px;font-size:14px;text-align:left" cellpadding="5">
                                <tr>
                                    <th style="width:40%">
                                       <p style="margin-left: 10px;">ID  # {{ $associate }}</p>
                                       <p style="margin-left: 10px;">Name : {{ $info['as_name'] }}</p>
                                       <p style="margin-left: 10px;">DOJ : {{ date("d-m-Y", strtotime($info['as_doj'])) }}</p>
                                    </th>
                                    <th>
                                        <p>Oracle ID : {{ $info['as_oracle_code'] }} </p>
                                        <p>Section : {{ $section[$info['as_section_id']]['hr_section_name']??'' }} </p>
                                        <p>Designation : {{ $designation[$info['as_designation_id']]['hr_designation_name']??'' }} </p>
                                    </th>
                                    <th>
                                       <p>Total Present : {{ $info['totalPresent'] }} </p>
                                       <p>Total Absent : {{ $info['totalAbsent'] }}</p>
                                       @if($info['as_ot'] == 1)
                                       <p>Total OT :  {{ numberToTimeClockFormat($info['otHour']) }}</p>
                                       @endif
                                    </th>
                                </tr>
                            </table>

                            <table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;font-size:13px;display: block;overflow-x: auto;white-space: nowrap;" cellpadding="2" cellspacing="0" border="1" align="center">
                                <thead>
                                    <tr>
                                        <th width="10%">Date</th>
                                        <th width="20%">Attendance Status</th>
                                        <th width="10%">Floor</th>
                                        <th width="10%">Line</th>
                                        <th width="10%">In Time</th>
                                        <th width="10%">Out Time</th>
                                        <th width="10%">OT Hour</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($info['totalDay'] > 0)

                                        @for($i=$start; $i<= $end; $i++)
                                        @php    
                                            $date = date('Y-m-d', strtotime($yearMonthD.$i));
                                            $attStatus = 'Absent';
                                            $flag = 0;
                                            $bgColor = 'bg-danger';
                                            if(array_key_exists($date, $leaveDate)){
                                                $attStatus = $leaveDate[$date];
                                                $bgColor = 'bg-primary';
                                            }elseif(array_key_exists($date, $holidayDate)){
                                                $attStatus = $holidayDate[$date] == null?'Weekend':$holidayDate[$date];
                                                $bgColor = 'bg-warning';
                                            }elseif(array_key_exists($date, $outsideDate)){
                                                $attStatus = $outsideDate[$date] == 'WFHOME'?'Work From Home':$outsideDate[$date];
                                                $bgColor = 'bg-success';
                                            }elseif(array_key_exists($date, $presentDate)){
                                                $attStatus = "Present";
                                                $bgColor = 'bg-default';
                                                $flag = 1;
                                            }
                                        @endphp
                                        @isset($specialAttDate[$date])

                                        @php $att = $specialAttDate[$date];  @endphp
                                        <tr>
                                            <td>{{ $yearMonth }}-<strong>{{ sprintf("%02d", $i) }}</strong></td>
                                            <td class="bg-dark text-white">Present</td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                @if($specialAttDate[$date]->in_time != '')
                                                {{ date('H:i', strtotime($specialAttDate[$date]->in_time))}}
                                                @endif
                                            </td>
                                            <td>
                                                @if($specialAttDate[$date]->out_time != '')
                                                {{ date('H:i', strtotime($specialAttDate[$date]->out_time))}}
                                                @endif
                                            </td>
                                            <td>
                                                {{numberToTimeClockFormat($specialAttDate[$date]->ot_hour)}}
                                            </td>
                                        </tr>
                                        @endisset
                                        <tr>
                                            <td>
                                                {{ $yearMonth }}-<strong>{{ sprintf("%02d", $i) }}</strong>
                                            </td>
                                            <td class="{{ $bgColor }} @if($bgColor != 'bg-default') text-white @endif">
                                                @if($info['as_status'] != 1 && $info['as_status'] != 6 && $date == $info['lastDayMonth'] && date('Y-m', strtotime($info['as_status_date'])) == $info['yearMonth'])
                                                <span class="label label-danger">{{ emp_status_name($info['as_status']) }}</span>
                                                @else
                                                {{ $attStatus }}
                                                @endif
                                                
                                                @if($flag == 1 && $presentDate[$date]->late_status==1)
                                                    <span style="height: auto;float:right;" class="label label-warning pull-right">Late</span>
                                                @endif
                                                @if($flag == 1 && $presentDate[$date]->remarks== 'HD')
                                                    <span style="height: auto;float:right;" class="label label-danger pull-right">Half Day @if($presentDate[$date]->late_status==1) , @endif</span>
                                                @endif
                                                
                                                @if($date == $info['firstDayMonth'] && date('Y-m', strtotime($info['as_doj'])) == $info['yearMonth'])
                                                <span style="height: auto;float:right;" class="label label-danger pull-right">Joining</span>
                                                @endif
                                                @if(isset($otPresentDate[$date]))
                                                <span style="height: auto;float:right;" class="label label-warning pull-right">OT</span>
                                                @endif
                                            </td> 

                                            @if($flag == 1)
                                            @php
                                            if($presentDate[$date]->line_id != null){
                                                $lineId = $presentDate[$date]->line_id??'';
                                                $floorId = $line[$lineId]['hr_line_floor_id']??'';
                                            }else{
                                                $lineId = $info['as_line_id']??'';
                                                $floorId = $info['as_floor_id']??'';
                                            }
                                            @endphp
                                            <td>
                                                {{ $floor[$floorId]['hr_floor_name']??'' }}
                                            </td>
                                            <td>
                                                {{ $line[$lineId]['hr_line_name']??'' }}
                                            </td>
                                            @else
                                            <td></td>
                                            <td></td>
                                            @endif
                                            <td>
                                                @if($flag == 1 && $presentDate[$date]->in_time != '' && $presentDate[$date]->remarks != 'DSI')
                                                {{ date('H:i', strtotime($presentDate[$date]->in_time))}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($flag == 1 && $presentDate[$date]->out_time != '')
                                                {{ date('H:i', strtotime($presentDate[$date]->out_time))}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if($flag == 1 && $info['as_ot'] == 1)
                                                {{ numberToTimeClockFormat($presentDate[$date]->ot_hour) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @endfor
                                    @else
                                    <tr>
                                        <td colspan="7" class="text-center">No Record Found!</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot style="border-top:2px double #999">
                                    <tr>
                                        <th style="text-align:right">Total present</th>
                                        <th>{{ $info['totalPresent'] }}</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align:right">Total Over Time</th>
                                        <th>
                                            @if($info['as_ot'] == 1)
                                                {{ numberToTimeClockFormat($info['otHour']) }}
                                            @else
                                                -
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