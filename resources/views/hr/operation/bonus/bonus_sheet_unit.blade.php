@php
    date_default_timezone_set('Asia/Dhaka');
    $en = array('0','1','2','3','4','5','6','7','8','9');
    $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
    $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
@endphp
<div class="col-sm-10" style="margin:20px auto;border:1px solid #ccc">
    <div class="page-header" style="text-align:left;border-bottom:2px double #666">

        <h2 style="margin:4px 10px; font-weight: bold;  text-align: center; color: #FF00FF">উৎসব বোনাস প্রদানের শীট </h2>
        <h3 style="margin:4px 10px; text-align: center; color: #FF00FF"></h3>
        
    </div>
    <!-- unit loop -->
    @php $pageno = 0; $sl = 0;@endphp
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
                                <h5 style="margin:4px 5px; font-size: 12px; color: #FF00FF"><font style="font-weight: bold; font-size: 12px; ">এরিয়াঃ 
                                    {{$location[$l]['hr_location_name']??''}}

                                </font></h5>
                                <h5 style="margin:4px 5px; font-size: 12px; color: #FF00FF"><font style="font-weight: bold; font-size: 12px;">তারিখঃ </h5>
                            </td>
                            <td>
                                <h5 style="margin:4px 5px; font-size: 10px; text-align: right; color: #FF00FF"><font style="font-weight: bold;">পাতা নং # {{eng_to_bn(++$pageno)}}</font></h5>
                                <h5 style="margin:4px 5px; font-size: 13px; text-align: right; color: #FF00FF"><font style="font-weight: bold;"></font></h5>
                                <h5 style="margin:4px 5px; font-size: 13px; text-align: right; color: #FF00FF"><font style="font-weight: bold;">মোট দেয়ঃ 
                                    {{eng_to_bn(bn_money(collect($page)->sum('bonus_amount')))}}
                                </font></h5>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table" style="width:100%;border:1px solid #ccc; font-size:12px; color: #2A86FF"  cellpadding="2" cellspacing="0" border="1" align="center"> 
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
                            <td width="5%">
                                {{eng_to_bn(++$sl)}}
                            </td>
                            <td>
                                <p style="margin: 0px; padding: 0px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font>{{ !empty($emp->hr_bn_associate_name)?$emp->hr_bn_associate_name:null }}</font></p>
                                <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    {{$designation[$emp->designation_id]['hr_designation_name_bn']}}
                                </p>
                                <p style="margin: 0px; padding: 0px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ !empty($emp->as_doj)?(eng_to_bn(date('d-m-Y', strtotime($emp->as_doj)))):null }}
                                </p>

                                <p style="margin: 0px; padding: 0px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp(<?php echo str_replace($en, $bn, floor($emp->duration/12))  ?> বৎসর <?php echo str_replace($en, $bn, ($emp->duration%12))  ?> মাস)</p>

                                <p style="margin: 0px; padding: 0px;">&nbsp;&nbsp;&nbsp;&nbsp;<font></font></p>
                            </td>

                            <td>
                                
                                @php 
                                    $temp_bn = eng_to_bn($emp->temp_id); 
                                @endphp
                                {!! !empty($emp->associate_id)?(substr_replace($emp->associate_id, "<big style='font-size:16px; font-weight:bold;'>$temp_bn</big>", 3, 6)):null !!} 
                                
                                @if($emp->as_oracle_code)
                                    <br> পূর্বের আইডিঃ {{$emp->as_oracle_code}}
                                @endif
                            </td>

                            <td>
                                <p style="margin: 0px; padding: 0px;">
                                    
                                    {{ !empty($emp->gross_salary)?(str_replace($en, $bn,(string)number_format($emp->gross_salary,2, '.', ','))):null }}</p>
                                <p style="margin: 0px; padding: 0px;">মূল বেতনঃ  <?php 
                                    $basic=$emp->basic;
                                     ?>
                                     {{ !empty($emp->basic)?(str_replace($en, $bn,(string)number_format($basic,2, '.', ','))):null }}</p>
                                <p style="margin: 0px; padding: 0px; font-size: 8px;">স্ট্যাম্পঃ ১০</p>
                            </td>
                            <td>
                                <p style="margin: 0px; padding: 0px; font-size: 20px; font-weight: bold;">
                                    {{ !empty($emp->bonus_amount)?(str_replace($en, $bn,(string)number_format($emp->bonus_amount-10,2, '.', ','))):null }}</p>
                                <p style="margin: 0px; padding: 0px;"></p>
                                <p style="margin: 0px; padding: 0px; font-size: 12px; font-weight: bold;">

                                    {{ !empty($emp->bonus_amount)?(str_replace($en, $bn,(string)number_format($emp->bonus_amount,2, '.', ','))):null }}</p>
                            </td>
                            <td width="10%"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
            @endforeach
        @endforeach
    @endforeach
</div>