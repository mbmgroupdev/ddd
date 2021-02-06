<div class="panel">
	<div class="panel-body">
		<div class="report_section" id="report_section">
			@php
				$formatHead = explode('_',$format);
				$urldata = http_build_query($input) . "\n";
				$jsonUrl = json_encode($urldata);
			@endphp
			
			<div class="top_summery_section">
				
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">{{ $input['report_type'] == 1?'Holiday':'Leave'}} @if($input['report_format'] == 0) Details @else Summary @endif Report</h2>
		            
		            <table class="table no-border f-16" border="0">
		            	<tr>
		            		<td>
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
		            			Date <b>: {{ $input['date']}} </b> <br>
		            			@if($input['otnonot'] != null)
		                			<b> OT </b> 
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
		                		Total Employee
		                			<b>: {{ $totalEmployees }}</b>
		                		
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
		        
			</div>
			<div class="content_list_section">
				@if($input['report_format'] == 0)
					<form class="" role="form" id="undeclaredReport" method="get" action="#">
						<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
							<input type="hidden" name="assignDates" class="form-control" value="{{ $input['date'] }}">
						@php $pageKey = 0; @endphp
						@foreach($uniqueGroupEmp as $group => $employees)
							<thead>
								@if(count($employees) > 0)
				                <tr>
				                	@php
				                		$pageKey += 1;
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
											$body = $section[$group]['hr_section_name']??'';
										}elseif($format == 'as_subsection_id'){
											$head = 'Sub Section';
											$body = $subSection[$group]['hr_subsec_name']??'N/A';
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
				                    <th width="2%">Sl</th>
				                    <th width="4%">Photo</th>
				                    <th width="10%">ID</th>
				                    <th width="16%">Name & Phone</th>
				                    <th width="10%">Designation</th>
				                    <th width="15%">Department</th>
				                    <th width="9%">Section</th>
				                    <th width="9%">Sub Section</th>
				                    <th width="5%">Floor</th>
				                    <th width="5%">Line</th>
				                    <th width="5%">Time</th>
				                    <th width="5%">
				                    	@if($input['report_type'] == 1)
				                    	<input type="checkbox" class="checkBoxGroup"onclick="checkAllGroup(this)" id="{{ $pageKey }}"/>
				                    	@endif
				                    </th>
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
						            	<td><img src="{{ emp_profile_picture($employee) }}" class='small-image min-img-file'></td>
						            	<td>
						            		<a class="job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}" data-toggle="tooltip" data-placement="top" title="" data-original-title="Job Card">{{ $employee->associate_id }}</a>
						            		<p>{{ $employee->as_oracle_code }}</p>
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
						            	<td>{{ (($employee->punch_time != null && $employee->punch_time != '00:00:00')?date('H:i:s', strtotime($employee->punch_time)):'') }}</td>
						            		
						            	<td>
						            		@if($input['report_type'] == 1)
						            		<input type='checkbox' class="selected-{{$pageKey}}" value="{{ $employee->associate_id }}" name='assigned[]'/>
						            		
						            		@endif
						            	</td>
					            	</tr>
					            @endforeach
					            @else
						            <tr>
						            	<td colspan="12" class="text-center">No Employee Found!</td>
						            </tr>
					            @endif
					            <tr style="border:0 !important;"><td colspan="12" style="border: 0 !important;height: 20px;"></td> </tr>
				            </tbody>
						@endforeach
						</table>
						@if($input['report_type'] == 1)
						<div class="row">
							<div class="col-8">
								<div class="form-group has-float-label has-required">
                                    <input type="text" class="report_date form-control" id="comment" name="comment" placeholder="Enter Comment" value="" autocomplete="off" />
                                    <label for="comment">Comment</label>
                                </div>
							</div>
							<div class="col-2 pl-0">
								<div class="form-group">
									<select name="type" id="" class="form-control">
										<option value="General">General</option>
										<option value="OT">OT</option>
									</select>
								</div>
							</div>
				            <div class="col-2 pl-0 form-group disburse-button submit-btn" id="">
				                <button class="btn btn-primary nextBtn btn-lg pull-right" type="button"><i class="fa fa-save"></i> Submit</button>
				            </div>
				        </div>
				        @endif
					</form>
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
								<th>Employee</th>
							</tr>
						</thead>
						<tbody>
							@php $i=0; @endphp
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
							</tr>
							@endforeach
							@else
							<tr>
				            	<td colspan="{{ ($format == 'as_subsection_id' || $format == 'as_subsection_id')?'5':'3'}}" class="text-center">No Employee Found!</td>
				            </tr>
							@endif
						</tbody>
						
					</table>
				@endif
			</div>
		</div>

		{{-- modal --}}
	</div>
	
</div>

