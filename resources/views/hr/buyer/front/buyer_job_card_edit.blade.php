<div class="panel">
    <div class="panel-body">
        <div class="row justify-content-center">
            <div class="col-sm-10">
                
                <div class="row">
                    <div class="col-sm-12" style="margin:30px auto;">
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
                                   <p style="margin:0;padding:4px 10px"><strong>Present </strong>: <b ></b> </p>
                                   <p style="margin:0;padding:4px 10px"><strong>Absent </strong>: <b ></b></p>
                                   <p style="margin:0;padding:4px 10px"><strong>Total OT </strong>: <b></b> </p>
                                </th>
                            </tr>
                        </table>

                        <table class="table" style="width:100%;border:1px solid #ccc;font-size:13px;display: block;overflow-x: auto;white-space: nowrap;"  cellpadding="2" cellspacing="0" border="1" align="center">
                            <thead>
                                <tr>
                                    <th rowspan="2" width="12%">Date</th>
                                    <th rowspan="2">Present Status</th>
                                    <th rowspan="2">Floor</th>
                                    <th rowspan="2">Line</th>
                                    <th colspan="1" class="text-center">Shift</th>
                                    <th colspan="2" class="text-center">Punch</th>
                                    <th rowspan="2">OT Hour</th>
                                </tr>
                                <tr>
                                    <th>In time - Out Time</th>
                                    <th>In Time</th>
                                    <th>Out Time</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                
                                @foreach($attdata as $key => $val)
                                    @php
                                        $shift_data = $shift[$default_shift];
                                        if(isset($val->hr_shift_code)){
                                            if(isset($shift[$val->hr_shift_code])){
                                                $shift_data = $shift[$val->hr_shift_code];
                                            }
                                        }
                                            

                                        $time = $shift_data['hr_shift_end_time'];
                                        $time2 = intdiv($shift_data['hr_shift_break_time'], 60).':'. ($shift_data['hr_shift_break_time'] % 60);

                                        $secs = strtotime($time2)-strtotime("00:00:00");
                                        $result = date("H:i",strtotime($time)+$secs);
                                    @endphp
                                    @if($val)
                                        <tr>
                                            <td>
                                                {{ $key }}
                                                @if($key == $employee->as_doj)
                                                    <span class="label label-success arrowed-right arrowed-in pull-right">Joined</span>
                                                @endif
                                                @if($key == $employee->as_status_date)
                                                    
                                                    <span class="label label-warning arrowed-right arrowed-in pull-right">
                                                        {{ emp_status_name($employee->as_status) }}
                                                    </span>
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
                                            <td></td>
                                            <td></td>
                                            <td class="text-center " style="font-weight: bold;">
                                                {{date("H:i",strtotime($shift_data['hr_shift_start_time']))}} - 
                                                {{ $result }}
                                            </td>
                                            <td style="text-align: center;">
                                                <input class="intime manual form-control" id="punchintime-" type="text" name="new_intime[]" value="@if($val->in_time) {{date('H:i:s', strtotime($val->in_time))}} @endif"  placeholder="HH:mm:ss" autocomplete="off">
                                            </td>
                                            <td style="text-align: center;">
                                                <input class="intime manual form-control" id="punchintime-" type="text" name="new_intime[]" value="@if($val->out_time) {{date('H:i:s', strtotime($val->out_time))}} @endif"  placeholder="HH:mm:ss" autocomplete="off">
                                            </td>
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
                                                    <span class="label label-warning arrowed-right arrowed-in pull-right">
                                                        {{ emp_status_name($employee->as_status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="background: #ea9d99;color:#000;">A</td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-center " style="font-weight: bold;">
                                                {{date("H:i",strtotime($shift_data['hr_shift_start_time']))}} - 
                                                {{ $result }}
                                            </td>
                                            <td style="text-align: center;">
                                                <input class="intime manual form-control" id="punchintime-" type="text" name="new_intime[]" value=""  placeholder="HH:mm:ss" autocomplete="off">
                                            </td>
                                            <td style="text-align: center;">
                                                <input class="intime manual form-control" id="punchintime-" type="text" name="new_intime[]" value=""  placeholder="HH:mm:ss" autocomplete="off">
                                            </td>
                                            <td>
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
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align:right">Total Over Time</th>
                                    <th>
                                    @if($employee->as_ot==1)


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