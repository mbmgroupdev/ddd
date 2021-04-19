@php
    date_default_timezone_set('Asia/Dhaka');
    $en = array('0','1','2','3','4','5','6','7','8','9');
    $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
    $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
@endphp
<div class="col-sm-10">
    <button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('bonus_content')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report" ><i class="las la-print"></i> </button>
</div>
<div class="col-sm-10" style="margin:20px auto;border:1px solid #ccc">
    
    <div id="bonus_content">
        <style type="text/css" media="print">
            .page-break{
                page-break-after: always;
            }
            p,td,th{ margin: 3pt 0 !important;}
        </style>
        <div class="page-header" style="text-align:left;border-bottom:2px double #666">

            <h2 style="margin:4px 10px; font-weight: bold;  text-align: center; color: #FF00FF">উৎসব বোনাস প্রদানের শীট - {{$rules->bonus_type_name}},  {{eng_to_bn(date('Y', strtotime($rules->cutoff_date )))}} </h2>
            <p style="text-align: center;">
                যোগদানের সর্বশেষ তারিখঃ {{eng_to_bn(date('d-m-Y', strtotime($eligible_date )))}} 
                &nbsp; &nbsp; &nbsp; বোনাসের তারিখঃ {{eng_to_bn(date('d-m-Y', strtotime($rules->cutoff_date )))}}
            </p>
            <p style="text-align: center;">
                    @if(!empty($input['subSection']))
                        @php $sub =  $subSection[$input['subSection']]; @endphp
                        <b>সাব-সেকশনঃ</b> {{$sub['hr_subsec_name_bn']}}, 
                        {{$section[$sub['hr_subsec_section_id']]['hr_section_name_bn']}},
                        {{$department[$sub['hr_subsec_department_id']]['hr_department_name_bn']}}
                    @elseif(!empty($input['section']))
                        @php $sub =  $section[$input['section']]; @endphp
                        <b>সেকশনঃ</b> {{$sub['hr_section_name_bn']}}, {{$department[$sub['hr_section_department_id']]['hr_department_name_bn']}}
                    @elseif(!empty($input['department']))
                        <b>ডিপার্টমেন্টঃ</b> {{$department[$input['department']]['hr_department_name_bn']}}
                    @elseif(!empty($input['area']))
                        <b>এরিয়াঃ</b> {{$area[$input['area']]['hr_area_name_bn']}}
                    @endif
               
                    
                    @if(!empty($input['employee_status']) )
                    <b>স্ট্যাটাসঃ</b> <span style="text-transform: capitalize; ">{{emp_status_name($input['employee_status'])}}</span>
                    @endif
            </p>
 
            </div>
                    
            
        </div>
        <!-- unit loop -->
        @php $pageno = 0; $sl = 0; $tEmp = 0; $tStamp = 0; $tBonus = 0;@endphp
        @foreach($bonusList as $u => $unitList)
            <!-- lcation loop -->
            @foreach($unitList as $l => $locList)
                <!-- perage 10 employee -->
                @foreach($locList as $key => $page)
                    <table width="100%">
                        <tbody>
                            <tr>
                                <td width="60%">
                                    <h5 style="margin:4px 5px; font-size: 12px; color: #FF00FF"><font style="font-weight: bold; font-size: 12px; ">ইউনিটঃ </font>
                                        {{$unit[$u]['hr_unit_name_bn']??''}}
                                    </h5>
                                    <h5 style="margin:4px 5px; font-size: 12px; color: #FF00FF"><font style="font-weight: bold; font-size: 12px; ">লোকেশনঃ 
                                        {{$location[$l]['hr_location_name']??''}}

                                    </font></h5>
                                </td>
                                <td>
                                    <h5 style="margin:4px 5px; font-size: 10px; text-align: right; color: #FF00FF"><font style="font-weight: bold;">পাতা নং # {{eng_to_bn(++$pageno)}}</font></h5>
                                    <h5 style="margin:4px 5px; font-size: 13px; text-align: right; color: #FF00FF"><font style="font-weight: bold;"></font></h5>
                                    <h5 style="margin:4px 5px; font-size: 13px; text-align: right; color: #FF00FF"><font style="font-weight: bold;">মোট দেয়ঃ 
                                        {{eng_to_bn(bn_money(collect($page)->sum('net_payable')))}}
                                    </font></h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table" style="width:100%;border:1px solid #ccc; font-size:12px !important; color: #2A86FF"  cellpadding="2" cellspacing="0" border="1" align="center"> 
                        <thead>
                            <tr style="color: #2A86FF">
                                <th>ক্রমিক নং</th>
                                <th>কর্মী/কর্মচারীদের নাম ও <br> যোগদানের তারিখ</th>
                                <th>আই.ডি নং</th>
                                <th>মাসিক বেতন/মজুরী</th>
                                <th>সর্বমোট দেয় <br>টাকার পরিমাণ</th>
                                <th>দস্তখত</th>
                            </tr> 
                        </thead>
                        <tbody>
                            <!-- excute sngle emplyee -->
                            @foreach($page as $key => $emp)
                            
                            <tr>
                                <td style="text-align: center;" width="5%">
                                    {{eng_to_bn(++$sl)}}
                                </td>
                                <td>
                                    <p >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font>{{ !empty($emp->hr_bn_associate_name)?$emp->hr_bn_associate_name:null }}</font></p>
                                    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        {{$designation[$emp->designation_id]['hr_designation_name_bn']}}
                                    </p>
                                    <p >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; যোগদানের তারিখঃ {{ !empty($emp->as_doj)?(eng_to_bn(date('d-m-Y', strtotime($emp->as_doj)))):null }}
                                    </p>

                                    <p >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp(<?php echo str_replace($en, $bn, floor($emp->duration/12))  ?> বৎসর <?php echo str_replace($en, $bn, ($emp->duration%12))  ?> মাস)</p>

                                    <p >&nbsp;&nbsp;&nbsp;&nbsp;<font></font></p>
                                </td>

                                <td style="text-align: center;">
                                    
                                    @php 
                                        $temp_bn = eng_to_bn($emp->temp_id); 
                                    @endphp
                                    {!! !empty($emp->associate_id)?(substr_replace($emp->associate_id, "<big style='font-size:16px; font-weight:bold;'>$temp_bn</big>", 3, 6)):null !!} 
                                    
                                    @if($emp->as_oracle_code)
                                        <br> পূর্বের আইডিঃ {{$emp->as_oracle_code}}
                                    @endif
                                </td>

                                <td>
                                    <p>&nbsp;&nbsp;&nbsp;
                                        
                                        {{ !empty($emp->gross_salary)?(str_replace($en, $bn,(string)number_format($emp->gross_salary,2, '.', ','))):null }}</p>
                                    <p >&nbsp;&nbsp;&nbsp;&nbsp;মূল বেতনঃ  <?php 
                                        $basic=$emp->basic;
                                         ?>
                                         {{ !empty($emp->basic)?(str_replace($en, $bn,(string)number_format($basic,2, '.', ','))):null }}</p>
                                    <p style=" font-size: 9px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;স্ট্যাম্পঃ {{eng_To_bn($emp->stamp)}}</p>
                                </td>
                                <td style="text-align: right;">
                                    <p style=" font-size: 20px; font-weight: bold;">
                                        {{ !empty($emp->net_payable)?(str_replace($en, $bn,(string)number_format($emp->net_payable,2, '.', ','))):null }}</p>
                                    <p ></p>
                                    <p style="font-size: 12px; font-weight: bold;">

                                        {{ !empty($emp->bonus_amount)?(str_replace($en, $bn,(string)number_format($emp->bonus_amount,2, '.', ','))):null }}</p>
                                </td>
                                @php $tEmp++; $tStamp += $emp->stamp; $tBonus += $emp->net_payable;@endphp
                                <td width="15%"></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="page-break"></div>
                    
                    
                @endforeach
            @endforeach
        @endforeach
        <div style="text-align:right;font-weight:bold;">
            <h2>সর্বমোট কর্মকর্তা/কর্মচারীঃ {{eng_to_bn($tEmp)}}</h2>
            <h2>স্ট্যাম্প বাবদঃ {{eng_to_bn(bn_money($tStamp))}}</h2>
            <h2>সর্বমোট বোনাসঃ {{eng_to_bn(bn_money($tBonus))}}</h2>
        </div>
    </div>
</div>