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
        <style type="text/css">
            .amount{text-align: right;width: 100px;display: inline-block;}
            .amount_type{width: 120px;display: inline-block;}
            .pay-slip-single span {
                font-size: 12px !important;
            }
            .sl-payslip{border-radius:50%;width: 40px;height: 40px;border:1px solid #999;color:#999;line-height: 40px;text-align:center;display: inline-block;position: absolute;right: 20px;top: 10px;}
        </style>
        <style type="text/css" media="print">
            .page-break{
                page-break-after: always;
            }
            th{font-weight: normal !important;}
            p,td,th{ margin: 3pt 0 !important;}
            .flex {
                display: flex;
            }
            
            .justify-content-between {
                justify-content: space-between!important;
            }

        </style>
        
        <!-- unit loop -->
        @php $pageno = 0; $sl = 0; $tEmp = 0; $tStamp = 0; $tBonus = 0;@endphp
        @foreach($bonusList as $u => $unitList)
            <!-- lcation loop -->
            @foreach($unitList as $l => $locList)
                <!-- perage 10 employee -->
                @foreach($locList as $key => $page)

                    @foreach($page as $key => $emp)
                    <div class="pay-slip-single" style="position: relative;margin: 10px 0">
                            <h2 style="margin:4px 10px;line-height: 1.4; font-weight: bold;  text-align: center;">{{$unit[$u]['hr_unit_name_bn']??''}}</h2>
                            <h5 style="margin:4px 10px;line-height: 1.4;  text-align: center;">বোনাস পে স্লিপ - ঈদ-উল-ফিতর{{-- {{$rules->bonus_type_name}} --}},  {{eng_to_bn(date('Y', strtotime($rules->cutoff_date )))}} </h5>
                            <center>আইডিঃ <b>{{$emp->associate_id}}</b> @if($emp->as_oracle_code) (পূর্বের আইডিঃ {{$emp->as_oracle_code}} ) @endif</center>

                            <span class="sl-payslip">{{eng_to_bn(++$sl)}}</span>
                            <br>
                            <table border="0" style="width: 100%;">
                                <tr>
                                    <td style="width:33.33%">
                                        <p>নামঃ {{ !empty($emp->hr_bn_associate_name)?$emp->hr_bn_associate_name:null }} </p> 
                                        <p>যোগদানঃ {{ !empty($emp->as_doj)?(eng_to_bn(date('d-m-Y', strtotime($emp->as_doj)))):null }}</p>
                                        <p>পদবীঃ {{$designation[$emp->designation_id]['hr_designation_name_bn']}}</p>
                                        <p>(<?php echo str_replace($en, $bn, floor($emp->duration/12))  ?> বৎসর <?php echo str_replace($en, $bn, ($emp->duration%12))  ?> মাস) </p>
                                        <br>
                                        @if($emp->override== 1)
                                        @if(isset($previousData[$emp->associate_id]))
                                            <b>&nbsp; Ex. Bonus ( {{eng_To_bn(number_format($previousData[$emp->associate_id],2, '.', ','))}})</b>
                                        @endisset
                                        @endif

                                    </td>
                                    <td style="width:33.33%">
                                        <p><span class="amount_type">মূল বেতন:</span> <span class="amount" >{{eng_To_bn(number_format($emp->basic,2, '.', ','))}}</span></p>
                                        <p><span class="amount_type">বাড়ী ভাড়া:</span> <span class="amount" >{{eng_To_bn(number_format(($emp->gross_salary - $emp->basic - $emp->medical - $emp->transport - $emp->food),2, '.', ','))}}</span></p>
                                        <p><span class="amount_type">চিকিৎসা ভাতা:</span> <span class="amount" >{{eng_To_bn(number_format($emp->medical,2, '.', ','))}}</span></p>
                                        <p><span class="amount_type">যাতায়াত:</span> <span class="amount" >{{eng_To_bn(number_format($emp->transport,2, '.', ','))}}</span></p>
                                        <p><span class="amount_type">খাদ্য:</span> <span class="amount" >{{eng_To_bn(number_format($emp->food,2, '.', ','))}}</span></p>
                                        <hr style="margin: 2px">
                                        <p><span class="amount_type">মোট বেতন/মজুরী:</span> <span class="amount" >{{eng_To_bn(number_format($emp->gross_salary,2, '.', ','))}}</span></p>
                                    </td>
                                    <td style="width:33.33%">
                                        <p class="flex justify-content-between">
                                            <span>&nbsp;বোনাসঃ</span>
                                            <span >
                                                {{ !empty($emp->bonus_amount)?(str_replace($en, $bn,(string)number_format($emp->bonus_amount,2, '.', ','))):null }}
                                            </span>
                                        </p>
                                        <p class="flex justify-content-between">
                                            <span >&nbsp;স্ট্যাম্প বাবদ কর্তন</span>
                                            <span>
                                                {{eng_To_bn(number_format($emp->stamp,2, '.', ','))}}
                                            </span>
                                        </p>
                                        <hr style="margin: 2px">
                                         @php $tEmp++; $tStamp += $emp->stamp; $tBonus += $emp->net_payable;@endphp
                                        <p class="flex justify-content-between">
                                            <b>&nbsp;মোট প্রদেয়ঃ</b>
                                            <b >
                                                {{ !empty($emp->net_payable)?(str_replace($en, $bn,(string)number_format($emp->net_payable,2, '.', ','))):null }}
                                            </b>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <hr>
                        </div>
                    @endforeach
                    <div class="page-break page-break page-break-{{++$pageno}}"></div>
                @endforeach
            @endforeach
        @endforeach
        <style type="text/css">
            .page-break-{{$pageno}}{
                page-break-after: avoid !important;
            }
        </style>
        <div style="text-align:right;font-weight:bold;">
            <h2>সর্বমোট কর্মকর্তা/কর্মচারীঃ {{eng_to_bn($tEmp)}}</h2>
            <h2>স্ট্যাম্প বাবদঃ {{eng_to_bn(bn_money($tStamp))}}</h2>
            <h2>সর্বমোট বোনাসঃ {{eng_to_bn(bn_money($tBonus))}}</h2>
        </div>
    </div>
</div>
