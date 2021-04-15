<div class="panel">
	<div class="panel-body">
		<div class="row">
			<div class="col-sm-5">
				<button class="btn btn-sm btn-danger" id="back-button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Back to bous rule" title="back">
					<i class="fa fa-arrow-left"></i></button>
				@php
					$urldata = http_build_query($input) . "\n";
				@endphp
				<button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('content_list_section')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report" ><i class="las la-print"></i> </button>
				<a href='{{ url("hr/reports/summary/excel?$urldata")}}' target="_blank" class="btn btn-sm btn-primary hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" ><i class="fa fa-file-excel-o"></i></a>
			</div>
			<div class="col-sm-2"></div>
			<div class="col-sm-5">
				<div class="row">
					<div class="col-4 pr-0">
						<div class="form-group has-float-label select-search-group mb-0">
                            <?php
                                $emptype = ['all'=>'All','maternity'=>'Maternity','partial'=>'Less than a Year'];
                            ?>
                            {{ Form::select('empType', $emptype, $input['emp_type'], ['class'=>'form-control capitalize', 'id'=>'empType']) }}
                            <label for="empType">Bonus Type</label>
                        </div>
					</div>
                    <div class="col-5 pr-0">
                      <div class="format">
                        <div class="form-group has-float-label select-search-group mb-0">
                            <?php
                                $type = ['as_unit_id'=>'Unit','as_designation_id'=>'Designation','as_floor_id'=>'Floor','as_department_id'=>'Department','as_section_id'=>'Section','as_subsection_id'=>'Sub Section'];
                            ?>
                            {{ Form::select('report_group_select', $type, $input['report_group'], ['class'=>'form-control capitalize', 'id'=>'reportGroupHead']) }}
                            <label for="reportGroupHead">Report Format</label>
                        </div>
                      </div>
                    </div>
                    <div class="col-3 pl-0">
                      <div class="text-right">
                        <a class="btn view grid_view no-padding " data-toggle="tooltip" data-placement="top" title="" data-original-title="Summary Report View" id="1">
                          <i class="las la-th-large @if($input['report_format'] == 1) btn-primary @endif"></i>
                        </a>
                        <a class="btn view list_view no-padding " data-toggle="tooltip" data-placement="top" title="" data-original-title="Details Report View" id="0">
                          <i class="las la-list-ul @if($input['report_format'] == 0) btn-primary @endif"></i>
                        </a>
                        
                      </div>
                    </div>
                  </div>
			</div>
		</div>
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
    			.amount{text-align: right;width: 100px;display: inline-block;}
			</style>
			<div class="page-header">
	            
	            <div class="row page-data">
	            	<div class="col-sm-12 mb-3">
	            		<h5 style="margin:5px 10px; font-weight: bold; text-align: center;text-decoration: underline;">Bonus Eligble List</h5>
	            	</div>
	            	<div class="col-sm-4">
	            		<div class="row">
	            			<div class="col-6">OT Employee</div>
	            			<div class="col-6">: <span class="amount" >{{$summary->ot}}</span></div>
	            			<div class="col-6">Non-OT Employee</div>
	            			<div class="col-6">: <span class="amount" >{{$summary->nonot}}</span></div>
	            			<div class="col-12"><hr style="margin: 0"></div>
	            			<div class="col-6"><b>Total Employee</b></div>
	            			<div class="col-6">: <span class="amount" ><b>{{$summary->active + $summary->maternity}}</b></span></div>
	            			<div class="col-6">OT Employee Bonus</div>
	            			<div class="col-6">: <span class="amount" >৳ {{bn_money($summary->ot_amount)}}</span> </div>
	            			
	            			<div class="col-6">Non-OT Employee Bonus</div>
	            			<div class="col-6">:<span class="amount" >৳ {{bn_money($summary->nonot_amount)}} </span></div>
	            			<div class="col-12"><hr style="margin: 0"></div>
	            			<div class="col-6"><b>Total Bonus</b></div>
	            			<div class="col-6">: <span class="amount" ><b>৳ {{bn_money($summary->active_amount + $summary->maternity_amount)}}</b></div>
	            		</div>
	            	</div>
	            	<div class="col-sm-4 pr-0">
	            		<div class="row">
	            			<div class="col-7">Active Employee</div>
	            			<div class="col-5">: <span class="amount" >{{$summary->active}}</span></div>
	            			<div class="col-7">Maternity Employee</div>
	            			<div class="col-5">: <span class="amount" >{{$summary->maternity}}</span></div>
	            			<div class="col-7">Active Employee Bonus</div>
	            			<div class="col-5">: <span class="amount" >৳ {{bn_money($summary->active_amount)}}</span></div>
	            			
	            			<div class="col-7">Maternity Employee Bonus</div>
	            			<div class="col-5">: <span class="amount" >৳ {{bn_money($summary->maternity_amount)}}</span> </div>

	            		</div>
	            	</div>
	            	<div class="col-sm-4 pr-0">
	            		<div class="row">
	            			<div class="col-7">Less than a year Employee</div>
	            			<div class="col-5">: <span class="amount" >{{$summary->partial}}</span></div>
	            			<div class="col-7">Less than a year Bonus Amount</div>
	            			<div class="col-5">: <span class="amount" >৳ {{bn_money($summary->partial_amount)}}</span> </div>
	            			<div class="col-12">
			            		<div class="text-right d-block w-100 pr-3">
			            			<br><br>
			            			<button id="approval" class="btn btn-primary btn-sm ">Proceed to Aproval</button>
		            			</div>
	            			</div>
	            		</div>
	            	</div>
	            	
	            	
	            </div>
	        </div>
			<input type="hidden" id="reportFormat" value="{{$input['report_format']}}">
			@if($input['report_format'] == 0)
				<table class="table table-bordered table-hover table-head" style="width:100%;border:0 !important;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
				@foreach($uniqueGroup as $group => $employees)
				
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
		                    <th width="5%">Sl</th>
		                    <th width="8%">Associate ID</th>
		                    <th width="10%">Name</th>
		                    <th width="11%">Designation</th>
		                    <th width="14%">Department</th>
		                    <th width="10%">DOJ</th>
		                    <th width="10%">Gross</th>
		                    <th width="10%">Basic</th>
		                    <th width="6%">Month</th>
		                    <th width="6%">Stamp</th>
		                    <th width="10%">Bonus Amount</th>
		                </tr>
		            </thead>
		            <tbody>
		            @php $i = 0; $otHourSum=0; $salarySum=0; $month = $input['month']??''; @endphp
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
				            	<td>{{$employee->ben_current_salary}}</td>
				            	<td>{{$employee->ben_basic}}</td>
				            	<td>
				            		@if($employee->month < 12)
				            			{{$employee->month}}/12
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
						@if(count($uniqueGroup) > 0)
						@foreach($uniqueGroup as $group => $employee)
						<tr>
							<td>{{ ++$i }}</td>
							<td>
								@php
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
								{{ bn_money($employee->nonot_amount) }}
							</td>
							<td style="text-align: center;">
								{{ $employee->ot }}
							</td>

							<td style="text-align: right;padding-right: 5px;">
								{{ bn_money($employee->ot_amount) }}
							</td>
							<td style="text-align: center;">
								{{ $employee->ot + $employee->nonot }}
							</td>

							<td style="text-align: right;padding-right: 5px;">
								{{ bn_money($employee->ot_amount + $employee->nonot_amount) }}
							</td>
							
							
						</tr>
						@endforeach
						<tr>
							<th style="text-align: center;" colspan="2">Total</th>
							<th style="text-align: center;">{{collect($uniqueGroup)->sum('ot')}}</th>
							<th style="text-align: right;padding-right: 5px;">{{bn_money(collect($uniqueGroup)->sum('ot_amount'))}}</th>
							<th style="text-align: center;">{{collect($uniqueGroup)->sum('nonot')}}</th>
							<th style="text-align: right;padding-right: 5px;">{{bn_money(collect($uniqueGroup)->sum('nonot_amount'))}}</th>
							<th style="text-align: center;">{{collect($uniqueGroup)->sum('ot') + collect($uniqueGroup)->sum('nonot')}}</th>
							<th style="text-align: right;padding-right: 5px;">{{bn_money(collect($uniqueGroup)->sum('ot_amount') + collect($uniqueGroup)->sum('nonot_amount'))}}</th>
						</tr>
						
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
	var mainurl = '/hr/operation/bonus-process?';
    function selectedGroup(e, body){
    	// console.log(body)
    	var part = e;
    	var input = @json($urldata);
    	var pareUrl = input+part;
    	$('#right_modal_jobcard').modal('show');
	    $('#modal-title-right').html(body+' Report');
	    $("#content-result").html(loaderContent);
    	$.ajax({
            url: mainurl+pareUrl,
            data: {
                body: body
            },
            type: "POST",
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
</script>