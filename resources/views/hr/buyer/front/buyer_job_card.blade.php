<div class="row">
    <div class="col-3">
        <div class="action-section">
            <h4 class="card-title capitalize inline">
                <button type="button" onClick="printMe1('result-data')" class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Job Card">
                   <i class="fa fa-print"></i>
                </button>
            </h4>
        </div>
    </div>
    <div class="col-6 text-center">
        <h4 class="card-title capitalize inline">
        @php
            $associate = request()->associate;
            $nextMonth = date('Y-m', strtotime($month.' +1 month'));
            $prevMonth = date('Y-m', strtotime($month.' -1 month'));

            $prevUrl = url("hrm/operation/job_card?associate=$associate&month_year=$prevMonth");
            $nextUrl = url("hrm/operation/job_card?associate=$associate&month_year=$nextMonth");
            $user  = auth()->user();
        @endphp

        @if($user->can('Attendance Operation') || $user->hasRole('Super Admin'))
        <a href="{{ $prevUrl }}" class="btn view prev_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Previous Month Job Card" >
          <i class="las la-chevron-left"></i>
        </a>
        @endif
        <b class="f-16" id="result-head">{{date('F, Y', strtotime($month))}}</b>
        @if($user->can('Attendance Operation') || $user->hasRole('Super Admin'))
        @if(strtotime(date('Y-m')) >= strtotime($nextMonth))
        <a href="{{ $nextUrl }}" class="btn view next_btn" data-toggle="tooltip" data-placement="top" title="" data-original-title="Next Month Job Card" >
          <i class="las la-chevron-right"></i>
        </a>
        @endif
        @endif
        </h4>
    </div>
        @php 

        $flagStatus = 0;
        $asStatus = emp_status_name($employee->as_status);
        $statusDate = $employee->as_status_date;
        if($statusDate != null && $employee->as_status != 1){
            $statusMonth = date('Ym', strtotime($employee->as_status_date));
            $requestMonth = date('Ym', strtotime($month));
          
            if($requestMonth > $statusMonth){
                $flagStatus = 1;
            }
        }
        @endphp
    <div class="col-3">
        @if($flagStatus == 0)
        @if($user->can('Attendance Operation') || $user->hasRole('Super Admin'))
          {{-- @if($month == date('m') ) --}}
          <div class="text-right">
            <h4 class="card-title capitalize inline">
            <a href='{{url("hrm/timeattendance/attendance_bulk_manual?associate=$employee->associate_id&month=$month")}}' class="btn view list_view no-padding" data-toggle="tooltip" data-placement="top" title="" data-original-title="Manual Edit Job Card">
              <i class="fa fa-edit bigger-120"></i>
            </a>
            </h4>
          </div>
          {{-- @endif --}}
        @endif
        @endif
    </div>
</div>
<div class="row">
    <div id="result-data" class="col-sm-12" style="margin:30px auto;">
        <div class="page-header" id="brand-head" style="border-bottom:2px double #666; text-align: center;">
            <h3 style="margin:4px 10px">{{ $unit[$employee->as_unit_id]['hr_unit_name'] }}</h3>
            <h5 style="margin:4px 10px">Job Card Report</h5>

            <h5 style="margin:4px 10px">For the month of {{date('F, Y', strtotime($month))}}</h5>
        </div>
        <table class="table" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left"  cellpadding="5">
            <tr>
                <th style="width:32%">
                   <p style="margin:0;padding:4px 10px"><strong>ID </strong> # {{ $employee->associate_id }}</p>
                   <p style="margin:0;padding:4px 10px"><strong>Name </strong>: {{ $employee->as_name }}</p>
                   <p style="margin:0;padding:4px 10px"><strong>DOJ </strong>: {{ date("d-m-Y", strtotime($employee->as_doj)) }}</p>
                </th>
                <th>
                    <p style="margin:0;padding:4px 10px"><strong>Oracle ID </strong>: {{ $employee->as_oracle_code }} </p>
                   <p style="margin:0;padding:4px 10px"><strong>Section </strong>: {{ $section[$employee->as_section_id]['hr_section_name'] }} <span> - Previous:  </span> </p>
                   <p style="margin:0;padding:4px 10px"><strong>Designation </strong>: {{ $designation[$employee->as_designation_id]['hr_designation_name'] }} <span><br> Previous:</span></p>
                </th>
                <th>
                   <p style="margin:0;padding:4px 10px"><strong>Present </strong>: <b >{{$sum['p']}}</b> </p>
                   <p style="margin:0;padding:4px 10px"><strong>Absent </strong>: <b >{{$sum['a']}}</b></p>
                   <p style="margin:0;padding:4px 10px"><strong>Total OT </strong>: <b>{{numberToTimeClockFormat($sum['ot'])}} </b> </p>
                </th>
            </tr>
        </table>

        <table class="table" style="width:100%;border:1px solid #ccc;font-size:13px;display: block;overflow-x: auto;white-space: nowrap;"  cellpadding="2" cellspacing="0" border="1" align="center">
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
                
                @foreach($attdata as $key => $val)
                @if($val)
                    <tr>
                        <td>
                            {{ $key }}
                            @if($key == $employee->as_doj)
                                <span class="label label-success arrowed-right arrowed-in pull-right">Joined</span>
                            @endif
                            @if($key == $employee->as_status_date)
                                @php
                                    $flag = '';
                                    if($employee->as_status === 0) {
                                        $flag = 'Delete';
                                    } else if($employee->as_status === 2) {
                                        $flag = 'Resign';
                                    } else if($employee->as_status === 3) {
                                        $flag = 'Terminate';
                                    } else if($employee->as_status === 4) {
                                        $flag = 'Suspend';
                                    } else if($employee->as_status === 5) {
                                        $flag = 'Left';
                                    }
                                @endphp
                                @if($flag != '')
                                <span class="label label-warning arrowed-right arrowed-in pull-right">
                                    {{ $flag }}
                                </span>
                                @endif
                            @endif
                        </td>
                        <td @if($val->att_status == 'a') style="background: #ea9d99;color:#000;" @endif>
                                @if($val->att_status == 'h')
                                    {{$val->remarks}}
                                @else
                                    <span style="text-transform: uppercase;">
                                        {{$val->att_status}}
                                    </span> 
                                    @if($val->remarks) - {{$val->remarks}} @endif
                                @endif
                        @if($val->late_status == 1)
                            <span style="height: auto;float:right;" class="label label-warning pull-right">Late</span>
                        @endif
                        </td>
                        @php

                            $line_id = $val->line_id;
                            $line_name = '';
                            $floor_name = '';
                            if($line_id != null){
                                if(isset($line[$val->line_id])){
                                    $floor_id = $line[$val->line_id]['hr_line_floor_id'];
                                    $line_name = $line[$val->line_id]['hr_line_name'];

                                }
                                if($floor_id != null){
                                    if(isset($floor[$floor_id])){
                                        $floor_name = $floor[$floor_id]['hr_floor_name'];

                                    }
                                }
                            }

                        @endphp
                        <td>{{$line_name}}</td>
                        <td>{{$floor_name}}</td>
                        <td style="text-align: center;">
                            @if($val->in_time) {{date('H:i', strtotime($val->in_time))}} @endif
                        </td>
                        <td style="text-align: center;">
                            @if($val->out_time) {{date('H:i', strtotime($val->out_time))}} @endif</td>
                        <td>
                        @if($employee->as_ot==1)
                            {{numberToTimeClockFormat($val->ot_hour)}}
                        @endif
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>
                            {{ $key }}
                            @if($key == $employee->as_doj)
                                <span class="label label-success arrowed-right arrowed-in pull-right">Joined</span>
                            @endif
                            @if($key == $employee->as_status_date)
                                @php
                                    $flag = '';
                                    if($employee->as_status === 0) {
                                        $flag = 'Delete';
                                    } else if($employee->as_status === 2) {
                                        $flag = 'Resign';
                                    } else if($employee->as_status === 3) {
                                        $flag = 'Terminate';
                                    } else if($employee->as_status === 4) {
                                        $flag = 'Suspend';
                                    } else if($employee->as_status === 5) {
                                        $flag = 'Left';
                                    }
                                @endphp
                                @if($flag != '')
                                <span class="label label-warning arrowed-right arrowed-in pull-right">
                                    {{ $flag }}
                                </span>
                                @endif
                            @endif
                        </td>
                        <td style="background: #ea9d99;color:#000;">A</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            @if($employee->as_ot==1)
                                0:00
                            @endif
                        </td>
                    </tr>

                @endif
                @endforeach
            </tbody>



            <tfoot style="border-top:2px double #999">
                <input type="hidden" id="present" value="">
                <input type="hidden" id="absent" value="">
                <tr>
                    <th style="text-align:right">Total present</th>
                    <th>{{$sum['p']}}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th style="text-align:right">Total Over Time</th>
                    <th>
                    @if($employee->as_ot==1)
                        {{numberToTimeClockFormat($sum['ot'])}}

                        <input type="hidden" id="ot" value="0">
                    @endif
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>