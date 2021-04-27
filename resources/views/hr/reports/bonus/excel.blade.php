<div class="panel">
	<div class="panel-body">
		@php
			$groupUnit = ($input['group_unit']??($input['unit']??''));
					@endphp
		<div class="content_list_section"  id="report_section">
			
			<div class="page-header report_section">
				
				<h3 style="font-weight: bold; text-align: center;"> {{ $unit[$groupUnit]['hr_unit_name']??'' }} </h3> 
				<h3 style=" text-align: center;">{{ $bonusType[$bonusSheet->bonus_type_id]['bonus_type_name']??''}}-{{ $bonusSheet->bonus_year }} @if($input['report_format'] == 0) Details @else Summary @endif Report </h3>
				<h5>Total Employee: {{$summary->active + $summary->maternity}}</h5>
				<h5>Total  Bonus : {{$summary->active_amount + $summary->maternity_amount}}</h5>
	            
            	
	        </div>
			
			@if($input['report_format'] == 0)
				<table class="table table-bordered table-hover table-head">
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
		                	@if($head != '' && $format != 'as_unit_id')
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
		                    <th>Unit</th>
		                    <th>Location</th>
		                    <th>Designation</th>
		                    <th>Department</th>
		                    <th>DOJ</th>
		                    <th>Gross</th>
		                    <th>Basic</th>
		                    <th>Month</th>
		                    <th>Bonus Amount</th>
		                    <th>Stamp</th>
		                    <th>Cash Amount</th>
			                <th>Bank Amount</th>
			                <th>Net Payable</th>
			                <th style=" font-weight: bold; font-size:13px;">Account No.</th>
                            <th>Last Bonus</th>
                            <th>Last Bonus Effect</th>
		                </tr>
		            </thead>
		            <tbody>
		            @php $i = 0; @endphp
		            @if(count($employees) > 0)
			            @foreach($employees as $employee)
			            	@php
			            		$designationName = $employee->hr_designation_name??'';
			            	@endphp
			            	
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	
				            	<td>{{ $employee->associate_id }}</td>
				            	<td><b>{{ $employee->as_name }}</b></td>
				            	<td>{{ $unit[$employee->as_unit_id]['hr_unit_short_name']??'' }}</td>
				            	<td>{{ $location[$employee->as_location]['hr_location_name']??'' }}</td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td >{{$employee->as_doj}}</td>
				            	<td>{{$employee->gross_salary }}</td>
				            	<td>{{$employee->basic}}</td>
				            	<td class="@if($employee->duration < 12) highlight @endif">
				            		@if($employee->duration < 12)
				            			{{$employee->duration}}/12
				            		
				            		@endif
				            	</td>
				            	
				            	<td>{{$employee->bonus_amount }}</td>
				            	<td>{{$employee->stamp }}</td>
				            	<td>{{$employee->cash_payable }}</td>
				            	<td>{{$employee->bank_payable }}</td>
				            	<td @if($employee->override == 1) style="background: yellow;" @endif>{{$employee->net_payable }}</td>
				            	<td>
                                    @if($employee->pay_status == 2)
                                        <b>{{ $employee->bank_no }}</b>
                                    @elseif($employee->pay_status == 3)
                                        <b>{{ $employee->bank_no }}</b>
                                    @endif
                                </td>
                                <td> @if($employee->override== 1) {{$employee->bonus_amount }} 
                                @endif</td>
                                <td> @if($employee->override== 1) 1 
                                @endif</td>
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
						@if(count($getEmployee) > 0)
						@foreach($getEmployee as $employee)
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
									$secUrl = $exPar;
								@endphp
								{{ ($body == null)?'N/A':$body }}
							</td>
							<td style="text-align: center;">
								{{ $employee->nonot }}
							</td>

							<td style="text-align: right;padding-right: 5px;">
								{{ $employee->totalNonOt }}
							</td>
							<td style="text-align: center;">
								{{ $employee->ot }}
							</td>

							<td style="text-align: right;padding-right: 5px;">
								{{ $employee->totalOt }}
							</td>
							<td style="text-align: center;">
								{{ $employee->total }}
							</td>

							<td style="text-align: right;padding-right: 5px;">
								{{ $employee->groupTotal }}
							</td>
						</tr>
						@endforeach
						<tr>
							<td colspan="2" class="text-center"> <b>Total</b> </td>
							<td class="text-center"><b>{{$summary->nonot}}</b></td>
							<td class="text-right"><b>{{bn_money($summary->nonot_amount)}}</b></td>
							<td class="text-center"><b>{{$summary->ot}}</b></td>
							<td class="text-right"><b>{{bn_money($summary->ot_amount)}}</b></td>
							<td class="text-center"><b>{{$summary->active + $summary->maternity}}</b></td>
							<td class="text-right"><b>{{$summary->active_amount + $summary->maternity_amount}}</b></td>
						</tr>
						
						@endif
					</tbody>
					
				</table>
			@endif
		</div>
	</div>
</div>

