<div id="PrintArea" >
    <div class="panel">
        <div class="panel-body">
            <p>Run Time:&nbsp;<?php echo date('l\&\\n\b\s\p\;F \&\\n\b\s\p\;d \&\\n\b\s\p\;Y \&\\n\b\s\p\;h:m a'); ?></p>
            <h2 style="text-align: center;">Attendance Summary Report</h2>
            <h4 style="text-align: center;">Unit: {{$unit->hr_unit_name}}</h4>
            <p style="text-align: center;">Date: {{$date}}</p>
            <br>
            <style type="text/css" media="print">
                h2{
                    font-size: 16px;
                }
                a{
                    text-decoration: none !important;
                    color: #000;
                }
                h4{
                    font-size: 14px;
                }
                table{
                    border-collapse: collapse;
                }
                table td{
                    font-size: 11px;
                    padding: 1px 0 !important;
                    border-color:#777 !important;
                }
                .table{
                    width:100%;
                }
                .px-1{
                    margin: 0 !important;
                }
                .pxx-2{
                    padding: 2px !important;
                    text-align: center;
                }
                .tbl-header th {
                    font-size: 12px;
                }
                .pagebreak { page-break-before: always; }
            </style>

            <div style="display:flex;width: 100%;">
                <div style="width:25%;display: inline-block;float:left;">
                        @php
                            $tsw = (int) ((int) ($nonot['present'][138]??0)+(int) ($ot['present'][138]??0)+(int) ($nonot['present'][48]??0)+(int) ($ot['present'][48]??0));

                            if($tsw > 0){

                            }else{
                                $tsw = 1;
                            }
                            $mmr = round((array_sum($nonot['present'])+array_sum($ot['present']))/$tsw,2);
                            //dd(array_sum($nonot['present']), array_sum($ot['present']), $tsw);
                        @endphp
                        {{-- <tr>
                           <td></td>
                           <td>&nbsp;&nbsp;=&nbsp;&nbsp;</td>
                            <td>
                                <font id="mmr_result">{{$mmr??0}}</font>
                            </td>
                        </tr>
                    </table> --}}
                    <h3 class="mb-1 px-1" style="margin: 20px 0;font-size: 12px;font-weight: bold;border-left: 3px solid #099dae;line-height: 12px;padding-left: 10px;">MMR</h3>
                    <div style="padding: 20px 13px;font-size: 40px;font-weight: bold;line-height: 40px;color: #099faf;">{{$mmr}}</div>
                </div>
                <div style="flex-grow: 1;">
                    
                    <table class="table table-bordered table-hover table-head" cellpadding="0" cellspacing="0" border='1'>
                        <tr>
                            <th width="25%" style="text-align: left; padding: " class="pxx-2"> Summary : </th>
                            <td width="15%" style="border: 1px; text-align: center; padding: " class="pxx-2">Employee</td>
                            <td width="15%" style="border: 1px; text-align: center; padding: " class="pxx-2">Present</td>
                            <td width="15%" style="border: 1px; text-align: center; padding: " class="pxx-2">Absent</td>
                            <td width="15%" style="border: 1px; text-align: center; padding: " class="pxx-2">Leave</td>
                            <td width="15%" style="border: 1px; text-align: center; padding: " class="pxx-2">Day Off</td>

                        </tr>
                    

                        <tr>
                            
                            <th width="25%" style="text-align: left;">OT : </th>
                            <td width="15%" style="text-align: center;" >{{array_sum($ot['total'])}}</td>
                            <td width="15%" style="text-align: center;" >{{array_sum($ot['present'])}}</td>
                            <td width="15%" style="text-align: center;" >{{array_sum($ot['absent'])}}</td>
                            <td width="15%" style="text-align: center;" >{{array_sum($ot['leave'])}}</td>
                            <td width="15%" style="text-align: center;" ><a href="{{url('hr/reports/get-att-emp?unit='.$unit->hr_unit_id.'&date='.$date.'&type=holiday&ot=1')}}">{{$ot['dayoff']??0}} </a></td>

                        </tr>
                        <tr>
                            <th width="25%" style="text-align: left;">Non OT: </th>
                            <td width="15%" style="text-align: center;" >{{array_sum($nonot['total'])}}</td>
                            <td width="15%" style="text-align: center;" >{{array_sum($nonot['present'])}}</td>
                            <td width="15%" style="text-align: center;" >{{array_sum($nonot['absent'])}}</td>
                            <td width="15%" style="text-align: center;" >{{array_sum($nonot['leave'])}}</td>
                            <td width="15%" style="text-align: center;" ><a href="{{url('hr/reports/get-att-emp?unit='.$unit->hr_unit_id.'&date='.$date.'&type=holiday&ot=0')}}">{{$nonot['dayoff']??0}}</a></td>

                        </tr>
                        <tr>
                            <th bgcolor="#C2C2C2" width="25%" style="text-align: left;">Total:</th>
                            <td bgcolor="#C2C2C2" width="15%" style="text-align: center;"  >{{array_sum($ot['total'])+array_sum($nonot['total'])}}</td>
                            <td bgcolor="#C2C2C2" width="15%" style="text-align: center;"  >{{array_sum($ot['present'])+array_sum($nonot['present'])}}</td>
                            <td bgcolor="#C2C2C2" width="15%" style="text-align: center;"  >{{array_sum($ot['absent'])+array_sum($nonot['absent'])}}</td>
                            <td bgcolor="#C2C2C2" width="15%" style="text-align: center;"  >{{array_sum($ot['leave'])+array_sum($nonot['leave'])}}</td>
                            <td bgcolor="#C2C2C2" width="15%" style="text-align: center;"  ><a href="{{url('hr/reports/get-att-emp?unit='.$unit->hr_unit_id.'&date='.$date.'&type=holiday')}}">{{($ot['dayoff']??0)+($nonot['dayoff'])}}</a></td>

                        </tr>
                    </table>
                </div>
            </div>
            <br>
            <div style="margin: 10px 0;">
                <h3 style="margin: 10px 0;font-size: 12px;font-weight: bold;border-left: 3px solid #099dae;line-height: 12px;padding-left: 10px;">OT Holder List:</h3>
            </div>
            <div class=" non_ot_holder_list">
                <table class="table table-bordered table-head" cellpadding="0" cellspacing="0" border="1" width="100%">
                    <thead>
                        <tr class="alert-info tbl-header">
                            <th colspan="3" style="text-align: center; padding: 10px;">Sl</th>
                            <th style="text-align: center; padding: 10px;">Section</th>
                            <th style="text-align: center; padding: 10px;">Sub Section</th>
                            <th width="7%" style="text-align: center; padding: 10px;">Enroll</th>
                            <th width="7%" style="text-align: center; padding: 10px;">Present</th>
                            <th width="7%" style="text-align: center; padding: 10px;">Absent</th>
                            <th width="7%" style="text-align: center; padding: 10px;">Leave</th>
                            <th width="7%" style="text-align: center; padding: 10px;">Absent%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $t_present = 0;
                            $t_absent = 0;
                            $t_leave = 0;
                            $t_total = 0; 
                            $count = 1;
                        @endphp
                        @foreach($area AS $key => $ar)
                        <tr class="">
                            <td colspan="10" style="padding:5px;text-align: left;background: #dcf3f2;">Area: <strong>{{$ar->hr_area_name}}</strong></td>
                            
                        </tr>
                            @foreach($ar->department AS $k => $dept)
                                <tr id="dept_{{$dept->hr_department_id}}_{{$ar->hr_area_id}}" class="">

                                    <td style="border:none; width:30px;"></td>
                                    <td colspan="9" style="padding:5px;text-align: left;background: #dcf3f2;">Department: <strong>{{$dept->hr_department_name}}</strong></td>
                                    
                                </tr>
                                @php $deptcount = 0; @endphp
                                @foreach($dept->section AS $key1 => $section)
                                    @php
                                        $s_present = 0;
                                        $s_absent = 0;
                                        $s_leave = 0;
                                        $s_total = 0; 
                                        $s_count = 1;

                                        $sec = 0;
                                       
                                    @endphp
                                    @foreach($section->subsection AS $key1 => $subsec)
                                        @php 
                                           
                                            $present = (int) ($ot['present'][$subsec->hr_subsec_id]??0);
                                            $leave = (int) ($ot['leave'][$subsec->hr_subsec_id]??0);
                                            $absent = (int) ($ot['absent'][$subsec->hr_subsec_id]??0);


                                           
                                        @endphp
                                        @if(isset($ot['total'][$subsec->hr_subsec_id]) && $ot['total'][$subsec->hr_subsec_id] > 0)
                                            @php  $sec ++; @endphp
                                        <tr>
                                            <td style="border:none!important"></td>
                                            <td style="border:none!important;border-left:1px solid #dee2e6 !important;width:30px;"></td>
                                            <td  style="text-align: center; padding: 10px;">{{$count}}</td>
                                            <td  style="text-align: center; padding: 10px; @if($sec != 1) border:none!important; @else border-bottom:none!important; @endif">
                                                @if($sec == 1)
                                                <a href="{{url('hr/reports/get-att-emp?unit='.$unit->hr_unit_id.'&date='.$date.'&section='.$section->hr_section_id.'&ot=1')}}">{{$section->hr_section_name}}</a>
                                                @endif
                                            </td>
                                            <td style="text-align: center; padding: 10px;">
                                                <a href="{{url('hr/reports/get-att-emp?unit='.$unit->hr_unit_id.'&date='.$date.'&subsection='.$subsec->hr_subsec_id.'&ot=1')}}">{{$subsec->hr_subsec_name}} </a></td>
                                            <td width="7%" style="text-align: center; padding: 10px;">{{$ot['total'][$subsec->hr_subsec_id]??0}}</td>
                                            <td width="7%" style="text-align: center; padding: 10px;">{{$ot['present'][$subsec->hr_subsec_id]??0}}</td>
                                            <td width="7%" style="text-align: center; padding: 10px;">{{$ot['absent'][$subsec->hr_subsec_id]??0}}</td>
                                            <td width="7%" style="text-align: center; padding: 10px;">{{$ot['leave'][$subsec->hr_subsec_id]??0}}</td>
                                            
                                            
                                            @php 
                                                $total = (int) ($ot['total'][$subsec->hr_subsec_id]);
                                                
                                                $count++;
                                                $t_total += $total;
                                                $t_present += $present;
                                                $t_leave += $leave;
                                                $t_absent += $absent;

                                                $deptcount += $total;
                                                $s_total += $total;
                                                $s_present += $present;
                                                $s_leave += $leave;
                                                $s_absent += $absent;
                                                $percent = round(((int) ($ot['absent'][$subsec->hr_subsec_id]??0)/$total)*100);
                                                
                                                if($percent > 50){
                                                    $style = 'color:#dc3545';
                                                }else if($percent > 0 && $percent <= 50){
                                                    $style = 'color:#c0a32c';
                                                }else{
                                                    $style = 'color:#28a745';
                                                }

                                            @endphp
                                            <td width="7%" class="text-att" style="text-align:center;{{$style}};">
                                                    <span style="font-size:14px;">{{$percent}}</span>%
                                            </td>
                                            
                                        </tr>
                                        @endif
                                    @endforeach
                                    
                                    @if($s_total > 0)

                                    <tr class="grand-total" style="    background: #dadada;">
                                        <td style="border:none !important;background: #fff;"></td>
                                        <td style="border:none !important;background: #fff;border-left:1px solid #dee2e6 !important;"></td>
                                        <td colspan="3"  style="padding:5px;"></td>
                                        <td style=" text-align: center;" id="grand_e"> {{$s_total}} </td>
                                        <td style="text-align: center;" id="grand_p"> {{$s_present}}</td>
                                        <td style="text-align: center;" id="grand_a"> {{$s_absent}}</td>
                                        <td style="text-align: center;" id="grand_l"> {{$s_leave}}</td>
                                        <td style=""></td>
                                        
                                    </tr>
                                    @endif
                                @endforeach

                                @if($deptcount == 0)
                                    <style type="text/css">
                                        #{{'dept_'.$dept->hr_department_id}}_{{$ar->hr_area_id}}{display: none;}
                                    </style>
                                @endif

                            @endforeach

                        
                        @endforeach
                        @php $nullsec = $ot['total']['']??0; @endphp
                        @if($nullsec >0)
                        <tr>
                            <td colspan="2" style="border:none;"></td>
                            <td  style="text-align: center; padding: 10px;">{{($count+1)}}</td>
                            <td  style="text-align: center; padding: 10px; border:none!important; ">
                                N/A
                            </td>
                            <td style="text-align: center; padding: 10px;">N/A</td>
                            <td width="7%" style="text-align: center; padding: 10px;">{{$ot['total']['']??0}}</td>
                            <td width="7%" style="text-align: center; padding: 10px;">{{$ot['present']['']??0}}</td>
                            <td width="7%" style="text-align: center; padding: 10px;">{{$ot['absent']['']??0}}</td>
                            <td width="7%" style="text-align: center; padding: 10px;">{{$ot['leave']['']??0}}</td>
                            
                            
                            @php 
                                

                                
                                $t_total += ($ot['total']['']??0);
                                $t_present += ($ot['present']['']??0);
                                $t_leave += ($ot['leave']['']??0);
                                $t_absent += ($ot['absent']['']??0);

                            @endphp
                            <td width="7%" class="text-att" style="text-align:center;">
                            </td>
                            
                        </tr>
                        @endif
                        <tr class="label-info grand-total">
                            <td colspan="5" style="padding:5px;">Grand Total: </td>
                            <td style="text-align: center;" id="grand_e"> {{$t_total}} </td>
                            <td style="text-align: center;" id="grand_p"> {{$t_present}}</td>
                            <td style="text-align: center;" id="grand_a"> {{$t_absent}}</td>
                            <td style="text-align: center;" id="grand_l"> {{$t_leave}}</td>
                            <td style="text-align: center;"></td>
                            
                        </tr>
                     
                    </tbody>
                </table>
            </div>
            <div class="pagebreak" style="margin: 10px;"></div>
            <h3 style="margin: 10px 0;font-size: 12px;font-weight: bold;border-left: 3px solid #099dae;line-height: 12px;padding-left: 10px;">Non-OT Holder List:</h3>
                        <div class=" non_ot_holder_list">
                <table class="table table-bordered table-head" cellpadding="0" cellspacing="0" border="1" width="100%">
                    <thead>
                        <tr class="alert-info tbl-header">
                            <th colspan="3" style="text-align: center; padding: 10px;">Sl</th>
                            <th style="text-align: center; padding: 10px;">Section</th>
                            <th style="text-align: center; padding: 10px;">Sub Section</th>
                            <th width="7%" style="text-align: center; padding: 10px;">Enroll</th>
                            <th width="7%" style="text-align: center; padding: 10px;">Present</th>
                            <th width="7%" style="text-align: center; padding: 10px;">Absent</th>
                            <th width="7%" style="text-align: center; padding: 10px;">Leave</th>
                            <th width="7%" style="text-align: center; padding: 10px;">Absent%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $t_present = 0;
                            $t_absent = 0;
                            $t_leave = 0;
                            $t_total = 0; 
                            $count = 1;
                        @endphp
                        
                        @foreach($area AS $key => $ar1)
                            <tr class="">
                                <td colspan="10" style="padding:5px;text-align: left;background: #dcf3f2;">Area: <strong>{{$ar1->hr_area_name}}</strong></td>
                                
                            </tr>
                            
                            @foreach($ar1->department AS $dept1)
                                
                                <tr id="nondept_{{$dept1->hr_department_id}}_{{$ar1->hr_area_id}}" class="">
                                    <td style="border:none; width:30px;"></td>
                                    <td colspan="9" style="padding:5px;text-align: left;background: #dcf3f2;">Department: <strong>{{$dept1->hr_department_name}}</strong></td>
                                </tr>
                                <tr>
                                    @php $deptcount = 0; @endphp
                                @foreach($dept1->section AS $key1 => $section)
                                    @php
                                        $s_present = 0;
                                        $s_absent = 0;
                                        $s_leave = 0;
                                        $s_total = 0; 
                                        $s_count = 1;

                                        $sec = 0;
                                       
                                    @endphp
                                    @foreach($section->subsection AS $key1 => $subsec)
                                        @php 
                                           
                                            $present = (int) ($nonot['present'][$subsec->hr_subsec_id]??0);
                                            $leave = (int) ($nonot['leave'][$subsec->hr_subsec_id]??0);
                                            $absent = (int) ($nonot['absent'][$subsec->hr_subsec_id]??0);


                                           
                                        @endphp
                                        @if(isset($nonot['total'][$subsec->hr_subsec_id]) && $nonot['total'][$subsec->hr_subsec_id] > 0)
                                            @php  $sec ++; @endphp
                                        <tr>
                                            <td style="border:none!important"></td>
                                            <td style="border:none!important;border-left:1px solid #dee2e6 !important;width:30px;"></td>
                                            <td  style="text-align: center; padding: 10px;">{{$count}}</td>
                                            <td  style="text-align: center; padding: 10px; @if($sec != 1) border:none!important; @else border-bottom:none!important; @endif">
                                                @if($sec == 1)
                                                <a href="{{url('hr/reports/get-att-emp?unit='.$unit->hr_unit_id.'&date='.$date.'&section='.$section->hr_section_id.'&ot=0')}}">{{$section->hr_section_name}}</a>
                                                @endif
                                            </td>
                                            <td style="text-align: center; padding: 10px;">
                                                <a href="{{url('hr/reports/get-att-emp?unit='.$unit->hr_unit_id.'&date='.$date.'&subsection='.$subsec->hr_subsec_id.'&ot=0')}}">{{$subsec->hr_subsec_name}} </a></td>
                                            <td width="7%" style="text-align: center; padding: 10px;">{{$nonot['total'][$subsec->hr_subsec_id]??0}}</td>
                                            <td width="7%" style="text-align: center; padding: 10px;">{{$nonot['present'][$subsec->hr_subsec_id]??0}}</td>
                                            <td width="7%" style="text-align: center; padding: 10px;">{{$nonot['absent'][$subsec->hr_subsec_id]??0}}</td>
                                            <td width="7%" style="text-align: center; padding: 10px;">{{$nonot['leave'][$subsec->hr_subsec_id]??0}}</td>
                                            
                                            
                                            @php 
                                                $total = (int) ($nonot['total'][$subsec->hr_subsec_id]);
                                                
                                                $count++;
                                                $t_total += $total;
                                                $t_present += $present;
                                                $t_leave += $leave;
                                                $t_absent += $absent;

                                                $deptcount += $total;
                                                $s_total += $total;
                                                $s_present += $present;
                                                $s_leave += $leave;
                                                $s_absent += $absent;
                                                $percent = round(((int) ($nonot['absent'][$subsec->hr_subsec_id]??0)/$total)*100);
                                                
                                                if($percent > 50){
                                                    $style = 'color:#dc3545';
                                                }else if($percent > 0 && $percent <= 50){
                                                    $style = 'color:#c0a32c';
                                                }else{
                                                    $style = 'color:#28a745';
                                                }

                                            @endphp
                                            <td width="7%" class="text-att" style="text-align:center;{{$style}};">
                                                    <span style="font-size:14px;">{{$percent}}</span>%
                                            </td>
                                            
                                        </tr>
                                        @endif
                                    @endforeach
                                    
                                    @if($s_total > 0)

                                    <tr class="grand-total" style="    background: #dadada;">
                                        <td style="border:none !important;background: #fff;"></td>
                                        <td style="border:none !important;background: #fff;border-left:1px solid #dee2e6 !important;"></td>
                                        <td colspan="3"  style="padding:5px;"></td>
                                        <td style=" text-align: center;" id="grand_e"> {{$s_total}} </td>
                                        <td style="text-align: center;" id="grand_p"> {{$s_present}}</td>
                                        <td style="text-align: center;" id="grand_a"> {{$s_absent}}</td>
                                        <td style="text-align: center;" id="grand_l"> {{$s_leave}}</td>
                                        <td style=""></td>
                                        
                                    </tr>
                                    @endif
                                @endforeach

                                @if($deptcount == 0)
                                    <style type="text/css">
                                        #{{'nondept_'.$dept1->hr_department_id}}_{{$ar1->hr_area_id}}{display: none;}
                                    </style>
                                @endif
                                </tr>
                                

                            @endforeach

                        
                        @endforeach

                        @php 
                        $nullsec = $nonot['total']['']??0; 

                        @endphp
                        @if($nullsec >0)
                        <tr>
                            <td colspan="2" style="border:none;"></td>
                            <td  style="text-align: center; padding: 10px;">{{($count+1)}}</td>
                            <td  style="text-align: center; padding: 10px; border:none!important; ">
                                N/A
                            </td>
                            <td style="text-align: center; padding: 10px;">N/A</td>
                            <td width="7%" style="text-align: center; padding: 10px;">{{$nonot['total']['']??0}}</td>
                            <td width="7%" style="text-align: center; padding: 10px;">{{$nonot['present']['']??0}}</td>
                            <td width="7%" style="text-align: center; padding: 10px;">{{$nonot['absent']['']??0}}</td>
                            <td width="7%" style="text-align: center; padding: 10px;">{{$nonot['leave']['']??0}}</td>
                            
                            
                            @php 
                                

                                
                                $t_total += ($nonot['total']['']??0);
                                $t_present += ($nonot['present']['']??0);
                                $t_leave += ($nonot['leave']['']??0);
                                $t_absent += ($nonot['absent']['']??0);

                            @endphp
                            <td width="7%" class="text-att" style="text-align:center;">
                            </td>
                            
                        </tr>
                        @endif
                        <tr class="label-info grand-total">
                            <td colspan="5" style="padding:5px;">Grand Total: </td>
                            <td style="text-align: center;" id="grand_e"> {{$t_total}} </td>
                            <td style="text-align: center;" id="grand_p"> {{$t_present}}</td>
                            <td style="text-align: center;" id="grand_a"> {{$t_absent}}</td>
                            <td style="text-align: center;" id="grand_l"> {{$t_leave}}</td>
                            <td style="text-align: center;"></td>
                            
                        </tr>
                     
                    </tbody>
                </table>
            </div> 
        </div>
    </div>
</div>