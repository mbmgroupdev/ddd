<div class="panel">
	<div class="panel-body">
		<div class="row">
			<div class="col-sm-3">Button</div>
			<div class="col-sm-6"></div>
			<div class="col-sm-3">filter</div>
		</div>
		<div class="content_list_section">
			@if($input['report_format'] == 0)
				<table class="table table-bordered table-hover table-head table-responsive" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
				@foreach($uniqueGroupEmp as $group => $employees)
				
					<thead>
						@if(count($employees) > 0)
		                
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
		                	<tr>
			                    <th colspan="2">{{ $head }}</th>
			                    <th colspan="12">{{ $body }}</th>
		                    </tr>
		                    @endif
		                
		                @endif
		                <tr>
		                    <th>Sl</th>
		                    <th>Associate ID</th>
		                    <th>Name</th>
		                    <th>Designation</th>
		                    <th>Department</th>
		                    <th>Bonus Month</th>
		                    <th>Payment Method</th>
		                    <th>Bonus Amount</th>
		                    @if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
		                    <th>Bank Amount</th>
		                    @endif
		                    @if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
		                    <th>Cash Amount</th>
		                    @endif
		                    <th>Stamp Amount</th>
		                    <th>Net Pay</th>
		                </tr>
		            </thead>
		            <tbody>
		            @php $i = 0; $otHourSum=0; $salarySum=0; $month = $input['month']; @endphp
		            @if(count($employees) > 0)
			            @foreach($employees as $employee)
			            	@php
			            		$designationName = $employee->hr_designation_name??'';
			            	@endphp
			            	
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	
				            	<td>{{ $employee->associate_id }}
				            	</td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            	</td>
				            	<td>{{ $designationName }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td></td>
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
				            	<td></td>
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
				            	</td>
			            	</tr>
			            	
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
		            	<tr style="border:0 !important;"><td colspan="16" style="border: 0 !important;height: 20px;"></td> </tr>
		            </tbody>
		            
				@endforeach
			</table>
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
								<th rowspan="2">Payable Salary</th>
								<th colspan="5">Salary segmentation (BDT)</th>
								<th rowspan="2">Net Pay Amount</th>
								<th colspan="3">Cash & Bank (BDT)</th>
							</tr>
							<tr class="text-center">
								<th>Non OT</th>
								<th>OT</th>
								<th>Total</th>
								<th>Salary</th>
								<th>Wages</th>
								<th>OT Amount</th>
								<th>Food Deduct</th>
								<th>stamp</th>
								<th>Cash</th>
								<th>Bank</th>
								<th>Tax</th>
							</tr>
						</thead>
						<tbody>
							@php $i=0; $tNonOt = 0; $tOt = 0; $totalOtSalary =0; $totalNonOtSalary =0; $totalGroupSalary = 0; $totalFoodDeduct = 0; $totalGroupPay = 0; @endphp
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
								
								$foodAmount = $employee->foodDeduct??0;
								$totalFoodDeduct += $foodAmount;	
								
								$totalGroupPay += $employee->groupTotal
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
											$exPar = '&selected='.$body;
										}elseif($format == 'as_floor_id'){
											$body = $floor[$group]['hr_floor_name']??'';
											$exPar = '&selected='.$body;
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
									{{ bn_money(round($employee->groupTotal+$employee->groupStamp+$foodAmount)) }}
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
								
								<td class="text-right">{{ bn_money(round($foodAmount))}}</td>
								
								<td class="text-right">
									{{ bn_money(round($employee->groupStamp)) }}
								</td>
								<td class="text-right" style="font-weight: bold">
									{{ bn_money(round($employee->groupTotal)) }}
								</td>

								<td class="text-right">
									{{ bn_money(round($employee->groupCashSalary)) }}
								</td>
								
								<td class="text-right">
									{{ bn_money(round($employee->groupBankSalary)) }}
								</td>
								<td class="text-right">
									{{ bn_money(round($employee->groupTds)) }}
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
								<td class="text-right fwb">{{ bn_money(round($totalGroupPay + $totalStamp + $totalFoodDeduct)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($totalNonOtSalary)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($totalOtSalary)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($totalOTAmount)) }}</td>
								
								<td class="text-right fwb">{{ bn_money(round($totalFoodDeduct)) }}</td>
								
								<td class="text-right fwb">{{ bn_money(round($totalStamp)) }}</td>
								
								<td class="text-right fwb" style="font-weight: bold">{{ bn_money(round($totalGroupPay)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($totalCashSalary)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($totalBankSalary)) }}</td>
								<td class="text-right fwb">{{ bn_money(round($totalTax)) }}</td>
								
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
</div>