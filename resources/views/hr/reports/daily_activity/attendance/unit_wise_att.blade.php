<div class="panel">
	
	<div class="panel-body">
		<div class="report_section" id="report_section">
			@php
				$formatHead = explode('_',$format);
				$urldata = http_build_query($input) . "\n";
			@endphp
			
			<div class="top_summery_section">
				@if($input['report_format'] == 0 || ($input['report_format'] == 1 && $format != null))
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;"> Attendance @if($input['report_format'] == 0) Details @else Summary @endif Report</h2>

		            <table class="table no-border f-16" border="0">
		            	<tr>
		            		<td style="width: 33%">
		            		@if(isset($input['unit']) && $input['unit'] != null)
	            				Unit <b>: {{ $input['unit'] == 145?'MBM + MBF + MBM 2':$unit[$input['unit']]['hr_unit_name'] }}</b> <br>
		        			@endif
		        			@if(isset($input['location']) &&  $input['location']!= null)
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
		            		<td style="width: 33%;text-align: center;">
		            			 Date <b>: {{ $input['date']??'' }}</b><br>
		            			 Employee <b>: {{ count($avail_as) }}</b><br>
		            			@if($input['otnonot'] != null)
		                			<b> OT </b> 
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
		                		
		                		
		            		</td>
		            		<td style="width: 33%;text-align: right;">
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
		                	
		            		</td>
		            	</tr>
		            	
		            </table>
		            
		            
		        </div>
		        @else
		        <div class="page-header-summery">
        			
        			<h2>Attendance </h2>
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

        		
		            		
		        </div>
		        @endif
			</div>
			<ul class="color-bar mb-3">
	            <li><span class="color-label lib-roster"></span><span class="lib-label"> MBM </span></li>
	            
	            <li><span class="color-label lib-roster-holiday"></span><span class="lib-label"> CEIL </span></li>
	            <li><span class="color-label lib-roster-general"></span><span class="lib-label"> HO </span></li>
	            <li><span class="color-label lib-roster-ot"></span><span class="lib-label"> AQL</span></li>
	            
	        </ul>
			@if($input['report_format'] == 0)
			<div class="content_list_section">
				<style type="text/css" media="print">
					td, tr ,table{
						padding: 2px !important;
					}
				</style>
					

					<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left;" border="1" cellpadding="5">
					@foreach($uniqueGroups as $group => $employees)
						
					<thead>
		                @if(count($employees) > 0)
		                <tr class="table-row">
		                	@php
								if($format == 'as_unit_id'){
									$head = 'Unit';
									if($group == 145){
										$body = 'MBM + MBF + MBM 2';
									}elseif($group == null){
										$body = '';
									}else{
										$body = $unit[$group]['hr_unit_name']??'';
									}
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
									$body = $subSection[$group]['hr_subsec_name']??'N/A';
								}elseif($format == 'as_area_id'){
									$head = 'Area';
									$body = $area[$group]['hr_area_name']??'N/A';
									
								}else{
									$head = '';
								}
							@endphp
		                	@if($head != '')
		                    <th colspan="1">{{ $head }}</th>
		                    <th colspan="6" ><div style="display:flex;justify-content: space-between;"><span>{{ $body }}</span> <span>{{ $date[0] }}</span></div></th>
		                    <th colspan="8" style="text-align: center;">{{ $date[1] }}</th>
		                    @endif
		                </tr>

		                @endif
		                <tr>
		                    <th rowspan="2" class="vertical-align">ID</th>
		                    <th rowspan="2" class="vertical-align">Name</th>
		                    <th rowspan="2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Designation" class="vertical-align">DEGN</th>
		                    <th colspan="4" style="white-space: nowrap;">In</th>
		                    <th colspan="4" style="white-space: nowrap;">In</th>
		                    <th colspan="4" style="white-space: nowrap;">Out</th>
		                    {{-- <th>OT</th> --}}
		                </tr>
		                <tr>
		                	<th>MBM</th>
		                	<th>CEIL</th>
		                	<th>HO</th>
		                	<th>AQL</th>
		                	<th>MBM</th>
		                	<th>CEIL</th>
		                	<th>HO</th>
		                	<th>AQL</th>
		                	<th>MBM</th>
		                	<th>CEIL</th>
		                	<th>HO</th>
		                	<th>AQL</th>
		                </tr>
		            </thead>
		            <tbody>
						
			            @php
			             $i = 0; $month = date('Y-m',strtotime($input['present_date'])); 
			            @endphp
			            @if(count($employees) > 0)
			            @foreach($employees as $employee)
			            	@php
			            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
			            	@endphp
			            	<tr class="table-row">
				            	<td class="@if(in_array($employee->as_unit_id, [1,4,5])) lib-roster @elseif(in_array($employee->as_unit_id, [2,8])) lib-roster-holiday @elseif($employee->as_unit_id == 3) lib-roster-ot @endif ">
				            		<a class="job_card text-white" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">
				            			@if($employee->as_oracle_code != null)
				            				{{ $employee->as_oracle_code }}
				            			@else
				            				<b> {{ $employee->associate_id }} </b>
				            			@endif
					            	</a>
					            </td>
				            	<td class="@if(in_array($employee->as_unit_id, [1,4,5])) lib-roster @elseif(in_array($employee->as_unit_id, [2,8])) lib-roster-holiday @elseif($employee->as_unit_id == 3) lib-roster-ot @endif ">
				            		<span class="font-weight-bold text-white">
				            			{{ $employee->as_name }}
				            		</span>
				            	</td>
				            	<td style="cursor:pointer;" data-toggle="tooltip" data-placement="top"  data-original-title="{{$designation[$employee->as_designation_id]['hr_designation_name']??'' }}" class="@if(in_array($employee->as_unit_id, [1,4,5])) lib-roster @elseif(in_array($employee->as_unit_id, [2,8])) lib-roster-holiday @elseif($employee->as_unit_id == 3) lib-roster-ot @endif "><span class="text-white">
				            		{{ $short_designation[$employee->as_designation_id]??'' }}
				            	</span></td>
				            	@if(isset($pr[$employee->as_id][$date[0]]))
				            	<td style="text-align: center;">
				            		{{-- detect today attendance --}}
				            		@if(isset($pr[$employee->as_id][$date[0]]))
				            		 	@if(($pr[$employee->as_id][$date[0]]->in_time || $pr[$employee->as_id][$date[0]]->in_time != null || $pr[$employee->as_id][$date[0]]->in_time != 'DSI') && in_array($pr[$employee->as_id][$date[0]]->in_unit, [1,4,5]))
					            			<p class="lib-roster text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[0]]->in_time))}}</p>
					            			
				            		 	@endif
				            		
				            		@endif
				            		
				            	</td>
				            	<td style="text-align: center;">
				            		{{-- detect today attendance --}}
				            		@if(isset($pr[$employee->as_id][$date[0]]))
				            		 	@if(($pr[$employee->as_id][$date[0]]->in_time || $pr[$employee->as_id][$date[0]]->in_time != null || $pr[$employee->as_id][$date[0]]->in_time != 'DSI') && in_array($pr[$employee->as_id][$date[0]]->in_unit, [2,8]))

					            			<p class="lib-roster-holiday text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[0]]->in_time))}}</p>
				            		 	@endif
				            		
				            		@endif
				            		
				            	</td>
				            	<td style="text-align: center;">
				            		{{-- detect today attendance --}}
				            		@if(isset($pr[$employee->as_id][$date[0]]))
				            		 	@if(($pr[$employee->as_id][$date[0]]->in_time || $pr[$employee->as_id][$date[0]]->in_time != null || $pr[$employee->as_id][$date[0]]->in_time != 'DSI') && in_array($pr[$employee->as_id][$date[0]]->in_unit, [1001]))

					            			<p class="lib-roster-general text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[0]]->in_time))}}</p>

				            		 	@endif
				            		
				            		@endif
				            		
				            	</td>
				            	<td style="text-align: center;">
				            		{{-- detect today attendance --}}
				            		@if(isset($pr[$employee->as_id][$date[0]]))
				            		 	@if(($pr[$employee->as_id][$date[0]]->in_time || $pr[$employee->as_id][$date[0]]->in_time != null || $pr[$employee->as_id][$date[0]]->in_time != 'DSI') && in_array($pr[$employee->as_id][$date[0]]->in_unit,[3]))
				            		 	<p class="lib-roster-ot text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[0]]->in_time))}}</p>
				            		 	@endif
				            		
				            		@endif
				            		
				            	</td>
				            	@else
				            	<td colspan="4" style="text-align: center;">
				            		{{-- detect today attendance --}}
				            		<b>@if(isset($lv[$employee->associate_id][2]))
				            			{{$lv[$employee->associate_id][2]->leave_type}} Leave
				            		@elseif(isset($lv[$employee->associate_id][$date[0]]))
				            			{{$lv[$employee->associate_id][$date[0]]->leave_type}} Leave
				            		@elseif(isset($do[$employee->associate_id][$date[0]]))
				            			DayOff
				            		@elseif(isset($ab[$employee->associate_id][$date[0]]))
				            			Absent
				            		@else
				            			Absent
				            		@endif
				            		</b>
				            		
				            	</td>	
				            	@endif
				            	@if(isset($pr[$employee->as_id][$date[1]]))
				            	<td style="text-align: center;">
				            		@if(isset($pr[$employee->as_id][$date[1]]))
				            		 	@if(($pr[$employee->as_id][$date[1]]->in_time || $pr[$employee->as_id][$date[1]]->in_time != null || $pr[$employee->as_id][$date[1]]->in_time != 'DSI') && in_array($pr[$employee->as_id][$date[1]]->in_unit,[1,4,5]))
					            			<p class="lib-roster text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[1]]->in_time))}}</p>
				            		 	@endif
				            		
				            		@endif


				            		
				            	</td>
				            	<td style="text-align: center;">
				            		@if(isset($pr[$employee->as_id][$date[1]]))
				            		 	@if(($pr[$employee->as_id][$date[1]]->in_time || $pr[$employee->as_id][$date[1]]->in_time != null || $pr[$employee->as_id][$date[1]]->in_time != 'DSI') && in_array($pr[$employee->as_id][$date[1]]->in_unit,[2,8]))
					            			<p class="lib-roster-holiday text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[1]]->in_time))}}</p>
				            		 	@endif
				            		
				            		@endif

				            	</td>
				            	<td style="text-align: center;">
				            		@if(isset($pr[$employee->as_id][$date[1]]))
				            		 	@if(($pr[$employee->as_id][$date[1]]->in_time || $pr[$employee->as_id][$date[1]]->in_time != null || $pr[$employee->as_id][$date[1]]->in_time != 'DSI') && in_array($pr[$employee->as_id][$date[1]]->in_unit, [1001]))
					            			<p class="lib-roster-general text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[1]]->in_time))}}</p>
				            		 	@endif
				            		
				            		@endif


				            		
				            	</td>
				            	<td style="text-align: center;">
				            		@if(isset($pr[$employee->as_id][$date[1]]))
				            		 	@if(($pr[$employee->as_id][$date[1]]->in_time || $pr[$employee->as_id][$date[1]]->in_time != null || $pr[$employee->as_id][$date[1]]->in_time != 'DSI') && in_array($pr[$employee->as_id][$date[1]]->in_unit,[3]))
					            			<p class="lib-roster-ot text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[1]]->in_time))}}</p>
				            		 	@endif
				            		
				            		@endif
				            		
				            	</td>
				            	@else
				            	<td colspan="4" style="text-align: center;">
				            		<b>@if(isset($lv[$employee->associate_id][2]))
				            			{{$lv[$employee->associate_id][2]->leave_type}} Leave
				            		@elseif(isset($lv[$employee->associate_id][$date[1]]))
				            			{{$lv[$employee->associate_id][$date[1]]->leave_type}} Leave
				            		@elseif(isset($do[$employee->associate_id][$date[1]]))
				            			DayOff
				            		@elseif(isset($ab[$employee->associate_id][$date[1]]))
				            			Absent
				            		@else
				            			Absent
				            		@endif
				            		</b>
				            	</td>
				            	@endif
				            	<td style="text-align: center;">
				            		@if(isset($pr[$employee->as_id][$date[1]]))
				            		 	@if(($pr[$employee->as_id][$date[1]]->out_time || $pr[$employee->as_id][$date[1]]->out_time != null) && in_array($pr[$employee->as_id][$date[1]]->out_unit, [1,4,5]))
					            			<p class="lib-roster text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[1]]->out_time))}}</p>
				            		 	@endif
				            		@endif
				            		
				            	</td>
				            	<td style="text-align: center;">
				            		@if(isset($pr[$employee->as_id][$date[1]]))
				            		 	@if(($pr[$employee->as_id][$date[1]]->out_time || $pr[$employee->as_id][$date[1]]->out_time != null) && in_array($pr[$employee->as_id][$date[1]]->out_unit, [2,8]))
					            			<p class="lib-roster-holiday text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[1]]->out_time))}}</p>
				            		 	@endif
				            		@endif
				            		
				            	</td>
				            	<td style="text-align: center;">
				            		@if(isset($pr[$employee->as_id][$date[1]]))
				            		 	@if(($pr[$employee->as_id][$date[1]]->out_time || $pr[$employee->as_id][$date[1]]->out_time != null) && in_array($pr[$employee->as_id][$date[1]]->out_unit,[1001]))
					            			<p class="lib-roster-general text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[1]]->out_time))}}</p>
				            		 	@endif
				            		@endif
				            		
				            	</td>
				            	<td style="text-align: center;">
				            		@if(isset($pr[$employee->as_id][$date[1]]))
				            		 	@if(($pr[$employee->as_id][$date[1]]->out_time || $pr[$employee->as_id][$date[1]]->out_time != null) && in_array($pr[$employee->as_id][$date[1]]->out_unit,[3]))
					            			<p class="lib-roster-ot text-white">{{date('H:i', strtotime($pr[$employee->as_id][$date[1]]->out_time))}}</p>
				            		 	@endif
				            		@endif
				            		
				            	</td>
				            	{{-- <td style="text-align: center;">
				            		@if(isset($pr[$employee->as_id][$date[1]]))
				            		 	@if($pr[$employee->as_id][$date[1]]->ot_hour > 0 )
					            			{{numberToTimeClockFormat($pr[$employee->as_id][$date[1]]->ot_hour)}}
				            		 	@endif
				            		@endif
				            		
				            	</td> --}}
			            	</tr>
			            	
			            @endforeach
			            <tr style="border: 0!important;"><td colspan="11" style="border: 0!important;height: 10px;"></td></tr>
			            @else
				            <tr>
				            	<td colspan="11" class="text-center">No Employee Found!</td>
				            </tr>
			            @endif
		            </tbody>
				@endforeach
				</table>
			</div>
			@elseif(($input['report_format'] == 1 && $format != null))
			<div class="content_list_section" >
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
					}elseif($format == 'as_emp_type_id'){
						$head = 'Area';
					}else{
						$head = '';
					}
				@endphp
				<table class="table table-bordered table-hover table-head">
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
							<th style="text-align: center;">Employee</th>
						</tr>
					</thead>
					<tbody>
						@php $i=0; @endphp
						@if(count($uniqueGroups) > 0)
						@foreach($uniqueGroups as $group => $employee)
						<tr>
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
								@php
									if($format == 'as_unit_id'){
										if($group == 145){
											$body = 'MBM + MBF + MBM 2';
											$exPar = '&selected=145';
										}elseif($group == null){
											$body = '';
											$exPar = $group;
										}else{
											$body = $unit[$group]['hr_unit_name']??'';
											$exPar = '&selected='.$unit[$group]['hr_unit_id']??'';
										}
									}elseif($format == 'as_line_id'){

										if(isset($line[$group])){
											$body = $line[$group]['hr_line_name']??'';
											$exPar = '&selected='.$line[$group]['hr_line_id']??'';
										}else{
											$body = '-';
											$exPar = '&selected=null';
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
											$body = '-';
											$exPar = '&selected=null';
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
									}elseif($format == 'as_area_id'){
										$body = $area[$group]['hr_area_name']??'N/A';
										$exPar = '&selected='.$group??'';
									}else{
										$body = 'N/A';
									}
								@endphp
								<a class="generate-drawer" data-url="{{($urldata.$exPar)}}" data-body="{{ ($head.' : '.$body
								) }}" id="{{$exPar}}" class="select-group"> 
									{{ ($body == null)?'N/A':$body }} 
								</a>
								<span style="font-size: 9px;">
									@if(isset($secName)) - {{$secName}} @endif
									@if(isset($seDeName)) - {{$seDeName}} @endif
								</span>
							</td>
							<td style="text-align: center;">
								{{ count($employee)}}
								
							</td>
						</tr>
						@endforeach
						@else
						<tr>
			            	<td colspan="{{ ($format == 'as_subsection_id' || $format == 'as_subsection_id')?'5':'3'}}" class="text-center">No Employee Found!</td>
			            </tr>
						@endif
					</tbody>
					
				</table>
			</div>
			@endif
		</div>

	</div>
</div>