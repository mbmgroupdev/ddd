
<div class="panel panel-info">
    <div class="panel-heading">{{ $title }}</div>
    <div class="panel-body salary_report_body">

        <table class="table" style="width:100%;border-bottom:1px solid #ccc;margin-bottom:0;font-size:12px;color:lightseagreen;text-align:left" cellpadding="5">
            <tr>
                <td style="width:14%">
                    <p style="margin:0;padding:4px 0"><strong>তারিখঃ </strong>
                        {{ $pageHead->current_date }}
                    </p>
                    <p style="margin:0;padding:4px 0"><strong>&nbsp;সময়ঃ </strong>
                        {{ $pageHead->current_time }}
                    </p>
                </td>
                <td style="width:15%;font-size:10px">
                    @if($pageHead->pay_date != null)
                    <p style="margin:0;padding:4px 0"><strong>&nbsp;প্রদান তারিখঃ </strong>
                        {{ $pageHead->pay_date }} ইং
                    </p>
                    @endif
                </td>
                <td>
                    <h3 style="margin:4px 10px;text-align:center;font-weight:600;font-size:18px;">
                        {{ $pageHead->unit_name }}
                    </h3>
                    <h5 style="margin:4px 10px;text-align:center;font-weight:600;font-size:14px;">বেতন/মজুরি এবং অতিরিক্ত সময়ের মজুরীঃ
            <br/>
            তারিখঃ {{ $pageHead->for_date }}</h5>
                </td>
                <td style="width:22%">
                    @if($pageHead->floor_name != null)
                    <p style="margin:0;padding:4px 0;">
                        <strong>ফ্লোর নংঃ
                            {{ $pageHead->floor_name }}
                        </strong>
                    </p>
                    @endif
                </td>
            </tr>
        </table>

        <table class="table" style="width:100%;border:1px solid #ccc;font-size:9px;color:lightseagreen" cellpadding="2" cellspacing="0" border="1" align="center" autosize="0">
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
                @if(count($getSalaryList) == 0)
                    <tr>
                        <td colspan='9'> <b><h5 class="text-center"> No data found !</h5></b></td>
                    </tr>
                @endif
                @php
                    $i = 0;
                @endphp
                @foreach($getSalaryList as $list)
                @php
                // get total hour with minutes calculation
                /*if (strpos($list->ot_hour, ':') !== false) {
                    list($hour,$minutes) = array_pad(explode(':',$list->ot_hour),2,NULL);
                    $minuteHour = 0;
                    if($minutes!==NULL) {
                        $minuteHour = number_format((float)($minutes/60), 3, '.', '');;
                        $list->ot_hour = $hour + $minuteHour;
                    }
                }*/
                // get designation
                if(isset($list->employee->as_designation_id)){
                    $designation = App\Models\Hr\Designation::where('hr_designation_id', $list->employee->as_designation_id)->first();
                } else {
                    $designation = new stdClass();
                }
                @endphp
                <?php //dump($list->ot_overtime_minutes);?>
                <tr>
                    <td>{{ ++$i }} </td>
                    <td>
                        <p style="margin:0;padding:0;">{{ $list->employee_bengali['hr_bn_associate_name'] }}</p>
                        <p style="margin:0;padding:0;">{{ $list->employee['as_doj'] }}</p>
                        <p style="margin:0;padding:0;">{{ isset($designation->hr_designation_grade)?$designation->hr_designation_grade:'' }}</p>
                        <p style="margin:0;padding:0;color:hotpink">মূল+বাড়ি ভাড়া+চিকিৎসা+যাতায়াত+খাদ্য </p>
                        <p style="margin:0;padding:0;">
                            {{ $list->basic.'+'.$list->house.'+'.$list->medical.'+'.$list->transport.'+'.$list->food }}
                        </p>
                    </td>
                    <td>
                        <p style="font-size:14px;margin:0;padding:0;color:blueviolet">
                            {{ $list->as_id }}
                        </p>
                        <p style="margin:0;padding:0;color:hotpink">
                            বিলম্ব উপস্থিতিঃ {{ $list->late_count }}
                        </p>
                        <p style="margin:0;padding:0">গ্রেডঃ {{ isset($designation->hr_designation_grade)?$designation->hr_designation_grade:'' }}</p>
                    </td>
                    <td>
                        <p style="margin:0;padding:0">
                            {{ $list->gross }}
                        </p>
                    </td>
                    <td>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত দিবস
                            </span> 
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink;" > {{ $list->present}}</font>
                            </span>  
                          
                        </p>
                        <p style="margin:0;padding:0">                            
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">সরকারি ছুটি </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink"> {{$list->holiday}}</font>
                            </span> 
                        </p>
                        <p style="margin:0;padding:0k">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিত দিবস </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                  <font style="color:hotpink"> {{ $list->absent}}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ছুটি মঞ্জুর </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ $list->leave }}</font>
                            </span>
                       
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">মোট দেয় </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink"> {{ ($list->present + $list->holiday + $list->leave)}}</font>
                            </span>
                        </p>
                    </td>
                    <td>
                        <p style="margin:0;padding:0">
                           
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অনুপস্থিতির জন্য</span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{  $list->absent_deduct }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অর্ধ দিবসের জন্য কর্তন </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{$list->half_day_deduct }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অগ্রিম গ্রহণ বাবদ </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->advp_deduct }} </font>
                            </span>



                          {{--   অগ্রিম গ্রহণ বাবদ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= {{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->advp_deduct }} </font> --}}

                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">স্ট্যাম্প বাবদ </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink"> 10.00</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">ভোগ্যপণ্য ক্রয় </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{ ($list->add_deduct == null) ? '0.00' : (isset($list->add_deduct['cg_product'])?$list->add_deduct['cg_product']:'') }}</font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">খাবার বাবদ কর্তন </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct['food_deduct'] }} </font>
                            </span>
                        </p>
                        <p style="margin:0;padding:0">
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অন্যান্য </span>
                            <span style ="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;"><font style="color:hotpink">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->others_deduct }} </font>
                            </span>

                     {{--        ভোগ্যপণ্য ক্রয় &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= {{ ($list->add_deduct == null) ? '0.00' : isset($list->add_deduct['cg_product'])?$list->add_deduct['cg_product']:'' }}</font>
                        </p>
                        <p style="margin:0;padding:0">
                            খাবার বাবদ কর্তন &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= {{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->food_deduct }} </font>
                        </p>
                        <p style="margin:0;padding:0">
                            অন্যান্য &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= {{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->others_deduct }} </font> --}}

                        </p>
                    </td>
                    <?php $otHour = date('H.i', mktime(0, $list->ot_hour)); ?>
                    <td>
                        <p style="margin:0;padding:0">
                           
                              <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মঞ্জুরি </span>
                              <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                              <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink"> {{ $list->salary_payable}}</font>
                             </span>
                        </p>
                        <p style="margin:0;padding:0">
                      
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত সময়ের কাজের মঞ্জুরি </span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ ($list->ot_rate * $otHour)}}</font>
                            </span>    
                        </p>
                        <p style="margin:0;padding:0">
                         

                                 <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">অতিরিক্ত কাজের মঞ্জুরি হার </span>
                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink">{{ $list->ot_rate }} </font>
                                </span> 
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink"> ({{ $list->employee['as_ot']==1?$otHour:'00' }}  ঘন্টা)</font>
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
                           
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">উপস্থিত বোনাস </span>
                                <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=</span>
                                <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                    <font style="color:hotpink">{{$list->attendance_bonus }}</font>
                                </span> 


                        </p>
                        <p style="margin:0;padding:0">

                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;">বেতন/মঞ্জুরি অগ্রিম/সমন্বয়</span>
                            <span style="text-align: right;width: 5%; float: left;white-space: wrap;color: hotpink;">=
                            </span>
                            <span style="text-align: right;width: 30%; float: right;  white-space: wrap;">
                                <font style="color:hotpink">{{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->salary_add }}</font>
                            </span>

                           {{--  বেতন/মঞ্জুরি অগ্রিম/সমন্বয় &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font style="color:hotpink">= {{ ($list->add_deduct == null) ? '0.00' : $list->add_deduct->salary_add }}</font> --}}

                        </p>
                    </td>
                    <td>
                        @php
                            $ot = ($list->ot_rate * $otHour);
                            $salaryAdd = ($list->add_deduct == null) ? '0.00' : $list->add_deduct->salary_add;
                            $total = ($list->salary_payable + $ot + $list->attendance_bonus + $salaryAdd);
                        @endphp
                        {{ $total }}



                    </td>
                    <td></td>
                </tr>
              @endforeach
            </tbody>
        </table>

        
    </div>
</div>