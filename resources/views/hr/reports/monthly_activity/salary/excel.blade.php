<div class="panel">
    <div class="panel-body">
        <div id="report_section" class="report_section">
            
            @php
                $unit = unit_by_id();
                $line = line_by_id();
                $floor = floor_by_id();
                $department = department_by_id();
                $designation = designation_by_id();
                $section = section_by_id();
                $subSection = subSection_by_id();
                $area = area_by_id();
                $location = location_by_id();
                $formatHead = explode('_',$format);
            @endphp
            <div class="top_summery_section">
                @if($input['report_format'] == 0 || ($input['report_format'] == 1 && $format != null))
                <div class="page-header">
                    <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Salary @if($input['report_format'] == 0) Details @else Summary @endif Report </h2>
                    <h4  style="text-align: center;">Month : {{ date('M Y', strtotime($input['month'])) }} </h4>
                </div>
                
                @endif
            </div>
            <div class="content_list_section">
                @if($input['report_format'] == 0)
                    @foreach($uniqueGroupEmp as $group => $employees)
                    
                    <table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
                        <thead style="font-weight: bold; font-size:14px; text-align: center;">
                            @if(count($employees) > 0)
                            <tr>
                                @php
                                    if($format == 'as_unit_id'){
                                        $head = 'Unit';
                                        $body = $unit[$group]['hr_unit_name']??'';
                                    }elseif($format == 'as_line_id'){
                                        $head = 'Line';
                                        $body = $line[$group]['hr_line_name']??'';
                                    }elseif($format == 'as_floor_id'){
                                        $head = 'Floor';
                                        $body = $floor[$group]['hr_floor_name']??'';
                                    }elseif($format == 'as_department_id'){
                                        $head = 'Department';
                                        $body = $department[$group]['hr_department_name']??'';
                                    }elseif($format == 'as_designation_id'){
                                        $head = 'Designation';
                                        $body = $designation[$group]['hr_designation_name']??'';
                                    }elseif($format == 'as_section_id'){
                                        $head = 'Section';
                                        $body = $section[$group]['hr_section_name']??'';
                                    }elseif($format == 'as_subsection_id'){
                                        $head = 'Sub Section';
                                        $body = $subSection[$group]['hr_subsec_name']??'';
                                    }else{
                                        $head = '';
                                    }
                                @endphp
                                @if($head != '')
                                <th style=" font-weight: bold; font-size:13px;" colspan="2">{{ $head }}</th>
                                <th style=" font-weight: bold; font-size:13px;" colspan="13">{{ $body }}</th>
                                @endif
                            </tr>
                            @endif
                            <tr>
                                <th style=" font-weight: bold; font-size:13px;">Sl</th>
                                <th style=" font-weight: bold; font-size:13px;">Oracle ID</th>
                                <th style=" font-weight: bold; font-size:13px;">Associate ID</th>
                                <th style=" font-weight: bold; font-size:13px;">Name</th>
                                <th style=" font-weight: bold; font-size:13px;">OT Status</th>
                                <th style=" font-weight: bold; font-size:13px;">Designation</th>
                                <th style=" font-weight: bold; font-size:13px;">Department</th>
                                <th style=" font-weight: bold; font-size:13px;">DOJ</th>
                                <th style=" font-weight: bold; font-size:13px;">Gross</th>
                                <th style=" font-weight: bold; font-size:13px;">Basic</th>
                                <th style=" font-weight: bold; font-size:13px;">House Rent</th>
                                <th style=" font-weight: bold; font-size:13px;">Other Part</th>
                                <th style=" font-weight: bold; font-size:13px;">Present</th>
                                <th style=" font-weight: bold; font-size:13px;">Absent</th>
                                <th style=" font-weight: bold; font-size:13px;">OT Hour</th>
                                <th style=" font-weight: bold; font-size:13px;">OT Amount</th>
                                
                                <th style=" font-weight: bold; font-size:13px;">Attendance Bonus</th>
                                <th style=" font-weight: bold; font-size:13px;">Payable Salary</th>
                                @if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
                                <th style=" font-weight: bold; font-size:13px;">Bank Amount</th>
                                <th style=" font-weight: bold; font-size:13px;">Tax Amount</th>
                                @endif
                                @if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
                                <th style=" font-weight: bold; font-size:13px;">Cash Amount</th>
                                @endif
                                
                                <th style=" font-weight: bold; font-size:13px;">Stamp Amount</th>
                                <th style=" font-weight: bold; font-size:13px;">Net Pay</th>
                                <th style=" font-weight: bold; font-size:13px;">Payment Method</th>
                                <th style=" font-weight: bold; font-size:13px;">Account No.</th>
                                <th style=" font-weight: bold; font-size:13px;">Location</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php $i = 0; $otHourSum=0; $salarySum=0; $month = $input['month']; @endphp
                        @if(count($employees) > 0)
                            @foreach($employees as $employee)
                                @php
                                    $designationName = $employee->hr_designation_name??'';
                                    $otHour = ($employee->ot_hour);
                                    $otAmount = ((float)($employee->ot_rate) * $employee->ot_hour);
                                    $otAmount = number_format((float)$otAmount, 2, '.', '');
                                @endphp
                                @if($head == '')
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    
                                    <td>{{ $employee->as_oracle_code }}</td>
                                    <td>{{ $employee->associate_id }}</td>
                                    <td>
                                        <b>{{ $employee->as_name }}</b>
                                    </td>
                                    <td>{{ $employee->ot_status == 1?'OT':'Non OT'}}</td>
                                    <td>{{ $designationName }}</td>

                                    <td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
                                    <td>{{ date('m/d/Y', strtotime($employee->as_doj)) }}</td>
                                    <td>{{ $employee->gross }}</td>
                                    <td>{{ $employee->basic }}</td>
                                    <td>{{ $employee->house }}</td>
                                    <td>{{ ($employee->medical + $employee->transport + $employee->food) }}</td>
                                    <td>{{ $employee->present }}</td>
                                    <td>{{ $employee->absent }}</td>
                                    <td><b>{{ number_format($otHour,2) }}</b></td>
                                    <td><b>{{ $otAmount }}</b></td>
                                    
                                    <td>{{ $employee->attendance_bonus }}</td>
                                    <td>
                                        @php $totalPay = $employee->total_payable + $employee->stamp; @endphp
                                        {{ ($totalPay) }}
                                    </td>   
                                    @if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
                                    <td>{{ ($employee->bank_payable) }}</td>
                                    <td>{{ ($employee->tds) }}</td>
                                    @endif
                                    @if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
                                    <td>{{ ($employee->cash_payable + $employee->stamp) }}</td>
                                    @endif
                                    <td>{{ ($employee->stamp) }}</td>
                                    
                                    <td>
                                        @php
                                            if($input['pay_status'] == 'cash'){
                                                $totalNet = $employee->cash_payable;
                                            }else{
                                                $totalNet = $employee->total_payable - $employee->tds;
                                            }
                                        @endphp
                                        {{ ($totalNet) }}
                                    </td>
                                    <td>
                                        @if($employee->pay_status == 1)
                                            Cash
                                        @elseif($employee->pay_status == 2)
                                            {{ $employee->bank_name }}
                                        @else
                                            {{ $employee->bank_name }} &amp; Cash
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->pay_status == 2)
                                            <b>{{ $employee->bank_no }}</b>
                                        @elseif($employee->pay_status == 3)
                                            <b>{{ $employee->bank_no }}</b>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $location[$employee->as_location]['hr_location_name']??'' }}
                                    </td>
                                </tr>
                                @else
                                @if($group == $employee->$format)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $employee->as_oracle_code }}</td>
                                    <td>{{ $employee->associate_id }}</td>
                                    <td>
                                        <b>{{ $employee->as_name }}</b>
                                    </td>
                                    <td>{{ $employee->ot_status == 1?'OT':'Non OT'}}</td>
                                    <td>{{ $designationName }}</td>
                                    <td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
                                    <td>{{ date('m/d/Y', strtotime($employee->as_doj)) }}</td>
                                    <td>{{ $employee->gross }}</td>
                                    <td>{{ $employee->basic }}</td>
                                    <td>{{ $employee->house }}</td>
                                    <td>{{ ($employee->medical + $employee->transport + $employee->food) }}</td>
                                    <td>{{ $employee->present }}</td>
                                    <td>{{ $employee->absent }}</td>
                                    <td><b>{{ number_format($otHour,2) }}</b></td>
                                    <td><b>{{ $otAmount }}</b></td>
                                    
                                    <td>{{ $employee->attendance_bonus }}</td>
                                    <td>
                                        @php $totalPay = $employee->total_payable + $employee->stamp; @endphp
                                        {{ ($totalPay) }}
                                    </td>   
                                    @if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
                                    <td>{{ ($employee->bank_payable) }}</td>
                                    <td>{{ ($employee->tds) }}</td>
                                    @endif
                                    @if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
                                    <td>{{ ($employee->cash_payable) }}</td>
                                    @endif
                                    <td>{{ ($employee->stamp) }}</td>
                                    <td>
                                        @php
                                            if($input['pay_status'] == 'cash'){
                                                $totalNet = $employee->cash_payable;
                                            }else{
                                                $totalNet = $employee->total_payable - $employee->tds;
                                            }
                                        @endphp
                                        {{ ($totalNet) }}
                                    </td>
                                    <td>
                                        @if($employee->pay_status == 1)
                                            Cash
                                        @elseif($employee->pay_status == 2)
                                            {{ $employee->bank_name }}
                                        @else
                                            {{ $employee->bank_name }} &amp; Cash
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->pay_status == 2)
                                            <b>{{ $employee->bank_no }}</b>
                                        @elseif($employee->pay_status == 3)
                                            <b>{{ $employee->bank_no }}</b>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $location[$employee->as_location]['hr_location_name']??'' }}
                                    </td>
                                </tr>
                                @endif
                                @endif
                            @endforeach
                        @else
                            <tr>
                                @if($input['pay_status'] == 'cash')
                                <td colspan="16" class="text-center">No Employee Found!</td>
                                @elseif($input['pay_status'] != 'cash' && $input['pay_status'] != 'all')
                                <td colspan="16" class="text-center">No Employee Found!</td>
                                @else
                                <td colspan="16" class="text-center">No Employee Found!</td>
                                @endif
                            </tr>
                        @endif
                        </tbody>
                        
                    </table>
                    @endforeach
                @elseif(($input['report_format'] == 1 && $format != null))
                    @php
                        if($format == 'as_unit_id'){
                            $head = 'Unit';
                        }elseif($format == 'as_line_id'){
                            $head = 'Line';
                        }elseif($format == 'as_floor_id'){
                            $head = 'Floor';
                        }elseif($format == 'as_department_id'){
                            $head = 'Department';
                        }elseif($format == 'as_designation_id'){
                            $head = 'Designation';
                        }elseif($format == 'as_section_id'){
                            $head = 'Section';
                        }elseif($format == 'as_subsection_id'){
                            $head = 'Sub Section';
                        }else{
                            $head = '';
                        }
                    @endphp
                    <table class="table table-bordered table-hover table-head" border="1" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" cellpadding="5">
                        <!-- custom design for all-->
                        @if($input['pay_status'] == 'all')
                            <thead>
                                <tr class="text-center">
                                    <th rowspan="2">Sl</th>
                                    <th rowspan="2"> {{ $head }} Name</th>
                                    <th colspan="3">No. of Employee</th>
                                    <th rowspan="2">OT Hour</th>
                                    
                                    <th colspan="5">Salary Amount (BDT)</th>
                                    <th colspan="5">Bank &amp; Cash (BDT)</th>
                                </tr>
                                <tr class="text-center">
                                    <th>Non OT</th>
                                    <th>OT</th>
                                    <th>Total</th>
                                    <th>Salary</th>
                                    <th>Wages</th>
                                    <th>OT Amount</th>
                                    <th>stamp</th>
                                    <th>Total</th>
                                    <th>Cash</th>
                                    <th>Stamp</th>
                                    <th>Bank</th>
                                    <th>Tax</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=0; $tNonOt = 0; $tOt = 0; $totalOtSalary =0; $totalNonOtSalary =0; $totalGroupSalary = 0; @endphp
                                @if(count($getEmployee) > 0)
                                @foreach($getEmployee as $employee)
                                @php 
                                    $groupTotalSalary = $employee->groupTotal-$employee->groupOtAmount;
                                    $nonOtSalary = $employee->totalNonOt;
                                    $otSalary = $groupTotalSalary - $nonOtSalary;

                                    $tNonOt += $employee->nonot; 
                                    $tOt += $employee->ot; 
                                    $totalNonOtSalary += $nonOtSalary;
                                    $totalOtSalary += $otSalary;
                                    $totalGroupStampSalary = $employee->groupTotal+$employee->groupStamp;
                                    $totalGroupSalary += $totalGroupStampSalary;
                                @endphp
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>
                                        @php
                                            $group = $employee->$format;
                                            if($format == 'as_unit_id'){
                                                $body = $unit[$group]['hr_unit_name']??'';
                                            }elseif($format == 'as_line_id'){
                                                $body = $line[$group]['hr_line_name']??'';
                                                
                                            }elseif($format == 'as_floor_id'){
                                                $body = $floor[$group]['hr_floor_name']??'';
                                                
                                            }elseif($format == 'as_department_id'){
                                                $body = $department[$group]['hr_department_name']??'';
                                                
                                            }elseif($format == 'as_designation_id'){
                                                $body = $designation[$group]['hr_designation_name']??'';
                                                
                                            }elseif($format == 'as_section_id'){
                                                $depId = $section[$group]['hr_section_department_id']??'';
                                                $seDeName = $department[$depId]['hr_department_name']??'';
                                                $seName = $section[$group]['hr_section_name']??'';
                                                $body = $seDeName.' - '.$seName;
                                                
                                            }elseif($format == 'as_subsection_id'){
                                                $body = $subSection[$group]['hr_subsec_name']??'';
                                                
                                            }else{
                                                $body = 'N/A';
                                            }
                                        @endphp
                                        {{ ($body == null)?'N/A':$body }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $employee->nonot }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $employee->ot }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $employee->total }}
                                        
                                    </td>
                                    <td class="text-right">
                                        {{ numberToTimeClockFormat($employee->groupOt) }}
                                    </td>

                                    <td class="text-right">
                                        {{ (round($nonOtSalary)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($otSalary)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupOtAmount)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupStamp)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupTotal+$employee->groupStamp)) }}
                                    </td>

                                    <td class="text-right">
                                        {{ (round($employee->groupCashSalary)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupStamp)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupBankSalary)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupTds)) }}
                                    </td>
                                    
                                    <td class="text-right">
                                        {{ (round($employee->groupTotal+$employee->groupStamp)) }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td class="text-center fwb" style="font-weight: bold; font-size:13px;"> Total </td>
                                    <td class="text-center fwb" style="font-weight: bold; font-size:13px;">{{ $tNonOt }}</td>
                                    <td class="text-center fwb" style="font-weight: bold; font-size:13px;">{{ $tOt }}</td>
                                    <td class="text-center fwb" style="font-weight: bold; font-size:13px;">{{ $totalEmployees }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ numberToTimeClockFormat(round($totalOtHour,2)) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ (round($totalNonOtSalary,2)) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ (round($totalOtSalary,2)) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ (round($totalOTAmount,2)) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ (round($totalStamp,2)) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ (round($totalGroupSalary,2)) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ (round($totalCashSalary,2)) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ (round($totalStamp,2)) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ (round($totalBankSalary,2)) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ (round($totalTax,2)) }}</td>
                                    <td class="text-right fwb" style="font-weight: bold; font-size:13px;">{{ (round($totalGroupSalary,2)) }}</td>
                                    
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-right fwb" style="font-weight: bold; font-size:13px; text-align: right"> Salary Payable <br>
                                        <span class="red">* Without stamp</span></td>
                                    <td colspan="3" class="text-center fwb" style="font-weight: bold; font-size:13px; text-align: center;">{{ (round(($totalNonOtSalary + $totalOtSalary + $totalOTAmount))) }}</td>
                                    <td colspan="7"></td>

                                </tr>
                                @else
                                <tr>
                                    <td colspan="14" class="text-center">No Data Found!</td>
                                </tr>
                                @endif
                            </tbody>
                        @else
                            <!-- custom design for cash/bank/partial -->
                            <thead>
                                <tr class="text-center">
                                    <th>Sl</th>
                                    <th> {{ $head }} Name</th>
                                    <th>No. Of Employee</th>
                                    @if($input['pay_status'] == 'all')
                                    <th>Salary Amount (BDT)</th>
                                    @endif
                                    @if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
                                    <th>Cash Amount (BDT)</th>
                                    @endif
                                    @if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
                                    <th>Bank Amount (BDT)</th>
                                    <th>Tax Amount (BDT)</th>
                                    @endif
                                    <th>OT Hour</th>
                                    <th>OT Amount (BDT)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=0; $totalEmployee = 0; @endphp
                                @if(count($getEmployee) > 0)
                                @foreach($getEmployee as $employee)
                                
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>
                                        @php
                                            $group = $employee->$format;
                                            if($format == 'as_unit_id'){
                                                $body = $unit[$group]['hr_unit_name']??'';
                                            }elseif($format == 'as_line_id'){
                                                $body = $line[$group]['hr_line_name']??'';
                                            }elseif($format == 'as_floor_id'){
                                                $body = $floor[$group]['hr_floor_name']??'';
                                            }elseif($format == 'as_department_id'){
                                                $body = $department[$group]['hr_department_name']??'';
                                            }elseif($format == 'as_designation_id'){
                                                $body = $designation[$group]['hr_designation_name']??'';
                                            }elseif($format == 'as_section_id'){
                                                $depId = $section[$group]['hr_section_department_id']??'';
                                                $seDeName = $department[$depId]['hr_department_name']??'';
                                                $seName = $section[$group]['hr_section_name']??'';
                                                $body = $seDeName.' - '.$seName;
                                            }elseif($format == 'as_subsection_id'){
                                                $body = $subSection[$group]['hr_subsec_name']??'';
                                            }else{
                                                $body = 'N/A';
                                            }
                                        @endphp
                                        {{ ($body == null)?'N/A':$body }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $employee->total }}
                                        @php $totalEmployee += $employee->total; @endphp
                                    </td>
                                    @if($input['pay_status'] == 'all')
                                    <td class="text-right">
                                        {{ (round($employee->groupSalary,2)) }}
                                    </td>
                                    @endif
                                    @if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
                                    <td class="text-right">
                                        {{ (round($employee->groupCashSalary,2)) }}
                                    </td>
                                    @endif
                                    @if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
                                    <td class="text-right">
                                        {{ (round($employee->groupBankSalary,2)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupTds,2)) }}
                                    </td>
                                    @endif
                                    <td class="text-right">
                                        {{ ($employee->groupOt) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupOtAmount,2)) }}
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="9" class="text-center">No Data Found!</td>
                                </tr>
                                @endif
                            </tbody>
                        @endif
                        
                    </table>
                @endif
            </div>
            <div class="bottom_summery_section">
                <div class="page-footer">
                    
                    <table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
                        @if($input['report_format'] == 1)
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th colspan="2" style="font-weight: bold; font-size:13px;">Total Employee</th>
                            <td style="text-align: right; font-size:13px;font-weight: bold;">{{ $totalEmployees }}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th colspan="2" style="font-weight: bold; font-size:13px;">Total Payable</th>
                            <td style="text-align: right; font-size:13px;font-weight: bold;">{{ $totalSalary }}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th colspan="2" style="font-weight: bold; font-size:13px;">Total Stamp</th>
                            <td style="text-align: right; font-size:13px;font-weight: bold;">{{ $totalStamp }}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th colspan="2" style="font-weight: bold; font-size:13px;">Total Salary </th>
                            <td style="text-align: right; font-size:13px;font-weight: bold;">{{ $totalSalary + $totalStamp }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- modal --}}
        
    </div>
</div>
