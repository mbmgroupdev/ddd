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
                    @foreach($uniqueGroups as $group)
                    
                    <table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
                        <thead style="font-weight: bold; font-size:14px; text-align: center;">
                            @if(count($getEmployee) > 0)
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
                                <th style=" font-weight: bold; font-size:13px;" colspan="12">{{ $body }}</th>
                                @endif
                            </tr>
                            @endif
                            <tr>
                                <th style=" font-weight: bold; font-size:13px;">Sl</th>
                                <th style=" font-weight: bold; font-size:13px;">Associate ID</th>
                                <th style=" font-weight: bold; font-size:13px;">Name</th>
                                <th style=" font-weight: bold; font-size:13px;">Designation</th>
                                <th style=" font-weight: bold; font-size:13px;">Department</th>
                                <th style=" font-weight: bold; font-size:13px;">Present</th>
                                <th style=" font-weight: bold; font-size:13px;">Absent</th>
                                <th style=" font-weight: bold; font-size:13px;">OT Hour</th>
                                <th style=" font-weight: bold; font-size:13px;">Payment Method</th>
                                <th style=" font-weight: bold; font-size:13px;">Account No.</th>
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
                            </tr>
                        </thead>
                        <tbody>
                        @php $i = 0; $otHourSum=0; $salarySum=0; $month = $input['month']; @endphp
                        @if(count($getEmployee) > 0)
                            @foreach($getEmployee as $employee)
                                @php
                                    $designationName = $employee->hr_designation_name??'';
                                    $otHour = ($employee->ot_hour);
                                @endphp
                                @if($head == '')
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    
                                    <td>{{ $employee->associate_id }}</td>
                                    <td>
                                        <b>{{ $employee->as_name }}</b>
                                    </td>
                                    <td>{{ $designationName }}</td>

                                    <td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
                                    <td>{{ $employee->present }}</td>
                                    <td>{{ $employee->absent }}</td>
                                    <td><b>{{ number_format($otHour,2) }}</b></td>
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
                                    
                                </tr>
                                @else
                                @if($group == $employee->$format)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    
                                    <td>{{ $employee->associate_id }}</td>
                                    <td>
                                        <b>{{ $employee->as_name }}</b>
                                    </td>
                                    <td>{{ $designationName }}</td>
                                    <td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
                                    <td>{{ $employee->present }}</td>
                                    <td>{{ $employee->absent }}</td>
                                    <td><b>{{ number_format($otHour,2) }}</b></td>
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
                                    
                                </tr>
                                @endif
                                @endif
                            @endforeach
                        @else
                            <tr>
                                @if($input['pay_status'] == 'cash')
                                <td colspan="15" class="text-center">No Employee Found!</td>
                                @elseif($input['pay_status'] != 'cash' && $input['pay_status'] != 'all')
                                <td colspan="15" class="text-center">No Employee Found!</td>
                                @else
                                <td colspan="15" class="text-center">No Employee Found!</td>
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
                                    <th style=" font-weight: bold; font-size:13px;" rowspan="2">Sl</th>
                                    <th style=" font-weight: bold; font-size:13px;" rowspan="2"> {{ $head }} Name</th>
                                    <th style=" font-weight: bold; font-size:13px;" colspan="3">No. of Employee</th>
                                    <th style=" font-weight: bold; font-size:13px;" rowspan="2">Salary (BDT)</th>
                                    
                                    <th style=" font-weight: bold; font-size:13px;" colspan="2">Over Time</th>
                                    <th style=" font-weight: bold; font-size:13px;" colspan="5">Salary Payable (BDT)</th>
                                </tr>
                                <tr class="text-center">
                                    <th style=" font-weight: bold; font-size:13px;">Non OT</th>
                                    <th style=" font-weight: bold; font-size:13px;">OT</th>
                                    <th style=" font-weight: bold; font-size:13px;">Total</th>
                                    <th style=" font-weight: bold; font-size:13px;">Time (Hour)</th>
                                    <th style=" font-weight: bold; font-size:13px;">Amount (BDT)</th>
                                    <th style=" font-weight: bold; font-size:13px;">Cash</th>
                                    <th style=" font-weight: bold; font-size:13px;">Bank</th>
                                    <th style=" font-weight: bold; font-size:13px;">Tax</th>
                                    <th style=" font-weight: bold; font-size:13px;">Stamp</th>
                                    <th style=" font-weight: bold; font-size:13px;">Total</th>
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
                                        {{ $employee->nonot }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $employee->ot }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $employee->total }}
                                        @php $totalEmployee += $employee->total; @endphp
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupTotal-$employee->groupOtAmount)) }}
                                    </td>
                                    
                                    <td class="text-right">
                                        {{ ($employee->groupOt) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupOtAmount)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupCashSalary)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupBankSalary)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupTds)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupStamp)) }}
                                    </td>
                                    <td class="text-right">
                                        {{ (round($employee->groupTotal+$employee->groupStamp)) }}
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="9" class="text-center">No Data Found!</td>
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
