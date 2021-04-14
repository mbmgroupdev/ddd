<style type="text/css">
	.format{display: none!important;}
</style>
<div class="panel">
	<div class="panel-body">
		<div class="report_section" id="report_section">
			@php
				$urldata = http_build_query($input) . "\n";
				$jsonUrl = json_encode($urldata);
			@endphp
			
			<div class="top_summery_section">
				@if($input['report_format'] == 0 || ($input['report_format'] == 1 && $format != null))
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Assigned Shift @if($input['report_format'] == 0) Details @else Summary @endif Report</h2>
		            
		            <table class="table no-border f-16" border="0">
		            	<tr>
		            		<td style="width: 33%">
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
		            		<td style="width: 33%;text-align: center;">
		            			Date <b>: {{ $input['date']}} </b> <br>
		            			@if($input['otnonot'] != null)
		                			<b> OT </b> 
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
		                		Total Employee
		                			<b>: {{$totalEmployees}}</b>
		                		
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
		                		Format 
		                			<b class="capitalize">: Shift </b>
		            		</td>
		            	</tr>
		            	
		            </table>
		            
		        </div>
		        @else
		        <div class="page-header-summery">
        			<h2>Assigned Shift Report </h2>
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
        			<h4>Date: {{ $input['date']}}</h4>
        			<h4>Total  Employee: <b>{{$totalEmployees}}</b></h4>
		            		
		        </div>
		        @endif
			</div>
			<div class="content_list_section">
				@if($input['report_format'] == 0)
					<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
					@foreach($employees as $group => $shifts)
						<thead>
							@if(count($shifts) > 0)
			                <tr>
			                    <th colspan="2">Shift</th>
			                    <th colspan="10">{{ $group }}</th>
			                </tr>
			                @endif
			                <tr>
			                    <th width="2%">Sl</th>
			                    <th width="10%">Associate ID</th>
			                    <th width="16%">Name & Phone</th>
			                    <th width="12%">Shift</th>
			                    <th width="8%">Oracle ID</th>
			                    <th width="10%">Designation</th>
			                    <th width="9%">Department</th>
			                    <th width="9%">Section</th>
			                    <th width="9%">Sub Section</th>
			                    <th width="5%">Floor</th>
			                    <th width="5%">Line</th>
			                    <th width="5%">Action</th>
			                </tr>
			            </thead>
			            <tbody>
				            @php
				             $i = 0; $month = date('Y-m',strtotime($input['date'])); 
				            @endphp
				            @if(count($shifts) > 0)
				            @foreach($shifts as $employee)
				            	@php
				            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
				            	@endphp
				            	
				            	<tr>
				            		<td>{{ ++$i }}</td>
					            	<td>
					            		<a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->associate_id }}</a>
					            	</td>
					            	<td>
					            		<b>{{ $employee->as_name }}</b>
					            		<p>{{ $employee->as_contact }}</p>
					            	</td>
					            	<td>
					            		@if(isset($absentShift[$employee->associate_id]) && $absentShift[$employee->associate_id] != null)
					            			{{ $absentShift[$employee->associate_id] }}
					            		@else
					            			{{ $employee->as_shift_id }}
					            		@endif
					            	</td>
					            	<td>{{ $employee->as_oracle_code }}</td>
					            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
					            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
					            	<td>{{ $section[$employee->as_section_id]['hr_section_name']??'' }}</td>
					            	<td>{{ $subSection[$employee->as_subsection_id]['hr_subsec_name']??'' }}</td>
					            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
					            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
					            	
					            	<td>
					            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Yearly Activity Report' ><i class="fa fa-eye"></i></button>
					            	</td>
				            	</tr>
				            	
				            @endforeach
				            @else
					            <tr>
					            	<td colspan="11" class="text-center">No Absent Employee Found!</td>
					            </tr>
				            @endif
				            <tr style="border:0 !important;"><td colspan="14" style="border: 0 !important;height: 20px;"></td> </tr>
			            </tbody>
					@endforeach
					</table>
				@elseif(($input['report_format'] == 1 && $format != null))
					<table class="table table-bordered table-hover table-head">
						<thead>
							<tr>
								<th style="text-align: center;">Sl</th>
								<th>Shift Name</th>
								<th style="text-align: center;">Male</th>
								<th style="text-align: center;">Female</th>
								<th style="text-align: center;">Total</th>
							</tr>
						</thead>
						<tbody>
							@php $i=0; @endphp
							@if(count($employees) > 0)
							@foreach($employees as $group => $shift)
								<tr class="cursor-pointer" onClick="selectedGroup(this.id, '', {{ $jsonUrl }})" data-body="" id="&shift_id={{$group}}" data-url="{{ $jsonUrl }}" class="select-group">
									<td style="text-align: center;">{{ ++$i }}</td>
									<td>{{ $group }}</td>
									<td style="text-align: center;">{{$shift->male??0}}</td>
									<td style="text-align: center;">{{$shift->female??0}}</td>
									<td style="text-align: center;">{{ ($shift->female + $shift->male) }}</td>
								</tr>
							@endforeach
								<tr style="text-align: center;">
									<th colspan="2">Total</th>
									<th>{{ collect($employees)->sum('male')}}</th>
									<th>{{ collect($employees)->sum('female')}}</th>
									<th>{{ collect($employees)->sum('male') + collect($employees)->sum('female')}}</th>
								</tr>
							@else
							<tr>
				            	<td colspan="5" class="text-center">No Absent Employee Found!</td>
				            </tr>
							@endif
						</tbody>
						
					</table>
				@endif
			</div>
		</div>
	</div>
</div>

