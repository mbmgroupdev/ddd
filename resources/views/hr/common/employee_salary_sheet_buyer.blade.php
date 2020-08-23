@if(count($getSalaryList) == 0)
    <b><h5 class="text-center"> No data found !</h5></b>

@endif
@foreach($uniqueLocation as $location)
@php
    $getLocation = Custom::unitNameBangla($location);
@endphp

<div class="panel panel-info">
    <div class="panel-heading">ইউনিট :<b> {{ $pageHead->unit_name }}</b> - লোকেশন :<b> {{ isset($getLocation)?$getLocation->hr_unit_name_bn:'' }}</b></div>
    <div class="panel-body">

        <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
            <tr>
                <td style="width:14%">
                    <p style="margin:0;padding:4px 0"><strong>তারিখঃ </strong>
                        {{ Custom::engToBnConvert($pageHead->current_date) }}
                    </p>
                    <p style="margin:0;padding:4px 0"><strong>&nbsp;সময়ঃ </strong>
                        {{ Custom::engToBnConvert($pageHead->current_time) }}
                    </p>
                </td>
                <td style="width:15%;font-size:10px">
                    @if($pageHead->pay_date != null)
                    <p style="margin:0;padding:4px 0"><strong>&nbsp;প্রদান তারিখঃ </strong>
                        {{ Custom::engToBnConvert($pageHead->pay_date) }} ইং
                    </p>
                    @endif
                </td>
                <td>
                    <h3 style="margin:4px 10px;text-align:center;font-weight:600;font-size:18px;">
                        {{ $pageHead->unit_name }}
                    </h3>
                    <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:14px;">বেতন/মজুরি এবং অতিরিক্ত সময়ের মজুরীঃ
            <br/>
            তারিখঃ {{ Custom::engToBnConvert($pageHead->for_date) }}</h5>
                </td>
                <td style="width:22%">
                    @if($pageHead->floor_name != null)
                    <p style="margin:0;padding:4px 0;">
                        <strong>ফ্লোর নংঃ
                            {{ Custom::engToBnConvert($pageHead->floor_name) }}
                        </strong>
                    </p>
                    @endif
                </td>
            </tr>
        </table>

        <table class="table" style="width:100%;border:1px solid #ccc;font-size:9px;color:lightseagreen" cellpadding="2" cellspacing="0" border="1" align="center">
            <thead>
                <tr style="color:hotpink">
                    <th style="color:lightseagreen">ক্রমিক নং</th>
                    <th width="180">কর্মী/কর্মচারীদের নাম
                        <br/> ও যোগদানের তারিখ</th>
                    <th>আই ডি নং</th>
                    <th>মাসিক বেতন/মজুরি</th>
                    <th width="140">হাজিরা দিবস</th>
                    <th width="220">বেতন হইতে কর্তন </th>
                    <th width="250">মোট দেয় টাকার পরিমান</th>
                    <th>সর্বমোট টাকার পরিমান</th>
                    <th width="80">দস্তখত</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 0;
                @endphp
                @foreach($getSalaryList as $list)
                @if($list->as_location == $location)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>
                        <p style="margin:0;padding:0;">
                          {{ $list->hr_bn_associate_name}}
                        </p>
                        <p style="margin:0;padding:0;">{{ Custom::engToBnConvert($list->as_doj) }}</p>
                        <p style="margin:0;padding:0;">{{ isset($list->designation['hr_designation_grade'])? Custom::engToBnConvert($list->designation['hr_designation_grade']):'' }} </p>
                        <p style="margin:0;padding:0;color:hotpink">মূল+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য </p>
                        <p style="margin:0;padding:0;">
                            {{ Custom::engToBnConvert($list->basic.'+'.$list->house.'+'.$list->medical.'+'.$list->transport.'+'.$list->food) }}
                        </p>
                    </td>
                    <td>
                        <p style="font-size:14px;margin:0;padding:0;color:blueviolet">
                            {{ $list->associate_id }}
                        </p>
                        <p style="margin:0;padding:0;color:hotpink">
                            বিলম্ব উপস্থিতিঃ {{ Custom::engToBnConvert($list->late_count) }}
                        </p>
                        <p style="margin:0;padding:0">গ্রেডঃ {{ isset($list->designation['hr_designation_grade'])? Custom::engToBnConvert($list->designation['hr_designation_grade']):'' }}</p>
                    </td>
                    <td>
                        <p style="margin:0;padding:0">
                            {{ Custom::engToBnConvert($list->gross) }}
                        </p>
                    </td>
                    <td>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত দিবস
                            </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink;" > {{ Custom::engToBnConvert($list->present) }}</font>
                            </span>

                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">সরকারি ছুটি </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink"> {{ Custom::engToBnConvert($list->holiday) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0k">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিত দিবস </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                  <font style="color:hotpink"> {{ Custom::engToBnConvert($list->absent) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি মঞ্জুর </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($list->leave) }}</font>
                            </span>

                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দেয় </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ Custom::engToBnConvert($list->present + $list->holiday + $list->leave)}}</font>
                            </span>
                        </p>
                    </td>
                    <td>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিতির জন্য</span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{  Custom::engToBnConvert($list->absent_deduct) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অর্ধ দিবসের জন্য কর্তন </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert($list->half_day_deduct) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অগ্রিম গ্রহণ বাবদ </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                              {{ ($list->salary_add_deduct_id == null) ? Custom::engToBnConvert('0.00') : Custom::engToBnConvert($list->advp_deduct) }}
                            </font>
                            </span>




                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">স্ট্যাম্প বাবদ </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert('10.00') }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ভোগ্যপণ্য ক্রয় </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                              @if($list->salary_add_deduct_id == null)
                                  {{ Custom::engToBnConvert('0.00') }}
                              @else
                                  {{ isset($list->cg_product) ? $list->cg_product:Custom::engToBnConvert('0.00') }}
                              @endif
                            </font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">খাবার বাবদ কর্তন </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                              {{ ($list->salary_add_deduct_id == null) ? Custom::engToBnConvert('0.00') : Custom::engToBnConvert($list->food_deduct) }}
                            </font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অন্যান্য </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">
                              {{ ($list->salary_add_deduct_id == null) ? Custom::engToBnConvert('0.00') : Custom::engToBnConvert($list->others_deduct) }}
                            </font>
                            </span>

                     {{--        ভোগ্যপণ্য ক্রয় &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= </font>
                        </p>
                        <p style="margin:0;padding:0">
                            খাবার বাবদ কর্তন &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">=
                            </font>
                        </p>
                        <p style="margin:0;padding:0">
                            অন্যান্য &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= </font> --}}

                        </p>
                    </td>

                    @php
                        $otHour = intdiv($list->ot_hour, 60).'.'. ($list->ot_hour % 60);
                        $ot = round((float)($list->ot_rate) * ($list->ot_hour / 60));
                        $salaryAdd = ($list->salary_add_deduct_id == null) ? '0.00' : $list->salary_add;

                    @endphp
                    <td>
                        <p style="margin:0;padding:0">

                              <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মঞ্জুরি </span>
                              <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                              <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink"> {{ Custom::engToBnConvert($list->salary_payable) }}</font>
                             </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত সময়ের কাজের মঞ্জুরি </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert($ot) }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                                 <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত কাজের মঞ্জুরি হার </span>
                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink">{{ Custom::engToBnConvert($list->ot_rate) }} </font>
                                </span>
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink"> ({{ $list->as_ot==1?Custom::engToBnConvert($otHour):Custom::engToBnConvert('00') }}  ঘন্টা)</font>
                                </span>

                            <?php
                                // show emploee holiday ot hours
                                //if(isset($list->holiday_ot_minutes)) {
                                 //   if($list->holiday_ot_minutes != 0) {
                                  //      $holiday_ot_hours = 0;
                                  //      $holiday_ot_hours = number_format((float)($list->holiday_ot_minutes/60), 2, '.', ''); // minute to float hours
                                 //       $holiday_ot_hours = sprintf('%02d:%02d', (int) $holiday_ot_hours, fmod($holiday_ot_hours, 1) * 60); // convert float hours to hour:minute
                                //        echo '('.$holiday_ot_hours.')';
                               //     }
                               // }
                            ?>

                        </p>
                        <p style="margin:0;padding:0">

                                <span style="text-align: right;width: 65%; float: right;  white-space: wrap;">&nbsp;
                                </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত বোনাস </span>
                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink">{{ Custom::engToBnConvert($list->attendance_bonus) }}</font>
                                </span>


                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মঞ্জুরি অগ্রিম/সমন্বয়</span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ Custom::engToBnConvert($salaryAdd) }}</font>
                            </span>

                           {{--  বেতন/মঞ্জুরি অগ্রিম/সমন্বয় &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= </font> --}}

                        </p>
                    </td>
                    <td>
                        @php
                            $totalSalary = ($list->salary_payable + $salaryAdd + $ot + $list->attendance_bonus);
                        @endphp
                        {{ Custom::engToBnConvert($totalSalary) }}
                    </td>
                    <td></td>
                </tr>
                @endif
              @endforeach
            </tbody>
        </table>


    </div>
</div>
@endforeach
