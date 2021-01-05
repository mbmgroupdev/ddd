<div class="panel">
	<div class="panel-body">
		
		@php
			$urldata = http_build_query($input) . "\n";
		@endphp
		<a href='{{ url("hr/reports/monthly-salary-excel?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 16px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>
		
		<div id="report_section" class="report_section">
			<style type="text/css" media="print">

				h4, h2, p{margin: 0;}
				.text-right{text-align:right;}
				.text-center{text-align:center;}
			</style>
			<style type="text/css">
              .table{
                width: 100%;
              }
              a{text-decoration: none;}
              .table-bordered {
                  border-collapse: collapse;
              }
              .table-bordered th,
              .table-bordered td {
                border: 1px solid #777 !important;
                padding:5px;
              }
              .no-border td, .no-border th{
                border:0 !important;
                vertical-align: top;
              }
              .f-14 th, .f-14 td, .f-14 td b{
                font-size: 14px !important;
              }
              .table thead th {
			    vertical-align: inherit;
				}
				#content-result .panel .panel-body .loader-p{
					margin-top: 20% !important;
				} 
				.modal-h3{
					line-height: 1;
				}
			</style>
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
		            
		            
		            <table class="table no-border f-14" border="0" style="width:100%;margin-bottom:0;font-size:14px;text-align:left"  cellpadding="5">
		            	<tr>
		            		<td width="32%">
		            			@if($input['unit'] != null)
		            			Unit <b>: {{ $unit[$input['unit']]['hr_unit_name'] }}</b> <br>
		            			@endif
		            			@if($input['location'] != null)
		            			Location <b>: {{ $location[$input['location']]['hr_location_name'] }}</b> <br>
		            			@endif
		            			@if($input['area'] != null)
		            			Area 
		                			<b>: {{ $area[$input['area']]['hr_area_name'] }}</b> <br>
		                		@endif
		                		@if($input['department'] != null)
		                			Department 
		                			<b>: {{ $department[$input['department']]['hr_department_name'] }}</b> <br>
		                		@endif
		                		@if($input['section'] != null)
		                		Section 
		                			<b>: {{ $section[$input['section']]['hr_section_name'] }}</b>
		                		@endif
		            		</td>
		            		<td>
		            			<p style="text-align: center; font-size: 14px;">Month : {{ date('M Y', strtotime($input['month'])) }} </p>
					            <p style="text-align: center; font-size: 14px;">Total Employee : {{ $totalEmployees }} </p>
					            @if($input['pay_status'] == 'all')
					            <p style="text-align: center; font-size: 14px;">Total Payable : {{ bn_money(round($totalSalary,2)) }} </p>
	                			@endif
		            		</td>
		            		<td>
		            			@if($input['subSection'] != null)
		            			Sub-section <b>: {{ $subSection[$input['subSection']]['hr_subsec_name'] }}</b><br>
		            			@endif
		            			@if($input['floor_id'] != null)
		                			Floor 
		                			<b>: {{ $floor[$input['floor_id']]['hr_floor_name'] }}</b><br>
		                		@endif
		                		@if($input['line_id'] != null)
		                		Line 
		                			<b>: {{ $line[$input['line_id']]['hr_line_name'] }}</b> <br>
		                		@endif
		                		Format 
		                			<b class="capitalize">: {{ isset($formatHead[1])?$formatHead[1]:'N/A' }}</b> <br>
	                			@if($input['otnonot'] != null)
		                			<b> OT </b> 
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
		                		@if($input['pay_status'] != null)
		                		Payment Type 
		                			<b class="capitalize">: {{ $input['pay_status'] }}</b> <br>
		                		@endif
		            		</td>
		            	</tr>
		            	
		            </table>
		            
		        </div>
		        @else
		        <div class="page-header-summery">
        			
        			<h2>{{ date('M Y', strtotime($input['month'])) }} Salary Summary Report </h2>
        			<h4>Unit: {{ $unit[$input['unit']]['hr_unit_name'] }}</h4>
        			@if($input['area'] != null)
        			<h4>Area: {{ $area[$input['area']]['hr_area_name'] }}</h4>
        			@endif
        			@if($input['department'] != null)
        			<h4>Department: {{ $department[$input['department']]['hr_department_name'] }}</h4>
        			@endif

        			@if($input['section'] != null)
        			<h4>Section: {{ $section[$input['section']]['hr_section_name'] }}</h4>
        			@endif

        			@if($input['subSection'] != null)
        			<h4>Sub Section: {{ $subSection[$input['subSection']]['hr_subsec_name'] }}</h4>
        			@endif

        			@if($input['floor_id'] != null)
        			<h4>Floor: {{ $floor[$input['floor_id']]['hr_floor_name'] }}</h4>
        			@endif

        			@if($input['line_id'] != null)
        			<h4>Line: {{ $line[$input['line_id']]['hr_line_name'] }}</h4>
        			@endif
        			@if($input['otnonot'] != null)
        			<h4>OT: @if($input['otnonot'] == 0) No @else Yes @endif </h4>
        			@endif
        			<h4>Total Employee: <b>{{ $totalEmployees }}</b></h4>
        			<h4>Total Salary: <b>{{ bn_money(round($totalSalary,2)) }}</b></h4>
        			<h4>Total OT Hour: <b>{{ numberToTimeClockFormat(round($totalOtHour,2)) }}</b></h4>
        			<h4>Total OT Amount: <b>{{ bn_money(round($totalOTAmount,2)) }}</b></h4>
		            		
		        </div>
		        @endif
			</div>

			<div class="content_list_section">
				@if($input['report_format'] == 0)
					@foreach($uniqueGroups as $group)
					
					<table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
						<thead>
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
			                    <th colspan="2">{{ $head }}</th>
			                    <th colspan="14">{{ $body }}</th>
			                    @endif
			                </tr>
			                @endif
			                <tr>
			                    <th>Sl</th>
			                    
			                    <th>Associate ID</th>
			                    <th>Name</th>
			                    <th>Designation</th>
			                    <th>Department</th>
			                    <th>Present</th>
			                    <th>Absent</th>
			                    <th>OT Hour</th>
			                    <th>Payment Method</th>
			                    <th>Payable Salary</th>
			                    @if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
			                    <th>Bank Amount</th>
			                    <th>Tax Amount</th>
			                    @endif
			                    @if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
			                    <th>Cash Amount</th>
			                    @endif
			                    <th>Stamp Amount</th>
			                    <th>Net Pay</th>
			                    <th>&nbsp;</th>
			                </tr>
			            </thead>
			            <tbody>
			            @php $i = 0; $otHourSum=0; $salarySum=0; $month = $input['month']; @endphp
			            @if(count($getEmployee) > 0)
				            @foreach($getEmployee as $employee)
				            	@php
				            		$designationName = $employee->hr_designation_name??'';
			                        $otHour = numberToTimeClockFormat($employee->ot_hour);
				            	@endphp
				            	@if($head == '')
				            	<tr>
				            		<td>{{ ++$i }}</td>
					            	
					            	<td><a href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank">{{ $employee->associate_id }}</a></td>
					            	<td>
					            		<b>{{ $employee->as_name }}</b>
					            	</td>
					            	<td>{{ $designationName }}</td>

					            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
					            	<td>{{ $employee->present }}</td>
					            	<td>{{ $employee->absent }}</td>
					            	<td><b>{{ $otHour }}</b></td>
					            	<td>
					            		@if($employee->pay_status == 1)
					            			Cash
					            		@elseif($employee->pay_status == 2)
					            		<b>{{ $employee->bank_name }}</b>
					            		<b>{{ $employee->bank_no }}</b>
					            		@else
					            		Bank & Cash
					            		<b>{{ $employee->bank_no }}</b>
					            		@endif
					            	</td>
					            	<td>
					            		@php $totalPay = $employee->total_payable + $employee->stamp; @endphp
					            		{{ bn_money($totalPay) }}
					            	</td>	
					            	@if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
					            	<td>{{ bn_money($employee->bank_payable) }}</td>
					            	<td>{{ bn_money($employee->tds) }}</td>
					            	@endif
					            	@if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
					            	<td>{{ bn_money($employee->cash_payable + $employee->stamp) }}</td>
					            	@endif
					            	<td>{{ bn_money($employee->stamp) }}</td>
					            	
					            	<td>
					            		@php
					            			if($input['pay_status'] == 'cash'){
					            				$totalNet = $employee->cash_payable;
					            			}else{
					            				$totalNet = $employee->total_payable - $employee->tds;
					            			}
					            		@endphp
					            		{{ bn_money($totalNet) }}
					            	</td>
					            	<td>
					            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-yearmonth="{{ $input['month'] }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Employee Salary Report' ><i class="fa fa-eye"></i></button>
					            	</td>
				            	</tr>
				            	@else
				            	@if($group == $employee->$format)
				            	<tr>
				            		<td>{{ ++$i }}</td>
					            	
					            	<td><a href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank">{{ $employee->associate_id }}</a></td>
					            	<td>
					            		<b>{{ $employee->as_name }}</b>
					            	</td>
					            	<td>{{ $designationName }}</td>
					            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
					            	<td>{{ $employee->present }}</td>
					            	<td>{{ $employee->absent }}</td>
					            	<td><b>{{ $otHour }}</b></td>
					            	<td>
					            		@if($employee->pay_status == 1)
					            			Cash
					            		@elseif($employee->pay_status == 2)
					            		<b class="uppercase">{{ $employee->bank_name }}</b>
					            		<br>
					            		<b>{{ $employee->bank_no }}</b>
					            		@else
					            		<b class="uppercase">{{ $employee->bank_name }}</b> & Cash
					            		<br>
					            		<b>{{ $employee->bank_no }}</b>
					            		@endif
					            	</td>
					            	<td>
					            		@php $totalPay = $employee->total_payable + $employee->stamp; @endphp
					            		{{ bn_money($totalPay) }}
					            	</td>	
					            	@if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
					            	<td>{{ bn_money($employee->bank_payable) }}</td>
					            	<td>{{ bn_money($employee->tds) }}</td>
					            	@endif
					            	@if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
					            	<td>{{ bn_money($employee->cash_payable) }}</td>
					            	@endif
					            	<td>{{ bn_money($employee->stamp) }}</td>
					            	<td>
					            		@php
					            			if($input['pay_status'] == 'cash'){
					            				$totalNet = $employee->cash_payable;
					            			}else{
					            				$totalNet = $employee->total_payable - $employee->tds;
					            			}
					            		@endphp
					            		{{ bn_money($totalNet) }}
					            	</td>
					            	<td>
					            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-yearmonth="{{ $input['month'] }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Employee Salary Report' ><i class="fa fa-eye"></i></button>
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
									<th rowspan="2">Sl</th>
									<th rowspan="2"> {{ $head }} Name</th>
									<th colspan="3">No. of Employee</th>
									<th rowspan="2">OT Hour</th>
									
									<th colspan="5">Salary Amount (BDT)</th>
									<th colspan="5">Bank & Cash (BDT)</th>
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
												$exPar = '&selected='.$unit[$group]['hr_unit_id']??'';
											}elseif($format == 'as_line_id'){
												$body = $line[$group]['hr_line_name']??'';
												$exPar = '&selected='.$line[$group]['hr_line_id']??'';
											}elseif($format == 'as_floor_id'){
												$body = $floor[$group]['hr_floor_name']??'';
												$exPar = '&selected='.$floor[$group]['hr_floor_name']??'';
											}elseif($format == 'as_department_id'){
												$body = $department[$group]['hr_department_name']??'';
												$exPar = '&selected='.$department[$group]['hr_department_id']??'';
											}elseif($format == 'as_designation_id'){
												$body = $designation[$group]['hr_designation_name']??'';
												$exPar = '&selected='.$designation[$group]['hr_designation_id']??'';
											}elseif($format == 'as_section_id'){
												$depId = $section[$group]['hr_section_department_id']??'';
												$seDeName = $department[$depId]['hr_department_name']??'';
												$seName = $section[$group]['hr_section_name']??'';
												$body = $seDeName.' - '.$seName;
												$exPar = '&selected='.$section[$group]['hr_section_id']??'';
											}elseif($format == 'as_subsection_id'){
												$body = $subSection[$group]['hr_subsec_name']??'';
												$exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
											}else{
												$body = 'N/A';
												$exPar = '';
											}
											$secUrl = $urldata.$exPar;
										@endphp
										<a onClick="selectedGroup(this.id, '{{ $body }}')" data-body="{{ $body }}" id="{{$exPar}}" class="select-group">{{ ($body == null)?'N/A':$body }}</a>
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
										{{ bn_money(round($nonOtSalary)) }}
									</td>
									<td class="text-right">
										{{ bn_money(round($otSalary)) }}
									</td>
									<td class="text-right">
										{{ bn_money(round($employee->groupOtAmount)) }}
									</td>
									<td class="text-right">
										{{ bn_money(round($employee->groupStamp)) }}
									</td>
									<td class="text-right">
										{{ bn_money(round($employee->groupTotal+$employee->groupStamp)) }}
									</td>

									<td class="text-right">
										{{ bn_money(round($employee->groupCashSalary)) }}
									</td>
									<td class="text-right">
										{{ bn_money(round($employee->groupStamp)) }}
									</td>
									<td class="text-right">
										{{ bn_money(round($employee->groupBankSalary)) }}
									</td>
									<td class="text-right">
										{{ bn_money(round($employee->groupTds)) }}
									</td>
									
									<td class="text-right">
										{{ bn_money(round($employee->groupTotal+$employee->groupStamp)) }}
									</td>
								</tr>
								@endforeach
								<tr>
									<td></td>
									<td class="text-center fwb"> Total </td>
									<td class="text-center fwb">{{ $tNonOt }}</td>
									<td class="text-center fwb">{{ $tOt }}</td>
									<td class="text-center fwb">{{ $totalEmployees }}</td>
									<td class="text-right fwb">{{ numberToTimeClockFormat(round($totalOtHour,2)) }}</td>
									<td class="text-right fwb">{{ bn_money(round($totalNonOtSalary,2)) }}</td>
									<td class="text-right fwb">{{ bn_money(round($totalOtSalary,2)) }}</td>
									<td class="text-right fwb">{{ bn_money(round($totalOTAmount,2)) }}</td>
									<td class="text-right fwb">{{ bn_money(round($totalStamp,2)) }}</td>
									<td class="text-right fwb">{{ bn_money(round($totalGroupSalary,2)) }}</td>
									<td class="text-right fwb">{{ bn_money(round($totalCashSalary,2)) }}</td>
									<td class="text-right fwb">{{ bn_money(round($totalStamp,2)) }}</td>
									<td class="text-right fwb">{{ bn_money(round($totalBankSalary,2)) }}</td>
									<td class="text-right fwb">{{ bn_money(round($totalTax,2)) }}</td>
									<td class="text-right fwb">{{ bn_money(round($totalGroupSalary,2)) }}</td>
									
								</tr>
								<tr>
									<td colspan="6" class="text-right fwb"> Salary Payable <br>
										<span class="red">* Without stamp</span></td>
									<td colspan="3" class="text-center fwb">{{ bn_money(round(($totalNonOtSalary + $totalOtSalary + $totalOTAmount))) }}</td>
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
										{{ bn_money(round($employee->groupSalary,2)) }}
									</td>
									@endif
									@if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
									<td class="text-right">
										{{ bn_money(round($employee->groupCashSalary,2)) }}
									</td>
									@endif
									@if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
									<td class="text-right">
										{{ bn_money(round($employee->groupBankSalary,2)) }}
									</td>
									<td class="text-right">
										{{ bn_money(round($employee->groupTds,2)) }}
									</td>
									@endif
									<td class="text-right">
										{{ numberToTimeClockFormat($employee->groupOt) }}
									</td>
									<td class="text-right">
										{{ bn_money(round($employee->groupOtAmount,2)) }}
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
		</div>

		{{-- modal employee salary --}}
		<div class="item_details_section">
		    <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
		      <div class="item_details_dialog show_item_details_modal" style="min-height: 115px;">
		        <div class="fade-box-details fade-box">
		          <div class="inner_gray clearfix">
		            <div class="inner_gray_text text-center" id="heading">
		             <h5 class="no_margin text-white">{{ date('M Y', strtotime($input['month'])) }} Salary</h5>   
		            </div>
		            <div class="inner_gray_close_button">
		              <a class="cancel_details item_modal_close" role="button" rel='tooltip' data-tooltip-location='left' data-tooltip="Close Modal">Close</a>
		            </div>
		          </div>

		          <div class="inner_body" id="modal-details-content" style="display: none">
		            <div class="inner_body_content">
		               	<div class="body_top_section">
		               		<h3 class="text-center modal-h3"><strong>Name :</strong> <b id="eName"></b></h3>
		               		<h3 class="text-center modal-h3"><strong>Id :</strong> <b id="eId"></b></h3>
		               		<h3 class="text-center modal-h3"><strong>Designation :</strong> <b id="eDesgination"></b></h3>
		               	</div>
		               	<div class="body_content_section">
			               	<div class="body_section" id="employee-salary">
			               		
			               	</div>
		               	</div>
		            </div>
		            <div class="inner_buttons">
		              <a class="cancel_modal_button cancel_details" role="button"> Close </a>
		            </div>
		          </div>
		        </div>
		      </div>
		    </div>
		</div>
		{{--  --}}
	</div>
</div>

<div class="modal right fade" id="right_modal_lg" tabindex="-1" role="dialog" aria-labelledby="right_modal_lg">
  <div class="modal-dialog modal-lg right-modal-width" role="document" > 
    <div class="modal-content">
      <div class="modal-header">
      	<a class="view prev_btn" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
			<i class="las la-chevron-left"></i>
		</a>
        <h5 class="modal-title right-modal-title text-center" id="modal-title-right"> &nbsp; </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="modal-content-result" id="content-result">
        	
        </div>
      </div>
      
    </div>
  </div>
</div>

<script type="text/javascript">
    var loaderModal = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:10px;" class="loader-p"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
    $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
    $(".overlay-modal, .item_details_dialog").removeAttr("style");
    /*Set min height to 90px after  has been set*/
    detailsheight = $(".item_details_dialog").css("min-height", "115px");
    var months    = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
    $(document).on('click','.yearly-activity',function(){
    	$("#employee-salary").html(loaderModal);
        let id = $(this).data('id');
        let associateId = $(this).data('eaid');
        let name = $(this).data('ename');
        let designation = $(this).data('edesign');
        let yearMonth = $(this).data('yearmonth');
        $("#eName").html(name);
        $("#eId").html(associateId);
        $("#eDesgination").html(designation);
        /*Show the dialog overlay-modal*/
        $(".overlay-modal-details").show();
        $(".inner_body").show();
        // ajax call
        $.ajax({
            url: '/hr/reports/employee-salary-modal',
            type: "GET",
            data: {
                as_id: associateId,
                year_month: yearMonth
            },
            success: function(response){
            	// console.log(response);
                if(response !== 'error'){
                	setTimeout(function(){
                		$("#employee-salary").html(response);
                	}, 1000);
                }else{
                	console.log(response);
                }
            }
        });
        /*Animate Dialog*/
        $(".show_item_details_modal").css("width", "225").animate({
          "opacity" : 1,
          height : detailsheight,
          width : "70%"
        }, 600, function() {
          /*When animation is done show inside content*/
          $(".fade-box").show();
        });
        // 
        
    });
    
    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });
    function selectedGroup(e, body){
    	var part = e;
    	var input = @json($urldata);
    	var pareUrl = input+part;
    	$("#modal-title-right").html(' '+body+' Salary Details');
    	$('#right_modal_lg').modal('show');
    	$("#content-result").html(loaderModal);
    	$.ajax({
            url: '/hr/reports/group-salary-sheet-details?'+pareUrl,
            data: {
                body: body
            },
            type: "GET",
            success: function(response){
            	// console.log(response);
                if(response !== 'error'){
                	setTimeout(function(){
                		$("#content-result").html(response);
                	}, 1000);
                }else{
                	console.log(response);
                }
            }
        });

    }
    function printDiv(divName)
    {   
        var mywindow=window.open('','','width=800,height=800');
        
        mywindow.document.write('<html><head><title>Print Contents</title>');
        mywindow.document.write('<style>@page {size: landscape; color: color;} </style>');
        mywindow.document.write('</head><body>');
        mywindow.document.write(document.getElementById(divName).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close();  
        mywindow.focus();           
        mywindow.print();
        mywindow.close();
    }
</script>