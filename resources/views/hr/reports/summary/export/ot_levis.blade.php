<div class="report_section" id="report_section">
	@php
		$formatHead = explode('_',$format);
	@endphp
	
	<div class="top_summery_section">
		<div class="page-header">
            <h2 style="text-align: center;">Over Time Summary </h2>
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
			<h4>Total Employee: {{ $totalEmployees }} </h4>
        </div>
    </div>
    <div class="content_list_section" >
    	@if($input['report_format'] == 0)

			@foreach($uniqueGroups as $group => $employees)
			
			<table class="table table-bordered table-hover table-head table-responsive" border="1">
				<thead>
					@if(count($employees) > 0)
	                <tr class="table-title">
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
							}elseif($format == 'as_unit_id'){
								$head = 'Unit';
								$body = $unit[$group]['hr_unit_name']??'N/A';
							}elseif($format == 'ot_hour'){
								$head = 'OT Hour';
								
			                    $otHourBody = $group;
								$body = $otHourBody??'N/A';
							}else{
								$head = '';
							}
						@endphp
	                	@if($head != '')
	                    <th colspan="2">{{ $head }}</th>
	                    <th colspan="10">{{ $body }}</th>
	                    @endif
	                </tr>
	                @endif
	                <tr>
	                    <th style="font-weight: bold;">Sl</th>
	                    <th style="font-weight: bold;">Associate ID</th>
	                    <th style="font-weight: bold;">Name &amp; Phone</th>
	                    <th style="font-weight: bold;">Oracle ID</th>
	                    <th style="font-weight: bold;">Designation</th>
	                    <th style="font-weight: bold;">Department</th>
	                    <th style="font-weight: bold;">Section</th>
	                    <th style="font-weight: bold;">Sub Section</th>
	                    <th style="font-weight: bold;">Floor</th>
	                    <th style="font-weight: bold;">Line</th>
	                    <th style="font-weight: bold;">Days</th>
	                    <th style="font-weight: bold;">OT Hour</th>
	                </tr>
	            </thead>
	            <tbody>
	            @php
	             $i = 0; $month = date('Y-m',strtotime($input['from_date'])); 
	             $totalOt=0; $totalPay = 0;
	            @endphp
	            @if(count($employees) > 0)
	            @foreach($employees as $employee)
	            	@php
	            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
	            		
	                    $otHour = $employee->ot_hour;
	                    

	            	@endphp
	            	<tr>
	            		<td>{{ ++$i }}</td>
		            	<td>{{ $employee->associate_id }}</td>
		            	<td>
		            		<b>{{ $employee->as_name }}</b><br>
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
		            	<td>{{ $otHour }}</td>
	            	</tr>
	            	@php 
	            		$totalOt += $employee->ot_hour; 
	            	@endphp
	            @endforeach
	            	<tr>
	            		<td colspan="10" style="text-align: right;"><b>Total</b></td>
	            		<td  style="text-align: right">
	            			<b>
	            			@php
	            			
		                    $otHourE = round($totalOt,2);
		                    echo $otHourE;
	            			@endphp
	            			</b>
	            		</td>
	            		<td></td>
	            	</tr>
	            @else
		            <tr>
		            	<td colspan="12" class="text-center">No OT Employee Found!</td>
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
				}elseif($format == 'ot_hour'){
					$head = 'Hour';
				}else{
					$head = '';
				}

				$i = 0; $tot0 = 0; $tot53 = 0;$tot61 = 0;$tot71 = 0;$tot81 = 0;$tot91 = 0;$tot100 = 0;
			@endphp
			<table class="table table-bordered table-hover table-head text-center" border="1" style="text-align: center;">
				<thead>
					<tr>
						<td rowspan="2">Sl</td>
						<td rowspan="2">{{ $head }}</td>
						<td rowspan="2">Total Worker</td>
						<td colspan="2"> 0-52 Hrs OT/month</td>
						<td colspan="2"> 53-60 Hrs</td>
						<td colspan="2"> 61-70 Hrs</td>
						<td colspan="2"> 71-80 Hrs</td>
						<td colspan="2"> 81-90 Hrs</td>
						<td colspan="2"> 91-100 Hrs</td>
						<td colspan="2"> 100+ Hrs</td>
						<td rowspan="2">Total Worker</td>
					</tr>
					<tr>
						<td style="font-size: 9px;">No. of Worker</td>
						<td style="font-size: 9px;">percentage</td>
						<td style="font-size: 9px;">No. of Worker</td>
						<td style="font-size: 9px;">percentage</td>
						<td style="font-size: 9px;">No. of Worker</td>
						<td style="font-size: 9px;">percentage</td>
						<td style="font-size: 9px;">No. of Worker</td>
						<td style="font-size: 9px;">percentage</td>
						<td style="font-size: 9px;">No. of Worker</td>
						<td style="font-size: 9px;">percentage</td>
						<td style="font-size: 9px;">No. of Worker</td>
						<td style="font-size: 9px;">percentage</td>
						<td style="font-size: 9px;">No. of Worker</td>
						<td style="font-size: 9px;">percentage</td>
					</tr>
				</thead>
				<tbody>
					@foreach($uniqueGroups as $group => $employees)
						@php
							$map = collect($employees)->groupBy('r',true);

							$ot100 = isset($map['100'])?count($map['100']):0;
							$ot91 = isset($map['91-100'])?count($map['91-100']):0;
							$ot81 = isset($map['81-90'])?count($map['81-90']):0;
							$ot71 = isset($map['71-80'])?count($map['71-80']):0;
							$ot61 = isset($map['61-70'])?count($map['61-70']):0;
							$ot53 = isset($map['53-60'])?count($map['53-60']):0;
							$ot0 = isset($map['0-52'])?count($map['0-52']):0;
							$total = count($employees);

							$tot0 += $ot0; $tot53 += $ot53; $tot61 += $ot61; $tot71 += $ot71; $tot81 += $ot81; $tot91 += $ot91; $tot100 += $ot100;
							
						@endphp
						<tr>
							<td>{{++$i}}</td>
							<td style="text-align: left;">
								@php
									if($format == 'as_unit_id'){
										if($group == 145){
											$body = 'MBM + MBF + MBM 2';
											$exPar = '&selected=145';
										}else{
											$body = $unit[$group]['hr_unit_name']??'';
											$exPar = '&selected='.$unit[$group]['hr_unit_id']??'';
										}
									}elseif($format == 'as_line_id'){
										if(isset($line[$group])){
											$body = $line[$group]['hr_line_name']??'';
											$exPar = '&selected='.$line[$group]['hr_line_id']??'';
										}else{
											$body = 'N/A';
											$exPar = '&selected=N/A';
										}
									}elseif($format == 'as_floor_id'){
										if(isset($floor[$group])){

											$body = $floor[$group]['hr_floor_name']??'';
											if($input['unit'] == 145){
												$uid = $floor[$group]['hr_floor_unit_id'];	
												$body .= '('.$unit[$uid]['hr_unit_short_name'].')';
											}
											$exPar = '&selected='.$floor[$group]['hr_floor_id']??'';
										}else{
											$body = 'N/A';
											$exPar = '&selected=N/A';
										}
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
										$body = $seName;
										$exPar = '&selected='.$section[$group]['hr_section_id']??'';
									}elseif($format == 'as_subsection_id'){
										$body = $subSection[$group]['hr_subsec_name']??'';
										$exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
									}elseif($format == 'ot_hour'){
										
					                    $otHourBody = round($group,2);
										$body = $otHourBody??'N/A';
									}else{
										$body = 'N/A';
									}
								@endphp
								
								{{ ($body == null)?'N/A':$body }} 
							</td>

							


							<td>{{count($employees)}}</td>
							<td>{{$ot0}}</td>
							<td>{{round($ot0/$total*100)}}</td>
							<td>{{$ot53}}</td>
							<td>{{round($ot53/$total*100)}}</td>
							<td>{{$ot61}}</td>
							<td>{{round($ot61/$total*100)}}</td>
							<td>{{$ot71}}</td>
							<td>{{round($ot71/$total*100)}}</td>
							<td>{{$ot81}}</td>
							<td>{{round($ot81/$total*100)}}</td>
							<td>{{$ot91}}</td>
							<td>{{round($ot91/$total*100)}}</td>
							<td>{{$ot100}}</td>
							<td>{{round($ot100/$total*100)}}</td>
							<td>{{count($employees)}}</td>
						</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td>Total</td>
						<td>{{$totalEmployees}}</td>
						<td>{{$tot0}}</td>
						<td>{{round($tot0/$total*100)}}</td>
						<td>{{$tot53}}</td>
						<td>{{round($tot53/$total*100)}}</td>
						<td>{{$tot61}}</td>
						<td>{{round($tot61/$total*100)}}</td>
						<td>{{$tot71}}</td>
						<td>{{round($tot71/$total*100)}}</td>
						<td>{{$tot81}}</td>
						<td>{{round($tot81/$total*100)}}</td>
						<td>{{$tot91}}</td>
						<td>{{round($tot91/$total*100)}}</td>
						<td>{{$tot100}}</td>
						<td>{{round($tot100/$total*100)}}</td>
						<td>{{$totalEmployees}}</td>
					</tr>
				</tfoot>
			</table>
		@endif
</div>

	