<div class="panel">
	<div class="panel-body">
			@php
				$urldata = http_build_query($input) . "\n";
				$jsonUrl = json_encode($urldata);
			@endphp
			{{-- <a href='{{ url("hr/reports/summary/excel?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 15px; left: 65px;"><i class="fa fa-file-excel-o"></i></a> --}}
			@if($input['report_format'] == 0)
				<a href='{{ url("hr/reports/activity-report-excle?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 15px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>
			@endif
		<div class="report_section" id="report_section">
			@php
				$formatHead = explode('_',$format);
			@endphp
			
			<div class="top_summery_section">
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">
		            	Attendance Statistics
		            </h2>
		            <table class="table no-border f-16">
		            	<tr>
		            		<td style="text-align: left;width: 30%">
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
		            		<td style="text-align: center;width: 45%">

		            			Date <b>: {{ $date}} </b> <br>
		            			@if($input['otnonot'] != null)
		                			<b> OT </b> 
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
	                			Total Employee
	                			<b>: </b>
		                		
		            		</td>
		            		<td style="text-align: right;width: 25%">
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
										
					                    $otHourBody = numberToTimeClockFormat($group);
										$body = $otHourBody??'N/A';
									}else{
										$head = '';
									}
								@endphp
			                	@if($head != '')
			                    <th colspan="2">{{ $head }}</th>
			                    <th colspan="11">{{ $body }}</th>
			                    @endif
			                </tr>
			                @endif
			                <tr>
			                    <th style="width:20px;">Sl</th>
			                    <th style="width:60px;">ID</th>
			                    <th style="width:100px;">Name</th>
			                    <th style="width:10%;">Designation</th>
			                    <th style="width:10%;">Department</th>
			                    <th style="width:5%;">Section</th>
			                    <th style="width:10%;">Sub Section</th>
			                    <th style="width:5%;">Floor</th>
			                    <th style="width:5%;">Line</th>
			                    <th style="width:5%;">Status</th>
			                    <th style="width:5%;">In time</th>
			                    <th style="width:5%;">Out time</th>
			                    <th style="width:5%;">OT Hour</th>
			                </tr>
			            </thead>
			            <tbody>
			            @php
			             $i = 0; $month = date('Y-m',strtotime($input['date'])); 
			            @endphp
			            @if(count($employees) > 0)
			            @foreach($employees as $employee)
			            	@php
			            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
			            	@endphp
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	{{-- <td><img src="{{ emp_profile_picture($employee) }}" class='small-image' style="height: 40px; width: auto;"></td> --}}
				            	<td><a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card"><strong>{{ $employee->associate_id }}</strong></a><br>
				            	{{ $employee->as_oracle_code }}
				            	</td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            		<p>{{ $employee->as_contact }}</p>
				            	</td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
				            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
				            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
				            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            	@php
				            		$sts = []; $col = 3;
				            		if(isset($pr[$employee->associate_id])){
				            			$punch = $pr[$employee->associate_id];
				            			$sts = 'Present';
				            			$col = 1;
				            		}else if(isset($lv[$employee->associate_id])){
				            			$sts = 'Leave - '.$lv[$employee->associate_id]->leave_type;
				            		}else if(isset($do[$employee->associate_id])){
				            			$sts = 'Day Off';
				            		}else{
				            			$sts = 'Absent';
				            		}


				            	@endphp

				            	@if($col == 1)
					            	<td>{{$sts}}</td>
					            	<td>
					            		@if($punch->in_time)
					            		{{date('H:i', strtotime($punch->in_time))}}
					            		@endif
					            	</td>
					            	<td>
					            		@if($punch->out_time)
					            		{{date('H:i', strtotime($punch->out_time))}}
					            		@endif
					            	</td>
					            	<td>{{($punch->ot_hour > 0 ? numberToTimeClockFormat($punch->ot_hour):'')}}</td>
				            	@else
				            		<td colspan="4"> {{$sts}}</td>
				            	@endif
			            	</tr>
			            	@php 
			            	@endphp
			            @endforeach
			            	<tr>
			            		<td colspan="11" style="text-align: right;"><b>Total</b></td>
			            		<td  style="text-align: right"><b>
			            			@php
			            			
			            			@endphp
			            		</b>
			            		</td>
			            		<td></td>
			            	</tr>
			            @else
				            <tr>
				            	<td colspan="14" class="text-center">No OT Employee Found!</td>
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

					@endphp
					<table class="table table-bordered table-hover table-head text-center table-striped" border="1" style="text-align: center;">
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
								<th> {{ $head }} {{ $format != 'ot_hour'?'Name':'' }}</th>
								<th> Present</th>
								<th> Absent</th>
								<th> Leave</th>
								<th> Day Off</th>
								<th> Total</th>
							</tr>
						</thead>
						<tbody>
							@php $i=0; @endphp
							@if(count($uniqueGroups) > 0)
							@foreach($uniqueGroups as $group => $employee)
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
								}elseif($format == 'as_section_id'){
									$body = $section[$group]['hr_section_name']??'N/A';
									$exPar = '&selected='.$section[$group]['hr_section_id']??'';
								}elseif($format == 'as_subsection_id'){
									$body = $subSection[$group]['hr_subsec_name']??'';
									$exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
								}elseif($format == 'as_designation_id'){
									$body = $designation[$group]['hr_designation_name']??'';
									$exPar = '&selected='.$designation[$group]['hr_designation_id']??'';
								}elseif($format == 'ot_hour'){
									
				                    $otHourBody = numberToTimeClockFormat($group);
									$body = $otHourBody??'N/A';
									$exPar = '&selected='.number_format($group,3)??'';
								}else{
									$body = 'N/A';
								}
							@endphp
							<tr class="cursor-pointer" onClick="selectedGroup(this.id, '{{ $body }}', {{ $jsonUrl }})" data-body="{{ $body }}" id="{{$exPar}}" data-url="{{ $jsonUrl }}" class="select-group">
								<td>{{ ++$i }}</td>
								@if($format == 'as_floor_id' || $format == 'as_line_id')
								<td style="text-align: left;">
									@if($format == 'as_floor_id')
										@php $unitIdfl = $floor[$group]['hr_floor_unit_id']??''; @endphp
									@else
										@php $unitIdfl = $line[$group]['hr_line_unit_id']??''; @endphp
									@endif
									{{ $unitIdfl != ''?($unit[$unitIdfl]['hr_unit_name']??''):'' }}
								</td>
								@endif
								@if($format == 'as_line_id')
								<td style="text-align: left;">
									@php $lineFloorId = $line[$group]['hr_line_floor_id']??''; @endphp
									{{ $lineFloorId != ''?($floor[$lineFloorId]['hr_floor_name']??''):'' }}
								</td>
								@endif
								@if($format == 'as_section_id' || $format == 'as_subsection_id')
								<td style="text-align: left;">
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
								<td style="text-align: left;">
									@php
										$getSec = $subSection[$group]['hr_subsec_section_id']??'';
										echo $section[$getSec]['hr_section_name']??'';
									@endphp
								</td>
								@endif
								<td style="text-align: left;">
									
									{{ ($body == null)?'N/A':$body }}
								</td>
								<td>
									{{$count[$group]['present']??0}}
								</td>
								<td>
									{{$count[$group]['absent']??0}}
								</td>
								<td>
									{{$count[$group]['leave']??0}}
								</td>
								<td>
									{{$count[$group]['holiday']??0}}
								</td>
								<td>
									{{$count[$group]['total']??0}}
								</td>
							</tr>
							@endforeach
							@else
							<tr>
				            	<td colspan="{{ ($format == 'as_subsection_id' || $format == 'as_subsection_id')?'6':'4'}}" class="text-center">No OT Employee Found!</td>
				            </tr>
							@endif
						</tbody>
					</table>
				@endif
		</div>

	</div>
</div>