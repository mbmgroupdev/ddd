<div id="PrintArea" >
    <p>Run Time:&nbsp;<?php echo date('l\&\\n\b\s\p\;F \&\\n\b\s\p\;d \&\\n\b\s\p\;Y \&\\n\b\s\p\;h:m a'); ?></p>
    <h2 class="center">{{$unit->hr_unit_name}}</h2>
    <h4 class="center">Attendance Summary Report: {{$date}}</h4>
    <table class=" table-bordered" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <th width="25%" style="text-align: left; padding: 5px;"> Summary : </th>
            <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Employee</td>
            <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Present</td>
            <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Absent</td>
            <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Leave</td>
            <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Holiday</td>

        </tr>
    

        <tr>
            
            <th width="25%" style="text-align: left; padding: 5px;">NON OT Employee : </th>
            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($ot['total'])}}</td>
            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($ot['present'])}}</td>
            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($ot['absent'])}}</td>
            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($ot['leave'])}}</td>
            <td width="15%" style="text-align: center; padding: 5px;" ></td>

        </tr>
        <tr>
            <th width="25%" style="text-align: left; padding: 5px;">OT Employee : </th>
            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($nonot['total'])}}</td>
            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($nonot['present'])}}</td>
            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($nonot['absent'])}}</td>
            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($nonot['leave'])}}</td>
            <td width="15%" style="text-align: center; padding: 5px;" ></td>

        </tr>
        <tr>
            <th bgcolor="#C2C2C2" width="25%" style="text-align: left; padding: 5px;">Total:</th>
            <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;"  >{{array_sum($ot['total'])+array_sum($nonot['total'])}}</td>
            <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;"  >{{array_sum($ot['present'])+array_sum($nonot['present'])}}</td>
            <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;"  >{{array_sum($ot['absent'])+array_sum($nonot['absent'])}}</td>
            <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;"  >{{array_sum($ot['leave'])+array_sum($nonot['leave'])}}</td>
            <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;"  ></td>

        </tr>
    </table>
    <div style="margin: 10px;"></div>
    <h3 style="margin-top: 20px; margin-bottom: 20px;">OT Holder List:</h3>
    <div class=" non_ot_holder_list">
        <table cellpadding="0" cellspacing="0" border="1" width="100%">
            <thead>
                <tr class="alert-info tbl-header">
                    <th style="text-align: center; padding: 10px;">Sl</th>
                    <th style="text-align: center; padding: 10px;">Area</th>
                    <th style="text-align: center; padding: 10px;">Section</th>
                    <th style="text-align: center; padding: 10px;">Sub Section</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Enroll</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Present</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Absent</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Leave</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Holiday</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Absent%</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $t_present = 0;
                    $t_absent = 0;
                    $t_leave = 0;
                    $t_holiday = 0; 
                    $t_total = 0; 
                    $count = 1;
                @endphp
                @foreach($area AS $key => $ar)
                    @foreach($ar->section AS $key1 => $section)
                        @foreach($section->subsection AS $key1 => $subsec)
                            @php 
                                $present = (int) ($ot['present'][$subsec->hr_subsec_id]??0);
                                $leave = (int) ($ot['leave'][$subsec->hr_subsec_id]??0);
                                $absent = (int) ($ot['absent'][$subsec->hr_subsec_id]??0);
                                $recorded = $present+$leave+$absent;
                                $holiday = (($ot['total'][$subsec->hr_subsec_id]??0)-$recorded) ;

                                

                                if($present == 0 && $leave == 0 && $absent == 0){
                                    $holiday = 0;
                                }

                                

                                
                            @endphp
                            @if(isset($ot['total'][$subsec->hr_subsec_id]) && $ot['total'][$subsec->hr_subsec_id] > 0)

                            <tr>
                                <th style="text-align: center; padding: 10px;">{{$count}}</th>
                                <th style="text-align: center; padding: 10px;">{{$ar->hr_area_name}}</th>
                                <th style="text-align: center; padding: 10px;">{{$section->hr_section_name}}</th>
                                <th style="text-align: center; padding: 10px;">{{$subsec->hr_subsec_name}}</th>
                                <th width="7%" style="text-align: center; padding: 10px;">{{$ot['total'][$subsec->hr_subsec_id]??0}}</th>
                                <th width="7%" style="text-align: center; padding: 10px;">{{$ot['present'][$subsec->hr_subsec_id]??0}}</th>
                                <th width="7%" style="text-align: center; padding: 10px;">{{$ot['absent'][$subsec->hr_subsec_id]??0}}</th>
                                <th width="7%" style="text-align: center; padding: 10px;">{{$ot['leave'][$subsec->hr_subsec_id]??0}}</th>
                                
                                <th width="7%" style="text-align: center; padding: 10px;">
                                    {{$holiday}}</th>
                                @php 
                                    $total = (int) ($ot['total'][$subsec->hr_subsec_id]);
                                    
                                    $count++;
                                    $t_total += $total;
                                    $t_present += $present;
                                    $t_holiday += $holiday;
                                    $t_leave += $leave;
                                    $t_absent += $absent;
                                    $t_present += $present;
                                    $percent = round(((int) ($ot['absent'][$subsec->hr_subsec_id]??0)/$total)*100);
                                    
                                    if($percent > 50){
                                        $style = 'background-color:#dc3545';
                                    }else if($percent > 0 && $percent <= 50){
                                        $style = 'background-color:#d65c14';
                                    }else{
                                        $style = 'background-color:#28a745';
                                    }

                                @endphp
                                <th width="7%" style="text-align: center; padding: 5px;{{$style}};color:#fff;">
                                        <span style="font-size:14px;">{{$percent}}</span>%
                                </th>
                                
                            </tr>
                            @endif
                        @endforeach
                    @endforeach
                @endforeach
                <tr class="label-info grand_total">
                    <td colspan="4" style="padding:5px;">Grand Total: </td>
                    <td style="text-align: right; padding: 5px;" id="grand_e"> {{$t_total}} </td>
                    <td style="text-align: right; padding: 5px;" id="grand_p"> {{$t_present}}</td>
                    <td style="text-align: right; padding: 5px;" id="grand_a"> {{$t_absent}}</td>
                    <td style="text-align: right; padding: 5px;" id="grand_l"> {{$t_leave}}</td>
                    <td style="text-align: right; padding: 5px;" id="grand_h"> {{$t_holiday}}</td>
                    <td style="text-align: right; padding: 5px;"></td>
                    
                </tr>
             
            </tbody>
        </table>
    </div>
    <div style="margin: 10px;"></div>
    <h3 style="margin-top: 20px; margin-bottom: 20px;">Non-OT Holder List:</h3>
    <div class="non_ot_holder_list" >
        <table cellpadding="0" cellspacing="0" border="1" width="100%">
            <thead>
                <tr class="alert-info tbl-header">
                    <th style="text-align: center; padding: 10px;">Sl</th>
                    <th style="text-align: center; padding: 10px;">Area</th>
                    <th style="text-align: center; padding: 10px;">Section</th>
                    <th style="text-align: center; padding: 10px;">Sub Section</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Enroll</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Present</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Absent</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Leave</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Holiday</th>
                    <th width="7%" style="text-align: center; padding: 10px;">Absent%</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $t_present = 0;
                    $t_absent = 0;
                    $t_leave = 0;
                    $t_holiday = 0; 
                    $t_total = 0; 
                    $count = 1;
                @endphp
                @foreach($area AS $key => $ar)
                    @foreach($ar->section AS $key1 => $section)
                        @foreach($section->subsection AS $key1 => $subsec)
                            @php 
                                $present = (int) ($nonot['present'][$subsec->hr_subsec_id]??0);
                                $leave = (int) ($nonot['leave'][$subsec->hr_subsec_id]??0);
                                $absent = (int) ($nonot['absent'][$subsec->hr_subsec_id]??0);
                                $recorded = $present+$leave+$absent;
                                $holiday = (($nonot['total'][$subsec->hr_subsec_id]??0)-$recorded) ;

                                

                                if($present == 0 && $leave == 0 && $absent == 0){
                                    $holiday = 0;
                                }

                                

                                
                            @endphp
                            @if(isset($nonot['total'][$subsec->hr_subsec_id]) && $nonot['total'][$subsec->hr_subsec_id] > 0)

                            <tr>
                                <th style="text-align: center; padding: 10px;">{{$count}}</th>
                                <th style="text-align: center; padding: 10px;">{{$ar->hr_area_name}}</th>
                                <th style="text-align: center; padding: 10px;">{{$section->hr_section_name}}</th>
                                <th style="text-align: center; padding: 10px;">{{$subsec->hr_subsec_name}}</th>
                                <th width="7%" style="text-align: center; padding: 10px;">{{$nonot['total'][$subsec->hr_subsec_id]??0}}</th>
                                <th width="7%" style="text-align: center; padding: 10px;">{{$nonot['present'][$subsec->hr_subsec_id]??0}}</th>
                                <th width="7%" style="text-align: center; padding: 10px;">{{$nonot['absent'][$subsec->hr_subsec_id]??0}}</th>
                                <th width="7%" style="text-align: center; padding: 10px;">{{$nonot['leave'][$subsec->hr_subsec_id]??0}}</th>
                                
                                <th width="7%" style="text-align: center; padding: 10px;">
                                    {{$holiday}}</th>
                                @php 
                                    $total = (int) ($nonot['total'][$subsec->hr_subsec_id]);
                                    
                                    $count++;
                                    $t_total += $total;
                                    $t_present += $present;
                                    $t_holiday += $holiday;
                                    $t_leave += $leave;
                                    $t_absent += $absent;
                                    $t_present += $present;
                                    $percent = round(((int) ($nonot['absent'][$subsec->hr_subsec_id]??0)/$total)*100);
                                    
                                    if($percent > 50){
                                        $style = 'background-color:#dc3545';
                                    }else if($percent > 0 && $percent <= 50){
                                        $style = 'background-color:#d65c14';
                                    }else{
                                        $style = 'background-color:#28a745';
                                    }

                                @endphp
                                <th width="7%" style="text-align: center; padding: 5px;{{$style}};color:#fff;">
                                        <span style="font-size:14px;">{{$percent}}</span>%
                                </th>
                                
                            </tr>
                            @endif
                        @endforeach
                    @endforeach
                @endforeach
                <tr class="label-info grand_total">
                    <td colspan="4" style="padding:5px;">Grand Total: </td>
                    <td style="text-align: right; padding: 5px;" id="grand_e"> {{$t_total}} </td>
                    <td style="text-align: right; padding: 5px;" id="grand_p"> {{$t_present}}</td>
                    <td style="text-align: right; padding: 5px;" id="grand_a"> {{$t_absent}}</td>
                    <td style="text-align: right; padding: 5px;" id="grand_l"> {{$t_leave}}</td>
                    <td style="text-align: right; padding: 5px;" id="grand_h"> {{$t_holiday}}</td>
                    <td style="text-align: right; padding: 5px;"></td>
                    
                </tr>
             
            </tbody>
        </table>
    </div>
</div>