<div class="panel">
	<div class="panel-body">
		@php
			$urldata = http_build_query($input) . "\n";
			$jsonUrl = json_encode($urldata);
		@endphp
		@if($input['report_format'] == 0)
			
			<a href='{{ url("hr/reports/activity-report-excle?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 15px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>
		@endif
		<div class="report_section" id="report_section">
			@php
				$formatHead = explode('_',$format);
			@endphp
			
			<div class="top_summery_section">
				@if($input['report_format'] == 0 || ($input['report_format'] == 1 && $format != null))
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Working Hour @if($input['report_format'] == 0) Details @else Summary @endif Report </h2>
		            <table class="table table2excel no-border f-16" >
		            	<tr>
		            		<td width="33%">
		            			@if($input['unit'] != null)
		            				Unit <b>: {{ $input['unit'] == 145?'MBM + MBF + MBM 2':$unit[$input['unit']]['hr_unit_name'] }}</b> <br>
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

		                	</div>
		            			Date <b>: {{ $input['date']}} </b> <br>
		            			@if($input['otnonot'] != null)
		                			OT  
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
	                			Total Employee
	                			<b>: {{ $totalEmployees }}</b><br>
		                			Total
		                			<b>: {{ $totalValue }} Working/Employee</b><br>
		                			Average
		                			<b>: {{ $totalAvgHour }} Working/Employee</b>
		                		
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
		                			<b class="capitalize">: {{ isset($formatHead[1])?$formatHead[1]:'N/A' }}</b>
		            		</td>
		            	</tr>
		            	
		            </table>
		        </div>
		        @else
		        <div class="page-header-summery">
        			
        			<h2>Working Hour Summary Report </h2>
        			@if($input['unit'] != null)
        			<h4>Unit: {{ $unit[$input['unit']]['hr_unit_name'] }}</h4>
        			@endif
        			@if($input['unit'] != null)
        			<h4>Location: {{ $location[$input['unit']]['hr_unit_name'] }}</h4>
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
        			<h4>Working Date: {{ $input['date']}}</h4>
        			<h4>Total Working Employee: <b>{{ $totalEmployees }}</b></h4>
        			<h4>Total: <b>{{ $totalValue }}</b> Working/Employee</h4>
        			<h4>Average: <b>{{ $totalAvgHour }}</b> Working/Employee</h4>
		            		
		        </div>
		        @endif
			</div>
			<div class="content_list_section">
				@if($input['report_format'] == 0)
					
					<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
					@foreach($uniqueGroupEmp as $group => $employees)
						<thead>
							@if(count($employees) > 0)
			                <tr>
			                	@php
									if($format == 'as_line_id'){
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
										$body = $section[$group]['hr_section_name']??'N/A';
									}elseif($format == 'as_subsection_id'){
										$head = 'Sub Section';
										$body = $subSection[$group]['hr_subsec_name']??'N/A';
									}else{
										$head = '';
									}
								@endphp
			                	@if($head != '')
			                    <th colspan="2">{{ $head }}</th>
			                    <th colspan="12">{{ $body }}</th>
			                    @endif
			                </tr>
			                @endif
			                <tr>
			                    <th>Sl</th>
			                    {{-- <th>Photo</th> --}}
			                    <th>Associate ID</th>
			                    <th>Name & Phone</th>
			                    <th>Oracle ID</th>
			                    <th>Designation</th>
			                    <th>Department</th>
			                    <th>Section</th>
			                    <th>Sub Section</th>
			                    <th>Floor</th>
			                    <th>Line</th>
			                    <th>In-time</th>
			                    <th>Out-time</th>
			                    <th>Break-time</th>
			                    <th>Working Hour</th>
			                </tr>
			            </thead>
			            <tbody>
			            
			            @php
			             $i = 0; $month = date('Y-m',strtotime($input['date'])); $totalMinute = 0;
			            @endphp
			            @if(count($employees) > 0)
					        @foreach($employees as $employee)
			            	@php
			            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
			            		$hours = $employee->hourDuration == 0?0:floor($employee->hourDuration / 60);
			                    $minutes = $employee->hourDuration == 0?0:($employee->hourDuration % 60);
			                    $totalHour = sprintf('%02d h:%02d m', $hours, $minutes);
			            	@endphp
			            	@if($head == '')
				            	<tr>
				            		<td>{{ ++$i }}</td>
					            	{{-- <td><img src="{{ emp_profile_picture($employee) }}" class='small-image' style="height: 40px; width: auto;"></td> --}}
					            	<td>
					            		
					            		<a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->associate_id }}</a>
					            	</td>
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
					            	<td>{{ ($employee->in_time != null && $employee->remarks != 'DSI') ? date('H:i:s', strtotime($employee->in_time)):'' }}</td>
					            	<td>{{ $employee->out_time != null?date('H:i:s', strtotime($employee->out_time)):'' }}</td>
					            	<td>{{ $employee->hr_shift_break_time }} min</td>
					            	<td data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$totalHour}}">{{ round($employee->hourDuration/60,2) }}</td>
					            	
				            	</tr>
				            	@php $totalMinute += $employee->hourDuration; @endphp
			            	@else
				            	@if($group == $employee->$format)
					            	<tr>
					            		<td>{{ ++$i }}</td>
						            	{{-- <td><img src="{{ emp_profile_picture($employee) }}" class='small-image' style="height: 40px; width: auto;"></td> --}}
						            	<td>
						            		<a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->associate_id }}</a>
						            	</td>
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
						            	<td>{{ ($employee->in_time != null && $employee->remarks != 'DSI') ? date('H:i:s', strtotime($employee->in_time)):'' }}</td>
						            	<td>{{ $employee->out_time != null?date('H:i:s', strtotime($employee->out_time)):'' }}</td>
						            	<td>{{ $employee->hr_shift_break_time }} min</td>
						            	<td data-toggle="tooltip" data-placement="top" title="" data-original-title="{{$totalHour}}">{{ round($employee->hourDuration/60,2) }}</td>
						            	
					            	</tr>
					            	@php $totalMinute += $employee->hourDuration; @endphp
			            		@endif
			            	@endif
			            	@endforeach
			            	{{-- <tr>
			            		<td colspan="8"></td>
			            		<td colspan="3"><b>Total Employee</b></td>
			            		<td colspan="2"><b>{{ $i }}</b></td>
			            	</tr> --}}
			            	<tr>
			            		<td colspan="8"></td>
			            		<td colspan="3"><b>Total Working Hour</b></td>
			            		<td colspan="2"><b>
			            			@php
			            				$groupHours = $totalMinute == 0?0:floor($totalMinute / 60);
					                    $groupMinutes = $totalMinute == 0?0:($totalMinute % 60);
					                    echo sprintf('%02dh:%02dm', $groupHours, $groupMinutes)??0;
			            			@endphp
			            		</b></td>
			            	</tr>
			            	<tr>
			            		<td colspan="8"></td>
			            		<td colspan="3"><b>Avg. Working Hour</b></td>
			            		<td colspan="2"><b>
			            			@php
			            				$avgminuteG = $totalMinute == 0?0:$totalMinute / $i;
			            				$avgroupHours = $avgminuteG == 0?0:floor($avgminuteG / 60);
					                    $avgroupMinutes = $avgminuteG == 0?0:($avgminuteG % 60);
					                    echo sprintf('%02dh:%02dm', $avgroupHours, $avgroupMinutes)??0;
			            			@endphp
			            		</b></td>
			            	</tr>
			            @else
				            <tr>
				            	<td colspan="14" class="text-center">No Employee Found!</td>
				            </tr>
			            @endif
			            <tr style="border:0 !important;"><td colspan="14" style="border: 0 !important;height: 20px;"></td> </tr>
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
					<table class="table table2excel table-bordered table-hover table-head" border="1" cellpadding="5">
						<thead>
							<tr>
								<th>Sl</th>
								@if($format == 'as_floor_id' || $format == 'as_line_id')
								<th>Unit</th>
								@endif
								@if($format == 'as_line_id')
								<th>Floor</th>
								@endif
								@if($format == 'as_section_id' || $format == 'as_subsection_id')
								<th>Department Name</th>
								@endif
								@if($format == 'as_subsection_id')
								<th>Section Name</th>
								@endif
								<th> {{ $head }} Name</th>
								<th>Employee</th>
								<th>Working Hour</th>
								<th>Average Working Hour</th>
							</tr>
						</thead>
						<tbody>
							@php  $i=0; $totalEmployee = 0; @endphp
							@if(count($getEmployee) > 0)
							@foreach($getEmployee as $employee)
							@php $group = $employee->$format; @endphp
							@php		
								if($format == 'as_unit_id'){
									if($group == 145){
										$body = 'MBM + MBF + MBM 2';
									}else{
										$body = $unit[$group]['hr_unit_name']??'';
									}
									$exPar = '&selected='.($unit[$group]['hr_unit_id']??'');
								}elseif($format == 'as_line_id'){
									$body = $line[$group]['hr_line_name']??'';
									$exPar = '&selected='.($line[$group]['hr_line_id']??'');
								}elseif($format == 'as_floor_id'){
									$body = $floor[$group]['hr_floor_name']??'';
									$exPar = '&selected='.($floor[$group]['hr_floor_id']??'');
								}elseif($format == 'as_department_id'){
									$body = $department[$group]['hr_department_name']??'';
									$exPar = '&selected='.$department[$group]['hr_department_id']??'';
								}elseif($format == 'as_designation_id'){
									$body = $designation[$group]['hr_designation_name']??'';
									$exPar = '&selected='.$designation[$group]['hr_designation_id']??'';
								}elseif($format == 'as_section_id'){
									$body = $section[$group]['hr_section_name']??'';
									$exPar = '&selected='.$section[$group]['hr_section_id']??'';
								}elseif($format == 'as_subsection_id'){
									$body = $subSection[$group]['hr_subsec_name']??'';
									$exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
								}else{
									$body = 'N/A';
								}
							@endphp
							<tr class="cursor-pointer" onClick="selectedGroup(this.id, '{{ $body }}', {{ $jsonUrl }})" data-body="{{ $body }}" id="{{$exPar}}" data-url="{{ $jsonUrl }}" class="select-group">
								<td>{{ ++$i }}</td>
								@if($format == 'as_floor_id' || $format == 'as_line_id')
								<td>
									@if($format == 'as_floor_id')
										@php $unitIdfl = $floor[$group]['hr_floor_unit_id']??''; @endphp
									@else
										@php $unitIdfl = $line[$group]['hr_line_unit_id']??''; @endphp
									@endif
									{{ $unitIdfl != ''?($unit[$unitIdfl]['hr_unit_name']??''):'' }}
								</td>
								@endif
								@if($format == 'as_line_id')
								<td>
									@php $lineFloorId = $line[$group]['hr_line_floor_id']??''; @endphp
									{{ $lineFloorId != ''?($floor[$lineFloorId]['hr_floor_name']??''):'' }}
								</td>
								@endif
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
									{{ ($body == null)?'N/A':$body }}
								</td>
								<td>
									{{ $employee->total }}
								</td>
								<td>
									@php
									$sgroupHours = $employee->groupHourDuration == 0?0:floor($employee->groupHourDuration / 60);
				                    $sgroupMinutes = $employee->groupHourDuration == 0?0:($employee->groupHourDuration % 60);
				                    echo sprintf('%02d h:%02d m', $sgroupHours, $sgroupMinutes)??0;
									@endphp
								</td>
								<td>
									@php
									$avgMin = $employee->groupHourDuration == 0?0:($employee->groupHourDuration / $employee->total);
					                $aHours = $avgMin == 0?0:floor($avgMin / 60);
					                $aMinutes = $avgMin == 0?0:($avgMin % 60);
					                echo sprintf('%02d h:%02d m', $aHours, $aMinutes);
									@endphp
								</td>
							</tr>
							@endforeach
							@else
							<tr>
				            	<td colspan="5" class="text-center">No Employee Found!</td>
				            </tr>
							@endif
						</tbody>
						
					</table>
				@endif
			</div>
		</div>

		
	</div>
</div>
