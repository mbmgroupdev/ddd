<button type="button" onclick="printMe('payment_slip_data')" class="btn btn-warning" title="Print">
    <i class="fa fa-print"></i> 
</button>
<div class="col-xs-12 no-padding-left" id="payment_slip_data" style="font-size: 12px;">
    <div class="tinyMceLetter" name="job_application" id="job_application" style="font-size: 12px;">
        <?php
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
        $date = str_replace($en, $bn, $benefits->status_date);
        ?>
        <p>
        <center><h2>{{$employee->hr_unit_name_bn??''}}</h2></center>
        <center>{{ (!empty($employee->hr_unit_address_bn)?$employee->hr_unit_address_bn:null) }}</center>
        <center>চূড়ান্ত নিষ্পত্তিকরন</center>
        <br>
        <style type="text/css">
            table{
                font-size: 12px;
            }
            .table-bordered {
                border-collapse: collapse;
            }
            .table-bordered th,
            .table-bordered td {
              border: 1px solid #000 !important;

            }
            .d-flex{display: flex;}
            span.uline {
                border-bottom: 1px solid #000;
                flex-grow: 1;
                text-align: center;
            }
            span.d-uline {
                border-bottom: 1px solid #000;
                display: block;
                text-align: center;
                margin: 0 10px;
            }
            .center{text-align: center;}
        </style>
        <table border="0" style="width: 100%;">
            <tr>
                <td colspan="2">প্রতিষ্ঠানের চাকুরী হইতে পদত্যাগ এর পরিপ্রেক্ষিতে জনাব/জনাবা</td>
                <td colspan="2">তারিখঃ {{$date}} ইং</td>
            </tr>
            <tr>
                <td style="width: 28%;"><div class="d-flex">নামঃ <span class="uline">{{$employee->hr_bn_associate_name??''}}</span></div></td>
                <td style="width: 27%;"><div class="d-flex">আইডি নংঃ <span class="uline">{{str_replace($en, $bn, $employee->associate_id)}}</span> </div></td>
                <td colspan="2" style="width: 45%;"><div class="d-flex">পদবীঃ <span class="uline">{{$employee->hr_designation_name_bn??''}}</span></div></td>
            </tr>
            <tr>
                <td><div class="d-flex">সেকশনঃ <span class="uline">{{$employee->hr_bn_associate_name??''}}</span></div></td>
                <td><div class="d-flex">বিভাগঃ <span class="uline">{{str_replace($en, $bn, $employee->hr_department_name_bn)}}</span> </div></td>
                <td colspan="2">এর চূড়ান্ত নিস্পত্তিকরন নিম্নলিখিতভাবে সম্পন্ন করা হইলঃ</td>
            </tr>
            <tr>
                <td>১। চাকুরীতে যোগদানের তারিখ</td>
                <td colspan="3">{{str_replace($en, $bn, $employee->as_doj->format('Y-m-d'))}}</td>
            </tr>
            <tr>
                <td>২। চাকুরী ছাড়ার তারিখ</td>
                <td colspan="3">{{$date}}</td>
            </tr>
            <tr>
                <td>৩। চাকুরীকালীন সময়ে মোট কার্যকাল</td>
                <td colspan="3">@if($years > 0) {{eng_to_bn($years)}} বছর @endif @if($months > 0) {{eng_to_bn($months)}} মাস @endif</td>
            </tr>
            <tr>
                <td>৪। বর্তমান মজুরী</td>
                <td><span class="uline">{{eng_to_bn(bn_money($employee->ben_current_salary))}}</span> টাকা</td>
                <td>মূল মজুরীঃ <span class="uline">{{eng_to_bn(bn_money($employee->ben_basic))}}</span> টাকা</td>
                <td>অন্যান্যঃ <span class="uline">{{eng_to_bn(bn_money(1850))}}</span> টাকা</td>
            </tr>
            <tr>
                <td>৫। প্র্যাপ্য সুবিধা</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td></td>
                <td><span class="d-uline">দিন</span></td>
                <td><span class="d-uline">হার</span></td>
                <td><span class="d-uline">টাকা</span></td>
            </tr>
            <tr>
                <td>মঞ্জুরীকৃত বাৎসরিক/প্রাপ্ত ছুটি</td>
                <td class="center">{{eng_to_bn($benefits->earned_leave)}}</td>
                <td class="center">{{eng_to_bn($benefits->earned_leave)}}</td>
                <td class="center">{{eng_to_bn(bn_money($benefits->earn_leave_amount))}}</td>
            </tr>
            <tr>
                <td>প্রাপ্ত/মঞ্জুরীকৃত সার্ভিস বেনিফিট</td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
            </tr>
            <tr>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>অন্যান্য বেনিফিট</td>
                <td class="center"></td>
                <td class="center"></td>
                <td class="center"></td>
            </tr>
            <tr>
                <td>সর্বমোট টাকা</td>
                <td colspan="2"></td>
                <td></td>
            </tr>
            <tr>
                <td>৬। প্রদেয় সুবিধা</td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td></td>
                <td><span class="d-uline">মাস</span></td>
                <td><span class="d-uline">হার</span></td>
                <td><span class="d-uline">টাকা</span></td>
            </tr>
            <tr>
                <td>নোটিশ পে</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>অন্যান্য সমন্বয় (যদি থাকে)</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>সর্বমোট টাকা</td>
                <td colspan="2"></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="2">৭। চূড়ান্ত প্র্যাপ্য/পরিশোধিত মোট টাকা</td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="4">
                    কথায়ঃ 
                </td>
            </tr>
        </table>
        <br>
        <br>
       
        <table style=" " width="100%" cellpadding="3" border="0">
            
            <tr style="width: 100%">
                <td style="text-align: center;">
                    <br><br><br>
                    প্রস্তুত
                </td>
                <td style="text-align: center;">
                    <br><br><br>
                    যাচাইকারী
                </td>
                <td style="text-align: center;">
                    <br><br><br>

                    হিসাববিভাগ 
                </td>
                <td style="text-align: center;">
                    <br><br><br>
                    ব্যাবস্থাপক<br>
                    মানবসম্পদ, প্রশাসন ও কমপ্লাইন্স
                </td>
                <td style="text-align: center;">
                    <br><br><br>
                    উপমহাব্যাবস্থাপক<br>
                    মানবসম্পদ, প্রশাসন ও কমপ্লাইন্স
                </td>
            </tr>
            <tr>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="5"> এর কাছ থেকে আমি আমার সকল পাওনা বুঝিয়া পাইয়া নিম্নে স্বাক্ষর ও টিপসহি প্রদান করিলাম</td>
            </tr>
            <tr style="width: 100%">
                <td colspan="4">
                </td>
                <td style="text-align: center;">
                    <br><br><br>
                    স্বাক্ষর
                </td>
            </tr>
        </table>
        <br>
        
        <p>অনুলিপিঃ</p>
        <p>১। একাউন্টস বিভাগ</p>
        <p>২। ব্যাক্তিগত নথি</p>
        <p>৩। অফিস কপি</p>
    </div>
</div>