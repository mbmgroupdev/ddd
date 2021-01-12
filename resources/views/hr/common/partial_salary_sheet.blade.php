
<div id="payment_slip_data" style="font-size: 9px;">
        <button type="button" onclick="printMe('partial_data')" class="btn btn-warning" title="Print">
            <i class="fa fa-print"></i> 
        </button>
        <div class="tinyMceLetter" name="job_application" id="partial_data" style="font-size: 9px;">
            <?php
            date_default_timezone_set('Asia/Dhaka');
            $en = array('0','1','2','3','4','5','6','7','8','9');
            $bn = array('০', '১', '২', '৩',  '৪', '৫', '৬', '৭', '৮', '৯');
            $date = str_replace($en, $bn, date('Y-m-d H:i:s'));
            ?>
            <p>
            <center><h2>{{$employee->hr_unit_name_bn??''}}</h2></center>
            <center>{{ (!empty($employee->hr_unit_address_bn)?$employee->hr_unit_address_bn:null) }}</center>
            <hr>
            <style type="text/css">
                table{
                    font-size: 12px;
                    width: 100%;
                }
                .table-bordered {
                    border-collapse: collapse;
                }
                .table-bordered th,
                .table-bordered td {
                  border: 1px solid #000 !important;
                  padding: 0 5px;
                }
            </style>
            <table border="0" style="width: 100%;">
                <tr>
                    <th colspan="2" style="width:70%;text-align: left;" > আংশিক মজুরীর বিবরণী - </th>
                    <th style="width:30%; text-align: right;">তারিখঃ {{str_replace($en, $bn, date('d-m-Y'))}}</th>
                </tr>
                <tr>
                    <td>কর্মকর্তা/কর্মচারীর নাম </td>
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
                    <td><b>{{$employee->associate_id}}</b>  &nbsp;&nbsp;পূর্বের আইডিঃ {{$employee->as_oracle_code}}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>যোগদানের তারিখ</td>
                    <td>{{str_replace($en, $bn, $employee->as_doj->format('d-m-Y'))}} ইং</td>
                    <td></td>
                </tr>
                <tr>
                    <td>মোট মজুরী</td>
                    <td>{{str_replace($en, $bn, $employee->ben_current_salary)}}</td>
                    <td></td>
                </tr>
                
            </table>
            <br>
            <table style="text-align: center;" class="table-bordered">
                <tr>
                    <td rowspan="2">
                        বিস্তারিত
                    </td>
                    <td>মূল বেতন</td>
                    <td>বাসা ভাড়া</td>
                    <td>মেডিকেল</td>
                    <td>খাদ্য</td>
                    <td>যাতায়াত</td>
                    <td>মোট বেতন</td>
                </tr>
                <tr>
                    <td>{{str_replace($en, $bn, $employee->ben_basic)}}</td>
                    <td>{{str_replace($en, $bn, $employee->ben_house_rent)}}</td>
                    <td>{{str_replace($en, $bn, $employee->ben_medical)}}</td>
                    <td>{{str_replace($en, $bn, $employee->ben_food)}}</td>
                    <td>{{str_replace($en, $bn, $employee->ben_transport)}}</td>
                    <td>{{str_replace($en, $bn, $employee->ben_current_salary)}}</td>
                </tr>
            </table>
            <br>
            <table border="0">
                <tr>
                    <td></td>
                    <td>দিন</td>
                    <td>হার</td>
                    <td style="text-align: right;">মোট</td>
                </tr>
                <tr>
                    <td>{{num_to_bn_month($salary['month'])}}, {{eng_to_bn($salary['year'])}} এর বেতন</td>
                    <td>{{eng_to_bn($salary['salary_date']??0)}}</td>
                    <td>{{eng_to_bn($salary['per_day_gross']??0)}}</td>
                    <td style="text-align: right;">{{eng_to_bn(bn_money(number_format(($salary['salary_date']*$salary['per_day_gross']),2,".","")))}}</td>
                </tr>
                <tr>
                    <td>অনুপস্থিতির কর্তন</td>
                    <td>{{eng_to_bn($salary['absent']??0)}}</td>
                    <td>{{eng_to_bn($salary['per_day_basic']??0)}}</td>
                    <td style="text-align: right;">{{eng_to_bn(bn_money(number_format(($salary['absent_deduct']),2,".","")))}}</td>
                </tr>
                <tr>
                    <td>মজুরী সমন্বয় বাবদ</td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right;">{{eng_to_bn(bn_money(number_format(($salary['adjust']),2,".","")))}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td>ঘন্টা</td>
                    <td>হার</td>
                    <td></td>
                </tr>
                <tr>
                    <td>{{num_to_bn_month($salary['month'])}}, {{eng_to_bn($salary['year'])}} এর অতিরিক্ত</td>
                    <td>{{eng_to_bn($salary['ot_hour']??0)}}</td>
                    <td>{{eng_to_bn($salary['ot_rate']??0)}}</td>
                    <td style="text-align: right;">{{eng_to_bn(bn_money(number_format(($salary['ot_hour']*$salary['ot_rate']),2,".","")))}}</td>
                </tr>
                <tr>
                    <td> </td>
                    <td>সর্বমোট টাকা</td>
                    <td></td>
                    <td style="text-align: right;">{{eng_to_bn(bn_money(number_format($salary['total_payable'],2,".","")))}}</td>
                </tr>
                <tr>
                    <td> </td>
                    <td>চূড়ান্ত প্রাপ্য</td>
                    <td></td>
                    <td style="text-align: right;">{{eng_to_bn(bn_money(number_format($salary['total_payable'],2,".","")))}}</td>
                </tr>
                <tr>
                    @php
                        $bnConvert = new BnConvert();
                        $toword = $bnConvert->bnMoney(number_format($salary['total_payable'],2,".",""));

                    @endphp
                    <td colspan="4" ><br><strong> কথায়ঃ {{$toword}} মাত্র </strong></td>
                </tr>
            </table>
            
            <table style=" " width="100%" cellpadding="3" border="0">
                
                <tr style="width: 100%">
                    <td style="text-align: center;">
                        <br><br><br><br>
                        <hr>
                        প্রস্তুত/যাচাইকারী
                    </td>
                    <td style="text-align: center;">
                        <br><br><br><br>
                        <hr>
                        হিসাব বিভাগ 
                    </td>
                    <td style="text-align: center;">
                       <br><br><br><br>
                        <hr>
                        ব্যাবস্থাপক<br>
                        মানবসম্পদ
                    </td>
                    <td style="text-align: center;">
                        <br><br><br><br>
                        <hr>
                        উপমহাব্যাবস্থাপক<br>
                        প্রশাসন ও মানবসম্পদ বিভাগ
                    </td>
                </tr>
            </table>
            <br>
            <br>
            <br>
            <table>
                <tr>
                    <td>অনুলিপিঃ</td>
                </tr>
                <tr>
                    <td>১। হিসাব বিভাগ</td>
                </tr>
                <tr>
                    <td>২। ব্যাক্তিগত নথি</td>
                </tr>
                <tr>
                    <td>৩। অফিস কপি</td>
                </tr>
            </table>
        </div>
    </div>