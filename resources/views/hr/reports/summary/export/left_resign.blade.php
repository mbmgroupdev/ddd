@php libxml_use_internal_errors(true); @endphp
<div class="panel">
	<div class="panel-body">
		<div class="report_section" id="report_section">
			@php
				$formatHead = explode('_',$format);
			@endphp
			
			<div class="top_summery_section">
				
		        <div class="page-header-summery">
        			
        			<h2>Left and Resign List </h2>
        			<h3 style="text-align: center;">Date {{$input['from_date']}} to {{$input['to_date']}}</h3>
        			@if($input['unit'] == 145)
        				<h4>Unit: MBM + MBW + MBM 2</h4>
        			@else
        			<h4>Unit: {{ $unit[$input['unit']]['hr_unit_name'] }}</h4>
        			@endif
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
        			<h4>Employee: <b>{{ $totalEmployees }}</b></h4>
		            		
		        </div>
			</div>
			<div class="content_list_section" >
				@if($input['report_format'] == 0)
					@foreach($uniqueGroups as $group => $employees)
					
					<table class="table table-bordered table-hover table-head" border="1">
						<thead>
							@if(count($employees) > 0)
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
									}elseif($format == 'as_unit_id'){
										$head = 'Unit';
										$body = $unit[$group]['hr_unit_name']??'N/A';
									}else{
										$head = '';
									}
								@endphp
			                	@if($head != '')
			                    <th colspan="2" style="font-style: bold;">{{ $head }}</th>
			                    <th colspan="12" style="font-style: bold;">{{ $body }}</th>
			                    @endif
			                </tr>
			                @endif
			                <tr>
			                    <th >Sl</th>
			                    <th >Associate ID</th>
			                    <th >Name</th>
			                    <th >Phone</th>
			                    <th >Oracle ID</th>
			                    <th >Designation</th>
			                    <th >Department</th>
			                    <th >Section</th>
			                    <th >Sub Section</th>
			                    <th >Floor</th>
			                    <th >Line</th>
			                    <th >Type</th>
			                    <th >Date</th>
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
			            		

			            	@endphp
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
				            	<td style="text-align: center;text-transform: capitalize;">
				            			{{emp_status_name($employee->as_status)}}
				            		</td>

					            	<td style="text-align: right">{{$employee->as_status_date}}</td>
				            	
			            	</tr>
			            @endforeach
			            @else
				            <tr>
				            	<td colspan="15" class="text-center">No Employee Found!</td>
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
					<table class="table table-bordered table-hover table-head" border="1">
						<thead>
							<tr style="text-align: center;">
								<th>Sl</th>
								@if($format == 'as_section_id' || $format == 'as_subsection_id')
								<th>Department Name</th>
								@endif
								@if($format == 'as_subsection_id')
								<th>Section Name</th>
								@endif
								<th> {{ $head }} {{ $format != 'ot_hour'?'Name':'' }}</th>
								<th>Left</th>
								<th>Resign</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>
							@php $i=0;  @endphp
							@if(count($getEmployee) > 0)
							@foreach($getEmployee as $employee)
							@php $group = $employee->$format; @endphp
							<tr>
								<td>{{ ++$i }}</td>
								@if($format == 'as_section_id' || $format == 'as_subsection_id')
								<td>
									@php
										if($format == 'as_subsection_id'){
											$getDepar = $subSection[$group]['hr_subsec_department_id']??'';
										}else{
											$getDepar = $section[$group]['hr_section_department_id']??'';
										}
										echo $department[$getDepar]['hr_department_name']??'';
									@endphp
								</td>
								@endif
								@if($format == 'as_subsection_id')
								<td>
									@php
										$getSec = $subSection[$group]['hr_subsec_section_id']??'';
										echo $section[$getSec]['hr_section_name']??'';
									@endphp
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
										}else{
											$body = 'N/A';
										}
									@endphp
									{{ ($body == null)?'N/A':$body }}
								</td>
								<td style="text-align: center;">
									{{ $employee->lefts??0 }}

								</td>
								<td style="text-align: center;">
									{{ $employee->resigns??0 }}
								</td>
								<td style="text-align: center;">
									{{ $employee->total }}
								</td>
							</tr>
							@endforeach
							<tr style="font-weight: bold;">
								<td 
								@if($format == 'as_section_id')
									colspan="3" 
								@elseif($format == 'as_subsection_id')
									colspan="4" 
								@else
									colspan="2" 
								@endif
								 style="font-weight: bold;">
									Total
								</td>
								<td style="text-align: center;font-weight: bold;">{{$getEmployee->sum('lefts')}}</td>
								<td style="text-align: center;font-weight: bold;">{{$getEmployee->sum('resigns')}}</td>
								<td style="text-align: center;font-weight: bold;">{{$totalEmployees}}</td>
							</tr>
							
							@else
							<tr>
				            	<td colspan="{{ ($format == 'as_subsection_id' || $format == 'as_subsection_id')?'6':'4'}}" class="text-center">No Employee Found!</td>
				            </tr>
							@endif
						</tbody>
						
					</table>
				@endif
			</div>
		</div>

	</div>
</div>
