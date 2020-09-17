<div class="panel">
	<div class="panel-body">
		<div class="report_section">
			@php
				$unit = unit_by_id();
				$line = line_by_id();
				$floor = floor_by_id();
				$department = department_by_id();
				$designation = designation_by_id();
				$section = section_by_id();
				$subSection = subSection_by_id();
				$area = area_by_id();
				$formatHead = explode('_',$format);
			@endphp
			
			<div class="top_summery_section">
				@if($input['report_format'] == 0 || ($input['report_format'] == 1 && $format != null))
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">OT @if($input['report_format'] == 0) Details @else Summary @endif Report </h2>
		            
		            <div class="row">
		            	<div class="col-5">
		            		<div class="row">
		                		<div class="col-2 pr-0">
		                			<h5>Unit</h5>
		                		</div>
		                		<div class="col-10">
		                			<b>: {{ $unit[$input['unit']]['hr_unit_name'] }}</b>
		                		</div>
		                		@if($input['area'] != null)
		                		<div class="col-2 pr-0">
		                			<h5>Area</h5>
		                		</div>
		                		<div class="col-10">
		                			<b>: {{ $area[$input['area']]['hr_area_name'] }}</b>
		                		</div>
		                		@endif
		                		@if($input['department'] != null)
		                		<div class="col-2 pr-0">
		                			<h5>Department</h5>
		                		</div>
		                		<div class="col-10">
		                			<b>: {{ $department[$input['department']]['hr_department_name'] }}</b>
		                		</div>
		                		@endif
		                		@if($input['section'] != null)
		                		<div class="col-2 pr-0">
		                			<h5>Section</h5>
		                		</div>
		                		<div class="col-10">
		                			<b>: {{ $section[$input['section']]['hr_section_name'] }}</b>
		                		</div>
		                		@endif
		            		</div>
		            	</div>
		            	<div class="col-4 no-padding">
		            		<div class="row">
		            			<div class="col-4 p-0">
		                			<h5>OT Date</h5>
		                		</div>
		                		<div class="col-8 pl-0">
		                			<b>: {{ $input['date']}} </b>
		                		</div>
		                		@if($input['otnonot'] != null)
		                		<div class="col-4 p-0">
		                			<h5>OT</h5>
		                		</div>
		                		<div class="col-8 pl-0">
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b>
		                		</div>
		                		@endif
		                		<div class="col-4 p-0">
		                			<h5>Total OT Employee</h5>
		                		</div>
		                		<div class="col-8 pl-0">
		                			<b>: {{ $totalEmployees }}</b>
		                		</div>
		                		<div class="col-4 p-0">
		                			<h5>Total OT Hour</h5>
		                		</div>
		                		<div class="col-8 pl-0">
		                			<b>: {{ $totalValue }}</b>
		                		</div>
		                	</div>
		            	</div>
		            	<div class="col-3 no-padding">
		            		<div class="row">
		                		@if($input['subSection'] != null)
		                		<div class="col-3 pr-0">
		                			<h5>Sub Section</h5>
		                		</div>
		                		<div class="col-9 pl-0">
		                			<b>: {{ $subSection[$input['subSection']]['hr_subsec_name'] }}</b>
		                		</div>
		                		@endif
		                		@if($input['floor_id'] != null)
		                		<div class="col-3 pr-0">
		                			<h5>Floor</h5>
		                		</div>
		                		<div class="col-9 pl-0">
		                			<b>: {{ $floor[$input['floor_id']]['hr_floor_name'] }}</b>
		                		</div>
		                		@endif
		                		@if($input['line_id'] != null)
		                		<div class="col-3 pr-0">
		                			<h5>Line</h5>
		                		</div>
		                		<div class="col-9 pl-0">
		                			<b>: {{ $line[$input['line_id']]['hr_line_name'] }}</b>
		                		</div>
		                		@endif
		                		<div class="col-3 pr-0">
		                			<h5>Format</h5>
		                		</div>
		                		<div class="col-9 pl-0">
		                			<b class="capitalize">: {{ isset($formatHead[1])?$formatHead[1]:'N/A' }}</b>
		                		</div>
		                	</div>
		            	</div>
		            </div>
		        </div>
		        @else
		        <div class="page-header-summery">
        			
        			<h2>OT Summary Report </h2>
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
        			<h4>OT Date: {{ $input['date']}}</h4>
        			<h4>Total OT Employee: <b>{{ $totalEmployees }}</b></h4>
        			<h4>Total OT Hour: <b>{{ $totalValue }}</b></h4>
		            		
		        </div>
		        @endif
			</div>
			<div class="content_list_section">
				@if($input['report_format'] == 0)
					@foreach($uniqueGroups as $group)
					
					<table class="table table-bordered table-hover table-head">
						<thead>
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
									}elseif($format == 'as_designation_id'){
										$head = 'Designation';
										$body = $designation[$group]['hr_designation_name']??'N/A';
									}elseif($format == 'ot_hour'){
										$head = 'OT Hour';
										$otHourEx = explode('.', $group);
					                    $minute = '00';
					                    if(isset($otHourEx[1])){
					                        $minute = $otHourEx[1];
					                        if($minute == 50){
					                            $minute = 30;
					                        }
					                    }
					                    $otHourBody = $otHourEx[0].'.'.$minute;
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
			                    <th>Sl</th>
			                    <th>Photo</th>
			                    <th>Associate ID</th>
			                    <th>Name & Phone</th>
			                    <th>Designation</th>
			                    <th>Department</th>
			                    <th>Floor</th>
			                    <th>Line</th>
			                    <th>In-Time</th>
			                    <th>Out-Time</th>
			                    <th>OT Hour</th>
			                    <th>Action</th>
			                </tr>
			            </thead>
			            <tbody>
			            @php
			             $i = 0; $month = date('Y-m',strtotime($input['date'])); $totalOt=0;
			            @endphp
			            @if(count($getEmployee) > 0)
			            @foreach($getEmployee as $employee)
			            	@php
			            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
			            		$otHourEx = explode('.', $employee->ot_hour);
			                    $minute = '00';
			                    if(isset($otHourEx[1])){
			                        $minute = $otHourEx[1];
			                        if($minute == 50){
			                            $minute = 30;
			                        }
			                    }
			                    $otHour = $otHourEx[0].'.'.$minute;

			            	@endphp
			            	@if($head == '')
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	<td><img src="{{ emp_profile_picture($employee) }}" class='small-image' style="height: 40px; width: auto;"></td>
				            	<td><a href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank">{{ $employee->associate_id }}</a></td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            		<p>{{ $employee->as_contact }}</p>
				            	</td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
				            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            	<td>{{ date('H:i:s', strtotime($employee->in_time)) }}</td>
				            	<td>{{ date('H:i:s', strtotime($employee->out_time)) }}</td>
				            	<td>{{ $otHour }}</td>
				            	<td>
				            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Yearly Activity Report' ><i class="fa fa-eye"></i></button>
				            	</td>
			            	</tr>
			            	@php $totalOt += $employee->ot_hour; @endphp
			            	@else
				            	@if($group == $employee->$format)
				            	<tr>
				            		<td>{{ ++$i }}</td>
					            	<td><img src="{{ emp_profile_picture($employee) }}" class='small-image' style="height: 40px; width: auto;"></td>
					            	<td><a href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank">{{ $employee->associate_id }}</a></td>
					            	<td>
					            		<b>{{ $employee->as_name }}</b>
					            		<p>{{ $employee->as_contact }}</p>
					            	</td>
					            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
					            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
					            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
					            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
					            	<td>{{ $employee->in_time }}</td>
					            	<td>{{ $employee->out_time }}</td>
					            	<td>{{ $otHour }}</td>
					            	<td>
					            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Yearly Activity Report' ><i class="fa fa-eye"></i></button>
					            	</td>
				            	</tr>
				            	@php $totalOt += $employee->ot_hour; @endphp
				            	@endif
			            	@endif
			            @endforeach
			            @else
				            <tr>
				            	<td colspan="12" class="text-center">No OT Employee Found!</td>
				            </tr>
			            @endif
			            </tbody>
			            <tfoot>
			            	<tr>
			            		<td colspan="9"></td>
			            		<td><b>Total Employee</b></td>
			            		<td colspan="2"><b>{{ $i }}</b></td>
			            	</tr>
			            	<tr>
			            		<td colspan="9"></td>
			            		<td><b>Total OT Hour</b></td>
			            		<td colspan="2"><b>
			            			@php
			            			$otHourEx = explode('.', $totalOt);
				                    $minute = '00';
				                    if(isset($otHourEx[1])){
				                        $minute = $otHourEx[1];
				                        if($minute == 5){
				                            $minute = 30;
				                        }
				                    }
				                    echo $otHourEx[0].'.'.$minute;
			            			@endphp
			            		</b></td>
			            	</tr>
			            </tfoot>
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
						}elseif($format == 'ot_hour'){
							$head = 'Hour';
						}else{
							$head = '';
						}
					@endphp
					<table class="table table-bordered table-hover table-head">
						<thead>
							<tr>
								<th>Sl</th>
								<th> {{ $head }} {{ $format != 'ot_hour'?'Name':'' }}</th>
								<th>Employee</th>
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
										}elseif($format == 'ot_hour'){
											$otHourEx = explode('.', $group);
						                    $minute = '00';
						                    if(isset($otHourEx[1])){
						                        $minute = $otHourEx[1];
						                        if($minute == 50){
						                            $minute = 30;
						                        }
						                    }
						                    $otHourBody = $otHourEx[0].'.'.$minute;
											$body = $otHourBody??'N/A';
										}else{
											$body = 'N/A';
										}
									@endphp
									{{ ($body == null)?'N/A':$body }}
								</td>
								<td>
									{{ $employee->total }}
									@php $totalEmployee += $employee->total; @endphp
								</td>
							</tr>
							@endforeach
							@else
							<tr>
				            	<td colspan="3" class="text-center">No OT Employee Found!</td>
				            </tr>
							@endif
						</tbody>
						
					</table>
				@endif
			</div>
		</div>

		{{-- modal --}}
		@include('hr.reports.daily_activity.attendance.employee_activity_modal')
	</div>
</div>
