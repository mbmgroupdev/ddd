<h2>Bonus Eligble List</h2>
<h5>Total Employee: {{$summary->active + $summary->maternity}}</h5>
<h5>Total  Bonus : {{$summary->active_amount + $summary->maternity_amount}}</h5>


@if($input['report_format'] == 0)
	<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
	@foreach($uniqueGroup as $group => $employees)	
		<thead>
			@if(count($employees) > 0 && $format != 'as_unit_id')
            
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
                <th >Sl</th>
                <th >Unit</th>
                <th >Associate ID</th>
                <th >Name</th>
                <th >Designation</th>
                <th >Department</th>
                <th >OT/Non OT</th>
                <th >Status</th>
                <th >DOJ</th>
                <th >Gross</th>
                <th >Basic</th>
                <th >Type</th>
                <th >Month</th>
                <th >Bonus Amount</th>
                <th >Stamp</th>
                <th >Cash Amount</th>
                <th >Bank Amount</th>
                <th >Net Payable</th>
            </tr>
        </thead>
        <tbody>
        @php $i = 0; $otHourSum=0; $salarySum=0; $month = $input['month']??''; @endphp
        @if(count($employees) > 0)
            @foreach($employees as $employee)
            	@php
            		$designationName = $employee->hr_designation_name??'';
            	@endphp
            	
            	<tr>
            		<td>{{ ++$i }}</td>
	            	
	            	<td>{{ $unit[$employee->as_unit_id]['hr_unit_short_name']??'' }}</td>
	            	<td>{{ $employee->associate_id }}</td>
	            	<td><b>{{ $employee->as_name }}</b></td>
	            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
	            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
	            	<td>{{ $employee->as_ot == 1?'OT':'Non OT' }}</td>
	            	<td>{{emp_status_name($employee->as_status)}}</td>
	            	<td>{{$employee->as_doj}}</td>
	            	<td>{{$employee->ben_current_salary}}</td>
	            	<td>{{$employee->ben_basic}}</td>
	            	<td>@if($employee->type != 'normal') {{$employee->type }} @endif</td>
	            	<td>
	            		@if($employee->month < 12)
							{{$employee->month}}
	            		@endif
	            	</td>
	            	<td>{{$employee->bonus_amount }}</td>
	            	<td>{{$employee->stamp }}</td>
	            	<td>{{$employee->cash_payable }}</td>
	            	<td>{{$employee->bank_payable }}</td>
	            	<td>{{$employee->net_payable }}</td>
            	</tr>
            	
            @endforeach
        @endif
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
		<thead>
			<tr class="text-center">
				<th rowspan="2" style="vertical-align: middle;">Sl</th>
				<th rowspan="2" style="vertical-align: middle;"> {{ $head }} Name</th>
				<th colspan="2">NonOT</th>
				<th colspan="2">OT</th>
				<th colspan="2">Total</th>
			</tr>
			<tr class="text-center">
				<th>Employee</th>
				<th>Amount</th>
				<th>Employee</th>
				<th>Amount</th>
				<th>Employee</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			@php $i = 0; @endphp
			@if(count($uniqueGroup) > 0)
			@foreach($uniqueGroup as $group => $employee)
			<tr>
				<td>{{ ++$i }}</td>
				<td>
					@php
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

				<td style="text-align: right;padding-right: 5px;">
					{{ bn_money($employee->nonot_amount) }}
				</td>
				<td style="text-align: center;">
					{{ $employee->ot }}
				</td>

				<td style="text-align: right;padding-right: 5px;">
					{{ bn_money($employee->ot_amount) }}
				</td>
				<td style="text-align: center;">
					{{ $employee->ot + $employee->nonot }}
				</td>

				<td style="text-align: right;padding-right: 5px;">
					{{ $employee->ot_amount + $employee->nonot_amount }}
				</td>
				
				
			</tr>
			@endforeach
			<tr>
				<th style="text-align: center;" colspan="2">Total</th>
				<th style="text-align: center;">{{collect($uniqueGroup)->sum('ot')}}</th>
				<th style="text-align: right;padding-right: 5px;">{{collect($uniqueGroup)->sum('ot_amount')}}</th>
				<th style="text-align: center;">{{collect($uniqueGroup)->sum('nonot')}}</th>
				<th style="text-align: right;padding-right: 5px;">{{collect($uniqueGroup)->sum('nonot_amount')}}</th>
				<th style="text-align: center;">{{collect($uniqueGroup)->sum('ot') + collect($uniqueGroup)->sum('nonot')}}</th>
				<th style="text-align: right;padding-right: 5px;">{{collect($uniqueGroup)->sum('ot_amount') + collect($uniqueGroup)->sum('nonot_amount')}}</th>
			</tr>
			
			@else
			<tr>
            	<td colspan="8" class="text-center">No Data Found!</td>
            </tr>
			@endif
		</tbody>
		
	</table>
@endif

