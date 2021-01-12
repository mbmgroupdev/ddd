<style type="text/css">
    @media print{@page {size: landscape}}
</style>
<div class="panel">
	<div class="panel-body">
			@php
				$urldata = http_build_query($input) . "\n";
			@endphp
			<a href='{{ url("hr/reports/summary/excel?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 15px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>
		<div class="report_section" id="report_section">
			@php
				$formatHead = explode('_',$format);
			@endphp
			
			<div class="top_summery_section">
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Overtime @if($input['report_format'] == 0) Details @else Summary @endif Report </h2>
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

		            			Date <b>: {{ $input['from_date']}} to {{ $input['to_date']}} </b> <br>
		            			@if($input['otnonot'] != null)
		                			<b> OT </b> 
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
	                			Total Employe
	                			<b>: {{$totalEmployees}}</b>
		                		
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
			                    {{-- <th>Photo</th> --}}
			                    <th style="width:60px;">Associate ID</th>
			                    <th style="width:100px;">Name & Phone</th>
			                    <th style="width:5%;">Oracle ID</th>
			                    <th style="width:10%;">Designation</th>
			                    <th style="width:10%;">Department</th>
			                    <th style="width:5%;">Section</th>
			                    <th style="width:10%;">Sub Section</th>
			                    <th style="width:5%;">Floor</th>
			                    <th style="width:5%;">Line</th>
			                    <th style="width:5%;">Days</th>
			                    <th style="width:5%;">OT Hour</th>
			                    <th style="width:5%;">Action</th>
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
			            		
			                    $otHour = numberToTimeClockFormat($employee->ot_hour);
			                    

			            	@endphp
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	{{-- <td><img src="{{ emp_profile_picture($employee) }}" class='small-image' style="height: 40px; width: auto;"></td> --}}
				            	<td><a href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank">{{ $employee->associate_id }}</a></td>
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
				            	<td>{{ $otHour }}</td>
				            	<td>
				            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Yearly Activity Report' ><i class="fa fa-eye"></i></button>
				            	</td>
			            	</tr>
			            	@php 
			            		$totalOt += $employee->ot_hour; 
			            	@endphp
			            @endforeach
			            	{{-- <tr>
			            		<td colspan="10"></td>
			            		<td colspan="2"><b>Total Employee</b></td>
			            		<td colspan="2"><b>{{ $i }}</b></td>
			            	</tr> --}}
			            	<tr>
			            		<td colspan="11" style="text-align: right;"><b>Total</b></td>
			            		<td  style="text-align: right"><b>
			            			@php
			            			
				                    $otHourE = numberToTimeClockFormat($totalOt);
				                    echo $otHourE;
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

						$i = 0; $tot0 = 0; $tot53 = 0;$tot61 = 0;$tot71 = 0;$tot81 = 0;$tot91 = 0;$tot100 = 0;
					@endphp
					<table class="table table-bordered table-hover table-head text-center table-striped" border="1" style="text-align: center;">
						<thead>
							<tr>
								<td rowspan="2">Sl</td>
								<td rowspan="2">{{ $head }}</td>
								<td rowspan="2">Total</td>
								<td colspan="2"> 0-52 Hrs</td>
								<td colspan="2"> 53-60 Hrs</td>
								<td colspan="2"> 61-70 Hrs</td>
								<td colspan="2"> 71-80 Hrs</td>
								<td colspan="2"> 81-90 Hrs</td>
								<td colspan="2"> 91-100 Hrs</td>
								<td colspan="2"> 100+ Hrs</td>
								<td rowspan="2">Total</td>
							</tr>
							<tr>
								<td style="font-size: 9px;">Worker</td>
								<td style="font-size: 9px;">%</td>
								<td style="font-size: 9px;">Worker</td>
								<td style="font-size: 9px;">%</td>
								<td style="font-size: 9px;">Worker</td>
								<td style="font-size: 9px;">%</td>
								<td style="font-size: 9px;">Worker</td>
								<td style="font-size: 9px;">%</td>
								<td style="font-size: 9px;">Worker</td>
								<td style="font-size: 9px;">%</td>
								<td style="font-size: 9px;">Worker</td>
								<td style="font-size: 9px;">%</td>
								<td style="font-size: 9px;">Worker</td>
								<td style="font-size: 9px;">%</td>
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
													if($input['unit'] == 145){
														$uid = $line[$group]['hr_line_unit_id'];	
														$body .= '('.$unit[$uid]['hr_unit_short_name'].')';
													}
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
												$depId = $subSection[$group]['hr_subsec_department_id']??'';
												$seDeName = $department[$depId]['hr_department_name']??'';
												$secId = $subSection[$group]['hr_subsec_section_id']??'';
												$secName = $section[$secId]['hr_section_name']??'';
												$body = $subSection[$group]['hr_subsec_name']??'';
												$exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
											}elseif($format == 'ot_hour'){
												
							                    $otHourBody = numberToTimeClockFormat($group);
												$body = $otHourBody??'N/A';
											}else{
												$body = 'N/A';
											}
										@endphp


										<a class="generate-drawer" data-url="{{($urldata.$exPar)}}" data-body="{{ ($head.' : '.$body
										) }}" id="{{$exPar}}" > 
											{{ ($body == null)?'N/A':$body }} 
										</a>
										<span style="font-size: 9px;">
											@if(isset($secName)) - {{$secName}} @endif
											@if(isset($seDeName)) - {{$seDeName}} @endif
										</span>

									</td>

									{{-- generate hourly button --}}
									@php 
										$durl = $urldata.$exPar.'&&filter=';
										$dbody = $head.' : '.$body.' & Hour: ';
									@endphp


									<td>{{count($employees)}}</td>
									<td>
										<a class="generate-drawer" data-url="{{($durl.'0-52')}}" data-body="{{ ($dbody.'0-52') }}"  >{{$ot0}}</a>
									</td>
									<td>
										{{round($ot0/$total*100)}}
									</td>
									<td>
										<a class="generate-drawer" data-url="{{($durl.'53-60')}}" data-body="{{ ($dbody.'53-60') }}"  >{{$ot53}}</a>
									</td>
									<td>
										{{round($ot53/$total*100)}}
									</td>
									<td>
										<a class="generate-drawer" data-url="{{($durl.'61-70')}}" data-body="{{ ($dbody.'61-70') }}"  >{{$ot61}}</a>
									</td>
									<td>
										{{round($ot61/$total*100)}}
									</td>
									<td>
										<a class="generate-drawer" data-url="{{($durl.'71-80')}}" data-body="{{ ($dbody.'71-80') }}"  >{{$ot71}}</a>
									</td>
									<td>
										{{round($ot71/$total*100)}}
									</td>
									<td>
										<a class="generate-drawer" data-url="{{($durl.'81-90')}}" data-body="{{ ($dbody.'81-90') }}"  >{{$ot81}}</a>
									</td>
									<td>
										{{round($ot81/$total*100)}}
									</td>
									<td>
										<a class="generate-drawer" data-url="{{($durl.'91-100')}}" data-body="{{ ($dbody.'91-100') }}"  >{{$ot91}}</a>
									</td>
									<td>
										{{round($ot91/$total*100)}}
									</td>
									<td>
										<a class="generate-drawer" data-url="{{($durl.'100')}}" data-body="{{ ($dbody.'100+') }}"  >{{$ot100}}</a>
									</td>
									<td>
										{{round($ot100/$total*100)}}
									</td>
									<td>{{count($employees)}}</td>
								</tr>
							@endforeach
								<tr>
									<th></th>
									<th style="text-align: left;">

										Total
										

									</th>

									{{-- generate hourly button --}}
									@php 
										$durl = $urldata.'&&filter=';
										$dbody = 'All employee & Hour: ';
										$total = $totalEmployees == 0?1:$totalEmployees;
									@endphp


									<th>{{$totalEmployees}}</th>
									<th>
										<a class="generate-drawer" data-url="{{($durl.'0-52')}}" data-body="{{ ($dbody.'0-52') }}"  >{{$tot0}}</a>
									</th>
									<th>
										{{round($tot0/$total*100)}}
									</th>
									<th>
										<a class="generate-drawer" data-url="{{($durl.'53-60')}}" data-body="{{ ($dbody.'53-60') }}"  >{{$tot53}}</a>
									</th>
									<th>
										{{round($tot53/$total*100)}}
									</th>
									<th>
										<a class="generate-drawer" data-url="{{($durl.'61-70')}}" data-body="{{ ($dbody.'61-70') }}"  >{{$tot61}}</a>
									</th>
									<th>
										{{round($tot61/$total*100)}}
									</th>
									<th>
										<a class="generate-drawer" data-url="{{($durl.'71-80')}}" data-body="{{ ($dbody.'71-80') }}"  >{{$tot71}}</a>
									</th>
									<th>
										{{round($tot71/$total*100)}}
									</th>
									<th>
										<a class="generate-drawer" data-url="{{($durl.'81-90')}}" data-body="{{ ($dbody.'81-90') }}"  >{{$tot81}}</a>
									</th>
									<th>
										{{round($tot81/$total*100)}}
									</th>
									<th>
										<a class="generate-drawer" data-url="{{($durl.'91-100')}}" data-body="{{ ($dbody.'91-100') }}"  >{{$tot91}}</a>
									</th>
									<th>
										{{round($tot91/$total*100)}}
									</th>
									<th>
										<a class="generate-drawer" data-url="{{($durl.'100')}}" data-body="{{ ($dbody.'100+') }}"  >{{$tot100}}</a>
									</th>
									<th>
										{{round($tot100/$total*100)}}
									</th>
									<th>{{$totalEmployees}}</th>
								</tr>
						</tbody>
				@endif
		</div>

	</div>
</div>