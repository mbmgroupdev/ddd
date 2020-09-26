<button type="button" onclick="printMe('payment_slip_data')" class="btn btn-warning" title="Print">
    <i class="fa fa-print"></i> 
</button>
<div class="col-xs-12 no-padding-left" id="payment_slip_data" style="font-size: 9px;">
    <div class="tinyMceLetter" name="job_application" id="job_application" style="font-size: 9px;">
        <?php
        date_default_timezone_set('Asia/Dhaka');
        $en = array('0','1','2','3','4','5','6','7','8','9');
        $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
        $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
        ?>
        <p>
        <center><h3>{{$employee->hr_unit_name_bn??''}}</h3></center>
        <center>{{ (!empty($employee->hr_unit_address_bn)?$employee->hr_unit_address_bn:null) }}</center>
        <hr>
        <table border="0" style="width: 100%;">
            <tr>
                <th colspan="2" style="width:70%" style="text-align: left;">মাতৃত্ব কল্যাণ সুবিধার হিসাব - </th>
                <th style="width:30%">তারিখঃ {{str_replace($en, $bn, date('Y-m-d'))}}</th>
            </tr>
            <tr>
                <td>কর্মকর্তা/করমচারীর নাম </td>
                <td>{{$employee->hr_bn_associate_name??''}}</td>
                <td></td>
            </tr>
            <tr>
                <td>পদবী</td>
                <td>{{$employee->hr_designation_name_bn??''}}</td>
                <td></td>
            </tr>
            <tr>
                <td>সেকশন</td>
                <td>{{$employee->hr_section_name_bn??''}}</td>
                <td></td>
            </tr>
            <tr>
                <td>আইডি নং</td>
                <td>{{str_replace($en, $bn, $employee->associate_id)}}</td>
                <td></td>
            </tr>
            </tr>
            <tr>
                <td>যোগদানের তারিখ</td>
                <td>{{str_replace($en, $bn, $employee->as_doj->format('Y-m-d'))}}</td>
                <td></td>
            </tr>
            <tr>
                <td>মোট মজুরী</td>
                <td>{{str_replace($en, $bn, $employee->ben_current_salary)}}</td>
                <td></td>
            </tr>
            <tr>
                <td>সন্তান প্রসবের সম্ভাব্য তারিখ</td>
                <td colspan="2">{{str_replace($en, $bn, $leave->leave_from->format('Y-m-d'))}} তারিখ থেকে {{str_replace($en, $bn, $leave->leave_to->format('Y-m-d'))}} পর্যন্ত</td>
            </tr>
            
        </table>
        <br>
        <strong><u>বিগত ০৩ (তিন) মাসের প্রাপ্ত মজুরীর বিবরনঃ</u></strong>
        <table border="1" style=" text-align: center;" width="100%" cellpadding="3" >
            <tr>
                <th rowspan="2">মাসের নাম</th>
                <th colspan="3">হাজিরা</th>
                <th colspan="6">মজুরী</th>
            </tr>
            <tr>
                <th>উপস্থিত</th>
                <th>অনুপস্থিত</th>
                <th>ছুটি</th>
                <th>মোট প্রদেয় মজুরী</th>
                <th>হাজিরা বোনাস</th>
                <th>অভারটাইম ভাতা</th>
                <th>অন্যান্য ভাতা</th>
                <th>ঈদ বোনাস</th>
                <th>প্রাপ্ত মোট মজুরী</th>
            </tr>
            @foreach($salary as $key => $sal)
                @if($sal)
                <tr>
                    <td>{{$key}}</td>
                    <td>{{str_replace($en, $bn, $sal->present)}}</td>
                    <td>{{str_replace($en, $bn, $sal->absent)}}</td>
                    <td>{{str_replace($en, $bn, $sal->leave)}}</td>
                    <td style="text-align: right;">{{str_replace($en, $bn, $sal->salary_payable)}}</td>
                    <td style="text-align: right;">{{str_replace($en, $bn, $sal->attendance_bonus)}}</td>
                    <td style="text-align: right;"> {{str_replace($en, $bn, $sal->ot_payment)}}</td>
                    <td style="text-align: right;">{{str_replace($en, $bn, $sal->leave_adjust)}}</td>
                    <td style="text-align: right;">{{str_replace($en, $bn, 0)}}</td>
                    <td style="text-align: right;">{{str_replace($en, $bn, $sal->total_payable)}}</td>
                </tr>
                @else
                    <tr>
                        <td>{{$key}}</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                @endif
            @endforeach
            <tr>
                <td>মোট</td>
                <td>{{str_replace($en, $bn, $totalcalc['present'])}}</td>
                <td>{{str_replace($en, $bn, $totalcalc['absent'])}}</td>
                <td>{{str_replace($en, $bn, $totalcalc['leave'])}}</td>
                <td style="text-align: right;">{{str_replace($en, $bn, $totalcalc['salary_payable'])}}</td>
                <td style="text-align: right;">{{str_replace($en, $bn, $totalcalc['attendance_bonus'])}}</td>
                <td style="text-align: right;">{{str_replace($en, $bn, $totalcalc['ot_payment'])}}</td>
                <td style="text-align: right;">{{str_replace($en, $bn, $totalcalc['leave_adjust'])}}</td>
                <td style="text-align: right;">{{str_replace($en, $bn, 0)}}</td>
                <td style="text-align: right;">{{str_replace($en, $bn, $totalcalc['total'])}}</td>
            </tr>
        </table>
        <br><br><br>
        <table style="border: none; " width="100%" cellpadding="3" width="100%">
            <tr>
                <th style="width: 30%;">০১ (এক) দিনের গড় মজুরী</th>
                <td>= তিন মাসের মোট প্রাপ্ত টাকা / (ভাগ) তিন মাসের মোট উপস্থিতি</td>
            </tr>
            <tr>
                <td style="width: 30%;">০১ (এক) দিনের গড় মজুরী</td>
                <td>= {{str_replace($en, $bn, $totalcalc['per_wages'])}} টাকা</td>
            </tr>
            <tr>
                <td style="width: 30%;">৫৬ দিনের মোট মজুরী</td>
                <td>= {{str_replace($en, $bn, $totalcalc['first_pay'])}} টাকা</td>
            </tr>
            <tr>
                <td style="width: 30%;">১ম কিস্তির টাকা</td>
                <td>= {{str_replace($en, $bn, $totalcalc['first_pay'])}} টাকা</td>
            </tr>
            <tr>
                <td style="width: 30%;">২য় কিস্তির টাকা</td>
                <td>= {{str_replace($en, $bn, $totalcalc['second_pay'])}} টাকা</td>
            </tr>
            <tr>
                <td style="width: 30%;">মোট প্রদেয়</td>
                <td>= {{str_replace($en, $bn, $totalcalc['total_pay'])}} টাকা</td>
            </tr>
            
        </table >
       
        <table style=" " width="100%" cellpadding="3" border="0">
            
            <tr style="width: 100%">
                <td style="text-align: center;">
                    <br><br><br>
                    প্রস্তুত/যাচাইকারী
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
        </table>
    </div>
</div>