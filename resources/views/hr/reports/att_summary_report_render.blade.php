<div id="PrintArea" >
    <div class="panel">
        <div class="panel-body">
            <p>Run Time:&nbsp;<?php echo date('l\&\\n\b\s\p\;F \&\\n\b\s\p\;d \&\\n\b\s\p\;Y \&\\n\b\s\p\;h:m a'); ?></p>
            <h2 style="text-align: center;">Attendance Summary Report</h2>
            <h4 style="text-align: center;">Unit: {{$unit->hr_unit_name}}</h4>
            <p style="text-align: center;">Date: {{$date}}</p>
            <br>
            <div style="display:flex;width: 100%;">
                <div style="width:33%;display: inline-block;float:left;">
                    {{-- <table > --}}
                        {{-- <tr>
                           <td style="text-align: right; padding: 5px;">MMR</td>
                           <td>&nbsp;&nbsp;=&nbsp;&nbsp;</td>
                           <td>
                               <p style="border-bottom: 1px solid; padding-bottom: 0px; margin-bottom: 0px; text-align: center;">P. NON OT + P. OT Holder</p>
                               <p style="margin-top: 0px; padding-top: 0px; text-align: center;"> Sewing Opr + Fin Opr</p>
                           </td>
                        </tr>
                        <tr>
                           <td></td>
                           <td>&nbsp;&nbsp;=&nbsp;&nbsp;</td>
                            <td>
                                <p style="border-bottom: 1px solid; padding-bottom: 0px; margin-bottom: 0px; text-align: center;">
                                    <span id="p_ot_n">{{array_sum($nonot['present'])}}</span> + <font id="p_ot">{{array_sum($ot['present'])}}</font>

                                  <!--   <input type="text" id="p_ot_n2" name=""> -->
                                </p>
                               <p style="margin-top: 0px; padding-top: 0px; text-align: center;">
                                   <font id="sw_opr">{{((int) ($ot['total'][138]??0)+(int) ($nonot['total'][138]??0))}}</font> + <font id="fin_opr">{{((int) ($ot['total'][84]??0)+(int) ($nonot['total'][84]??0))}}</font>
                            </p>
                            </td>
                        </tr> --}}
                        @php
                            $tsw = (int) ((int) ($nonot['total'][138]??0)+(int) ($ot['total'][138]??0)+(int) ($nonot['total'][48]??0)+(int) ($ot['total'][48]??0));

                            if($tsw > 0){

                            }else{
                                $tsw = 1;
                            }
                            $mmr = round((array_sum($nonot['present'])+array_sum($ot['present']))/$tsw,2);
                        @endphp
                        {{-- <tr>
                           <td></td>
                           <td>&nbsp;&nbsp;=&nbsp;&nbsp;</td>
                            <td>
                                <font id="mmr_result">{{$mmr??0}}</font>
                            </td>
                        </tr>
                    </table> --}}
                    <h3 class="mb-1 " style="margin: 20px 0;font-size: 14px;font-weight: bold;border-left: 3px solid #099dae;line-height: 18px;padding-left: 10px;">MMR</h3>
                    <div style="padding: 20px 13px;font-size: 40px;font-weight: bold;line-height: 40px;color: #099faf;">{{$mmr}}</div>
                </div>
                <div style="width:67%; display: inline-block;float:right;">
                

                    <table class="table table-bordered table-hover table-head" cellpadding="0" cellspacing="0" border='1'>
                        <tr>
                            <th width="25%" style="text-align: left; padding: 5px;"> Summary : </th>
                            <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Employee</td>
                            <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Present</td>
                            <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Absent</td>
                            <td width="15%" style="border: 1px; text-align: center; padding: 5px;">Leave</td>

                        </tr>
                    

                        <tr>
                            
                            <th width="25%" style="text-align: left; padding: 5px;">OT Employee : </th>
                            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($ot['total'])}}</td>
                            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($ot['present'])}}</td>
                            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($ot['absent'])}}</td>
                            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($ot['leave'])}}</td>

                        </tr>
                        <tr>
                            <th width="25%" style="text-align: left; padding: 5px;">Non OT Employee : </th>
                            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($nonot['total'])}}</td>
                            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($nonot['present'])}}</td>
                            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($nonot['absent'])}}</td>
                            <td width="15%" style="text-align: center; padding: 5px;" >{{array_sum($nonot['leave'])}}</td>

                        </tr>
                        <tr>
                            <th bgcolor="#C2C2C2" width="25%" style="text-align: left; padding: 5px;">Total:</th>
                            <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;"  >{{array_sum($ot['total'])+array_sum($nonot['total'])}}</td>
                            <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;"  >{{array_sum($ot['present'])+array_sum($nonot['present'])}}</td>
                            <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;"  >{{array_sum($ot['absent'])+array_sum($nonot['absent'])}}</td>
                            <td bgcolor="#C2C2C2" width="15%" style="text-align: center; padding: 5px;"  >{{array_sum($ot['leave'])+array_sum($nonot['leave'])}}</td>

                        </tr>
                    </table>
                </div>
            </div>
            <br>
            <div style="margin: 10px 0;">
                <h3 style="margin: 10px 0;font-size: 14px;
    font-weight: bold;border-left: 3px solid #099dae;line-height: 18px;padding-left: 10px;">OT Holder List:</h3>
            </div>
            <div class=" non_ot_holder_list">
                <table class="table table-bordered table-head" cellpadding="0" cellspacing="0" border="1" width="100%">
                    <thead>
                        <tr class="alert-info tbl-header">
                            <th style="text-align: center; padding: 10px;">Sl</th>
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
                            <td colspan="8" style="padding:5px;text-align: left;background: #dcf3f2;">Area: <strong>{{$ar->hr_area_name}}</strong></td>
                            
                        </tr>
                            @foreach($ar->section AS $key1 => $section)
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
                                        <td style="text-align: center; padding: 10px;">{{$count}}</td>
                                        <td  style="text-align: center; padding: 10px; @if($sec != 1) border:none!important; @else border-bottom:none!important; @endif">
                                            @if($sec == 1)
                                            {{$section->hr_section_name}}
                                            @endif
                                        </td>
                                        <td style="text-align: center; padding: 10px;">{{$subsec->hr_subsec_name}}</td>
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
                                    <td  colspan="2" style="background: #fff;"></td>
                                    <td  style="padding:5px;"></td>
                                    <td style=" text-align: center; padding: 5px;" id="grand_e"> {{$s_total}} </td>
                                    <td style="text-align: center; padding: 5px;" id="grand_p"> {{$s_present}}</td>
                                    <td style="text-align: center; padding: 5px;" id="grand_a"> {{$s_absent}}</td>
                                    <td style="text-align: center; padding: 5px;" id="grand_l"> {{$s_leave}}</td>
                                    <td style=" padding: 5px;"></td>
                                    
                                </tr>
                                @endif
                            @endforeach
                        @endforeach
                        <tr class="label-info grand-total">
                            <td colspan="3" style="padding:5px;">Grand Total: </td>
                            <td style="text-align: center; padding: 5px;" id="grand_e"> {{$t_total}} </td>
                            <td style="text-align: center; padding: 5px;" id="grand_p"> {{$t_present}}</td>
                            <td style="text-align: center; padding: 5px;" id="grand_a"> {{$t_absent}}</td>
                            <td style="text-align: center; padding: 5px;" id="grand_l"> {{$t_leave}}</td>
                            <td style="text-align: center; padding: 5px;"></td>
                            
                        </tr>
                     
                    </tbody>
                </table>
            </div>
            <div style="margin: 10px;"></div>
            <h3 style="margin: 10px 0;font-size: 14px;font-weight: bold;border-left: 3px solid #099dae;line-height: 18px;padding-left: 10px;">Non-OT Holder List:</h3>
            <div class="non_ot_holder_list" >
                <table class="table table-bordered table-head" cellpadding="0" cellspacing="0" border="1" width="100%">
                    <thead>
                        <tr class="alert-info tbl-header">
                            <th style="text-align: center; padding: 10px;">Sl</th>
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
                            <td colspan="8" style="padding:5px;text-align: left;background: #dcf3f2;">Area: <strong>{{$ar->hr_area_name}}</strong></td>
                            
                        </tr>
                            @foreach($ar->section AS $key1 => $section)
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
                                        <td style="text-align: center; padding: 10px;">{{$count}}</td>
                                        <td style="text-align: center; padding: 10px; @if($sec != 1) border:none!important; @else border-bottom:none!important; @endif">
                                            @if($sec == 1)
                                            {{$section->hr_section_name}}
                                            @endif
                                        </td>
                                        <td style="text-align: center; padding: 10px;">{{$subsec->hr_subsec_name}}</td>
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
                                    <td  colspan="2" style="background: #fff;"></td>
                                    <td  style="text-align: center;padding:5px;"></td>
                                    <td style="text-align: center; padding: 5px;" id="grand_e"> {{$s_total}} </td>
                                    <td style="text-align: center; padding: 5px;" id="grand_p"> {{$s_present}}</td>
                                    <td style=" text-align: center;padding: 5px;" id="grand_a"> {{$s_absent}}</td>
                                    <td style="text-align: center; padding: 5px;" id="grand_l"> {{$s_leave}}</td>
                                    <td style=" padding: 5px;"></td>
                                    
                                </tr>
                                @endif
                            @endforeach
                        @endforeach
                        <tr class="label-info grand-total">
                            <td colspan="3" style="padding:5px;">Grand Total: </td>
                            <td style="text-align: center; padding: 5px;" id="grand_e"> {{$t_total}} </td>
                            <td style="text-align: center; padding: 5px;" id="grand_p"> {{$t_present}}</td>
                            <td style="text-align: center; padding: 5px;" id="grand_a"> {{$t_absent}}</td>
                            <td style="text-align: center; padding: 5px;" id="grand_l"> {{$t_leave}}</td>
                            <td style=" padding: 5px;"></td>
                            
                        </tr>
                     
                    </tbody>
                </table>
            </div> 
        </div>
    </div>
</div>