<div class="panel">
	<div class="panel-body">
		@php
			$urldata = http_build_query($input) . "\n";
			$groupUnit = ($input['group_unit']??$input['unit']);
		@endphp
		<div class="content_list_section">
			<style type="text/css">
				.page-data{
				    border: 1px solid #d1d1d1;
				    margin: 7px 0;
				    padding: 5px 0;
				}
				.table th, .table td {
    				padding: 5px;
    			}
			</style>
			<div class="page-header report_section">
				
				<h3 style="font-weight: bold; text-align: center;"> {{ $unit[$groupUnit]['hr_unit_name']??'' }} </h3> 
				<h3 style=" text-align: center;">{{ $bonusType[$bonusSheet->bonus_type_id]['bonus_type_name']??''}}-{{ $bonusSheet->bonus_year }} @if($input['report_format'] == 0) Details @else Summary @endif Report </h3>
					
	            <table class="table no-border f-14" border="0" style="width:100%;margin-bottom:0;font-size:14px;text-align:left"  cellpadding="5">
	            	<tr>
	            		<td width="25%">
		            		Active <b>: {{ $summary->active }} Employee</b> <br>
		            		Amount <b>: ৳</b> <br>
	            		</td>
	            		<td width="25%">
		            		Partial (< 12 Month) <b>: Employee</b> <br>
		            		Amount <b>: ৳</b> <br>
	            		</td>
	            		<td width="25%">
		            		Maternity <b>: Employee</b> <br>
		            		Amount <b>: ৳</b> <br>
	            		</td>
	            		<td width="25%">
		            		Total <b>: Employee</b> <br>
		            		Amount <b>: ৳</b> <br>
	            		</td>
	            		
	            	</tr>
	            	
	            </table>
	            
	        </div>
			<input type="hidden" id="reportFormat" value="{{$input['report_format']}}">
			@if($input['report_format'] == 0)
				<table class="table table-bordered table-hover table-head table-responsive" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
				@foreach($uniqueGroupEmp as $group => $employees)
				
					<thead>
						@if(count($employees) > 0)
		                
		                	@php
								if($format == 'as_unit_id'){
									$head = 'Unit';
									$body = $unit[$group]['hr_unit_name']??'';
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
									$body = $subSection[$group]['hr_subsec_name']??'';
								}else{
									$head = '';
								}
							@endphp
		                	@if($head != '')
		                	<tr>
			                    <th colspan="2">{{ $head }}</th>
			                    <th colspan="10">{{ $body }}</th>
		                    </tr>
		                    @endif
		                
		                @endif
		                <tr>
		                    <th>Sl</th>
		                    <th>Associate ID</th>
		                    <th>Name</th>
		                    <th>Designation</th>
		                    <th>Department</th>
		                    <th>DOJ</th>
		                    <th>Gross</th>
		                    <th>Basic</th>
		                    <th>Month</th>
		                    <th>Stamp</th>
		                    <th>Bonus Amount</th>
		                </tr>
		            </thead>
		            <tbody>
		            @php $i = 0; $otHourSum=0; $salarySum=0; @endphp
		            @if(count($employees) > 0)
			            @foreach($employees as $employee)
			            	@php
			            		$designationName = $employee->hr_designation_name??'';
			            	@endphp
			            	
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	
				            	<td>{{ $employee->associate_id }}</td>
				            	<td><b>{{ $employee->as_name }}</b></td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td style="white-space: nowrap;">{{$employee->as_doj}}</td>
				            	<td>{{$employee->gross_salary }}</td>
				            	<td>{{$employee->basic}}</td>
				            	<td>
				            		@if($employee->duration < 12)
				            			{{$employee->duration}}/12
				            		@else
				            			12/12
				            		@endif
				            	</td>
				            	
				            	
				            	<td>{{$employee->stamp }}</td>
				            	<td>{{$employee->bonus_amount }}</td>
			            	</tr>
			            	
			            @endforeach
		            @else
			            <tr>
			            	<td colspan="13" class="text-center">No Employee Found!</td>
			            </tr>
		            @endif
		            	<tr style="border:0 !important;"><td colspan="16" style="border: 0 !important;height: 20px;"></td> </tr>
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
				<table class="table table-bordered table-hover table-head" border="1" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" cellpadding="5">
					<!-- custom design for all-->
					<thead>
						<tr class="text-center">
							<th rowspan="2" style="vertical-align: middle;">Sl</th>
							<th rowspan="2" style="vertical-align: middle;"> {{ $head }} Name</th>
							<th colspan="2">NonOT</th>
							<th colspan="2">OT</th>
							<th colspan="2">Total</th>
						</tr>
						<tr class="text-center">
							<th>Employee</th>
							<th>Amount</th>
							<th>Employee</th>
							<th>Amount</th>
							<th>Employee</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						@php $i = 0; @endphp
						@if(count($getEmployee) > 0)
						@foreach($getEmployee as $employee)
						<tr>

							<td>{{ ++$i }}</td>
							<td>
								@php
									$group = $employee->$format;
									if($format == 'as_unit_id'){
										$body = $unit[$group]['hr_unit_name']??'';
										$exPar = '&selected='.$unit[$group]['hr_unit_id']??'';
									}elseif($format == 'as_line_id'){
										$body = $line[$group]['hr_line_name']??'';
										$exPar = '&selected='.$body;
									}elseif($format == 'as_floor_id'){
										$body = $floor[$group]['hr_floor_name']??'';
										$exPar = '&selected='.$body;
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
										$body = $seDeName.' - '.$seName;
										$exPar = '&selected='.$section[$group]['hr_section_id']??'';
									}elseif($format == 'as_subsection_id'){
										$body = $subSection[$group]['hr_subsec_name']??'';
										$exPar = '&selected='.$subSection[$group]['hr_subsec_id']??'';
									}else{
										$body = 'N/A';
										$exPar = '';
									}
									$secUrl = $urldata.$exPar;
								@endphp
								<a onClick="selectedGroup(this.id, '{{ $body }}')" data-body="{{ $body }}" id="{{$exPar}}" class="select-group">{{ ($body == null)?'N/A':$body }}</a>
							</td>
							<td style="text-align: center;">
								{{ $employee->nonot }}
							</td>

							<td style="text-align: right;padding-right: 5px;">
								{{ $employee->totalNonOt }}
							</td>
							<td style="text-align: center;">
								{{ $employee->ot }}
							</td>

							<td style="text-align: right;padding-right: 5px;">
								{{ $employee->totalOt }}
							</td>
							<td style="text-align: center;">
								{{ $employee->total }}
							</td>

							<td style="text-align: right;padding-right: 5px;">
								{{ $employee->groupTotal }}
							</td>
							
							
						</tr>
						@endforeach
						
						
						@else
						<tr>
			            	<td colspan="8" class="text-center">No Data Found!</td>
			            </tr>
						@endif
					</tbody>
					
				</table>
			@endif
		</div>
	</div>
</div>



<script>
	@if(auth()->user()->hasRole('Buyer Mode'))
    	var mainurl = '/hrm/reports/group-salary-sheet-details?';
    @else
    	var mainurl = '/hr/reports/group-salary-sheet-details?';
    @endif
    function selectedGroup(e, body){
    	var part = e;
    	var input = @json($urldata);
    	var pareUrl = input+part;
    	$("#modal-title-right").html(' '+body+' Salary Details');
    	$('#right_modal_lg').modal('show');
    	$("#content-result").html(loaderContent);
    	$.ajax({
            url: mainurl+pareUrl,
            data: {
                body: body
            },
            type: "GET",
            success: function(response){
            	// console.log(response);
                if(response !== 'error'){
                	setTimeout(function(){
                		$("#content-result").html(response);
                	}, 1000);
                }else{
                	console.log(response);
                }
            }
        });

    }
    function printDiv(divName)
    {   
        var mywindow=window.open('','','width=800,height=800');
        
        mywindow.document.write('<html><head><title>Print Contents</title>');
        mywindow.document.write('<style>@page {size: landscape; color: color;} </style>');
        mywindow.document.write('</head><body>');
        mywindow.document.write(document.getElementById(divName).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close();  
        mywindow.focus();           
        mywindow.print();
        mywindow.close();
    }
</script>