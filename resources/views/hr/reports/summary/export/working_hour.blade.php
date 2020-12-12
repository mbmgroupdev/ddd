<div class="panel">
	<div class="panel-body">
		<div class="report_section" id="report_section">
			@php
				$formatHead = explode('_',$format);
			@endphp
			
			<div class="top_summery_section">
				
		        <div class="page-header-summery">
        			
        			<h2 style="text-align: center;">Working Hour Report </h2>
        			<h3 style="text-align: center;">Date {{$input['from_date']}} to {{$input['to_date']}}</h3>
        			@if($input['unit'] == 145)
        				<h4>Unit: MBM + MBW + MBM 2</h4>
        			@else
        			<h4  style="text-align: center;">Unit: {{ $unit[$input['unit']]['hr_unit_name'] }}</h4>
        			@endif
        			<h4  style="text-align: center;">
	        			@if($input['area'] != null)
	        			@endif
	        			@if($input['department'] != null)
	        				Department: {{ $department[$input['department']]['hr_department_name'] }}
	        			@endif

	        			@if($input['section'] != null)
	        			Section: {{ $section[$input['section']]['hr_section_name'] }}
	        			@endif

	        			@if($input['subSection'] != null)
	        			Sub Section: {{ $subSection[$input['subSection']]['hr_subsec_name'] }}
	        			@endif

	        			@if($input['floor_id'] != null)
	        			Floor: {{ $floor[$input['floor_id']]['hr_floor_name'] }}
	        			@endif

	        			@if($input['line_id'] != null)
	        			Line: {{ $line[$input['line_id']]['hr_line_name'] }}
	        			@endif
        			</h4>
        			<h4>Total Employee: {{ $totalEmployees }}</h4>
        			<h4> Total Working Hour: {{ number_format($totalValue,2, '.', '')  }} </h4>
        			<h4></h4>
		            		
		        </div>
			</div>
			<div class="content_list_section" >
				@if($input['report_format'] == 0)
					@foreach($uniqueGroups as $group)
					
					<table class="table table-bordered table-hover table-head" border="1">
						<thead >
							@if(count($getEmployee) > 0)
			                <tr>
			                	@php
									if($format == 'as_line_id'){
										$head = 'Line';
										$body = $line[$group]['hr_line_name']??'N/A';
									}elseif($format == 'as_floor_id'){
										$head = 'Floor';
										$body = $floor[$group]['hr_floor_name']??'N/A';
									}elseif($format == 'as_department_id'){
										$head = 'Department';
										$body = $department[$group]['hr_department_name']??'N/A';
									}elseif($format == 'as_section_id'){
										$head = 'Section';
										$body = $section[$group]['hr_section_name']??'N/A';
									}elseif($format == 'as_subsection_id'){
										$head = 'Sub Section';
										$body = $subSection[$group]['hr_subsec_name']??'N/A';
									}elseif($format == 'as_designation_id'){
										$head = 'Designation';
										$body = $designation[$group]['hr_designation_name']??'N/A';
									}elseif($format == 'ot_hour'){
										$head = 'OT Hour';
										
					                    $otHourBody = $group;
										$body = $otHourBody??'N/A';
									}else{
										$head = '';
									}
								@endphp
			                	@if($head != '')
			                    <th colspan="2" style="font-weight: bold;">{{ $head }}</th>
			                    <th colspan="11" style="font-weight: bold;">{{ $body }}</th>
			                    @endif
			                </tr>
			                @endif
			                <tr >
			                    <th style="font-weight: bold;" >Sl</th>
			                    <th style="font-weight: bold;" >Associate ID</th>
			                    <th style="font-weight: bold;" >Name  Phone</th>
			                    <th style="font-weight: bold;" >Oracle ID</th>
			                    <th style="font-weight: bold;" >Designation</th>
			                    <th style="font-weight: bold;" >Department</th>
			                    <th style="font-weight: bold;" >Section</th>
			                    <th style="font-weight: bold;" >Sub Section</th>
			                    <th style="font-weight: bold;" >Floor</th>
			                    <th style="font-weight: bold;" >Line</th>
			                    <th style="font-weight: bold;" >Days</th>
			                    <th style="font-weight: bold;" >Working Hour</th>
			                </tr>
			            </thead>
			            <tbody>
			            @php
			             $i = 0; $month = date('Y-m',strtotime($input['from_date'])); 
			             $totalWH=0; $totalPay = 0;
			            @endphp
			            @if(count($getEmployee) > 0)
			            @foreach($getEmployee as $employee)
			            	@php
			            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
			            		
			                    $hourDuration  = number_format($employee->hourDuration/60,2,'.','');

			            	@endphp
			            	@if($head == '')
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	{{-- <td><img src="{{ emp_profile_picture($employee) }}" class='small-image' style="height: 40px; width: auto;"></td> --}}
				            	<td>{{ $employee->associate_id }}</td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            		<p>{{ $employee->as_contact }}</p>
				            	</td>
				            	<td>{{ $employee->as_oracle_code }}</td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
				            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
				            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
				            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            	<td style="text-align: center;">{{$employee->days}}</td>
				            	<td>{{ number_format($hourDuration,2, '.', '') }}</td>
				            	<td style="text-align: right;"></td>
				            
			            	</tr>
			            	@php 
			            		$totalWH += $hourDuration; 
			            	@endphp
			            	@else
				            	@if($group == $employee->$format)
				            	<tr>
				            		<td>{{ ++$i }}</td>
					            	{{-- <td><img src="{{ emp_profile_picture($employee) }}" class='small-image' style="height: 40px; width: auto;"></td> --}}
					            	<td>{{ $employee->associate_id }}</td>
					            	<td>
					            		<b>{{ $employee->as_name }}</b>
					            		<p>{{ $employee->as_contact }}</p>
					            	</td>
					            	<td>{{ $employee->as_oracle_code }}</td>
					            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
					            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
					            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
					            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
					            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
					            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            		<td style="text-align: center;">{{$employee->days}}</td>

					            	<td style="text-align: right">{{ number_format($hourDuration,2, '.', '') }}</td>
				            	</tr>
				            	@php 
				            		$totalWH += $hourDuration; 
				            	@endphp
				            	@endif
			            	@endif
			            @endforeach
			            	<tr>
			            		<td colspan="11" style="text-align: right;font-weight: bold;"><b>Total</b></td>
			            		<td  style="text-align: right;font-weight: bold;">
			            			<b> {{number_format($totalWH,2, '.', '')}} </b>
			            		</td>

			            	</tr>
			            @else
			            @endif
			            </tbody>
					</table>
					@endforeach
				@elseif(($input['report_format'] == 1 and $format != null))
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
						}elseif($format == 'ot_hour'){
							$head = 'Hour';
						}else{
							$head = '';
						}
					@endphp
					<table class="table table-bordered table-hover table-head" border="1">
						<thead>
							<tr>
								<th>Sl</th>
								@if($format == 'as_section_id' || $format == 'as_subsection_id')
								<th>Department Name</th>
								@endif
								@if($format == 'as_subsection_id')
								<th>Section Name</th>
								@endif
								<th> {{ $head }} {{ $format != 'ot_hour'?'Name':'' }}</th>
								<th>No of Employee</th>
								<th>Total Working Hour</th>
							</tr>
						</thead>
						<tbody>
							@php $i=0; @endphp
							
							@if(count($getEmployee) > 0)
								@foreach($getEmployee as $employee)
									@php $group = $employee->$format; @endphp
									<tr>
										<td>{{ (++$i) }}</td>
										@if($format == 'as_section_id' || $format == 'as_subsection_id')
										<td>
											@php
												if($format == 'as_subsection_id'){
													$getDepar = $subSection[$group]['hr_subsec_department_id']??'';
												}else{
													$getDepar = $section[$group]['hr_section_department_id']??'';
												}
												$departmentName =  $department[$getDepar]['hr_department_name']??'';
											@endphp
											{{$departmentName }}
										</td>
										@endif
										@if($format == 'as_subsection_id')
										<td>
											@php
												$sectionName = '';
												if(isset($subSection[$group])){
													$getSec = $subSection[$group]['hr_subsec_section_id']??'';
													if(isset($section[$getSec])){
														$sectionName =  $section[$getSec]['hr_section_name']??'';
													}

												}
											@endphp
											{{$sectionName}}
										</td>
										@endif
										<td>
											@php
												if($format == 'as_unit_id'){
													if($group == 145){
														$body = 'MBM + MBF + MBM 2';
													}else{
														$body = $unit[$group]['hr_unit_name']??'';
													}
												}elseif($format == 'as_line_id'){
													$body = $line[$group]['hr_line_name']??'';
												}elseif($format == 'as_floor_id'){
													$body = $floor[$group]['hr_floor_name']??'';
												}elseif($format == 'as_department_id'){
													$body = $department[$group]['hr_department_name']??'';
												}elseif($format == 'as_section_id'){
													$body = $section[$group]['hr_section_name']??'N/A';
												}elseif($format == 'as_subsection_id'){
													$body = $subSection[$group]['hr_subsec_name']??'';
												}elseif($format == 'as_designation_id'){
													$body = $designation[$group]['hr_designation_name']??'';
												}elseif($format == 'ot_hour'){
													
								                    $otHourBody = $group;
													$body = $otHourBody??'N/A';
												}else{
													$body = 'N/A';
												}
											@endphp
											{{ (($body == null)?'N/A':$body) }}
										</td>
										<td style="text-align: right">
											{{ $employee->total }}
										</td>
										<td style="text-align: right">
											
											@php $wh = $employee->groupHourDuration/60; @endphp
											{{number_format($wh,2, '.', '')}}
										</td>
									</tr>
								@endforeach
							@endif
						</tbody>
						
					</table>
				@endif
			</div>
			<div class="bottom_summery_section">
                <div class="page-footer">
                    
                    <div class="bottom_summery_section">
		                <div class="page-footer">
		                    
		                    <table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
		                        <tr>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            @if($input['report_format'] == 0)
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            @endif
		                            <th colspan="2" style="font-weight: bold; font-size:13px;">Total Employee</th>
		                            <td style="text-align: right; font-size:13px;font-weight: bold;">{{ $totalEmployees }}</td>
		                        </tr>
		                        <tr>
		                            <th></th>
		                            <th></th>
		                            @if($input['report_format'] == 0)
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            <th></th>
		                            @endif
		                            <th></th>
		                            <th colspan="2" style="font-weight: bold; font-size:13px;">Total Working Hour</th>
		                            <td style="text-align: right; font-size:13px;font-weight: bold;">{{ number_format($totalValue,2, '.', '')  }}</td>
		                        </tr>
		                    </table>
		                </div>
		            </div>
                </div>
            </div>
		</div>

	</div>
</div>
