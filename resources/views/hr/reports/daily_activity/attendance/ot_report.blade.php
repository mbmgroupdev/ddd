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
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">{{ $input['date']}} OT Report </h2>
		            <h4 style="margin:4px 10px; font-weight: bold; text-align: center;">@if($input['report_format'] == 0) Details @else Summary @endif Report</h4>
		            <div class="row">
		            	<div class="col-5">
		            		<div class="row">
		                		<div class="col-2 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Group: </font></h4>
		                		</div>
		                		<div class="col-10 pl-0">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0; text-transform: capitalize;">&nbsp;&nbsp;{{ isset($formatHead[1])?$formatHead[1].' Wise':'N/A' }}</h4>
		                		</div>
		                		
		                		<div class="col-2 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Unit: </font></h4>
		                		</div>
		                		<div class="col-10 pl-0">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $unit[$input['unit']]['hr_unit_name'] }}</h4>
		                		</div>
		                		
		                		<div class="col-2 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Area: </font></h4>
		                		</div>
		                		<div class="col-10 pl-0">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $area[$input['area']]['hr_area_name'] }}</h4>
		                		</div>
		                		@if($input['department'] != null)
		                		<div class="col-2 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Department: </font></h4>
		                		</div>
		                		<div class="col-10 pl-0">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $department[$input['department']]['hr_department_name'] }}</h4>
		                		</div>
		                		@endif
		            		</div>
		            	</div>
		            	<div class="col-4 no-padding">
		            		<div class="row">
		                		<div class="col-4 pr-0">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Total OT Hour: </font></h4>
		                		</div>
		                		<div class="col-8 pl-0">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $totalValue }}</h4>
		                		</div>
		                		
		                		
		                		<div class="col-4 pr-0">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Total Employee: </font></h4>
		                		</div>
		                		<div class="col-8 pl-0">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $totalEmployees }}</h4>
		                		</div>
		                	</div>
		            	</div>
		            	<div class="col-3 no-padding">
		            		<div class="row">
		                		@if($input['section'] != null)
		                		<div class="col-3 no-padding-right">
		                			<h4 style="margin:4px 5px; text-align:right; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Section: </font></h4>
		                		</div>
		                		<div class="col-9">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $section[$input['section']]['hr_section_name'] }}</h4>
		                		</div>
		                		@endif
		                		@if($input['subSection'] != null)
		                		<div class="col-3 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Sub Section: </font></h4>
		                		</div>
		                		<div class="col-9">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $subSection[$input['subSection']]['hr_subsec_name'] }}</h4>
		                		</div>
		                		@endif
		                		@if($input['floor_id'] != null)
		                		<div class="col-3 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Floor: </font></h4>
		                		</div>
		                		<div class="col-9">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $floor[$input['floor_id']]['hr_floor_name'] }}</h4>
		                		</div>
		                		@endif
		                		@if($input['line_id'] != null)
		                		<div class="col-3 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Line: </font></h4>
		                		</div>
		                		<div class="col-9">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $line[$input['line_id']]['hr_line_name'] }}</h4>
		                		</div>
		                		@endif
		                	</div>
		            	</div>
		            </div>
		        </div>
		        @else
		        <div class="page-header-summery">
        			
        			<h2>{{ $input['date']}} Employee OT Summary Report </h2>
        			<h4>Unit: {{ $unit[$input['unit']]['hr_unit_name'] }}</h4>
        			<h4>Area: {{ $area[$input['area']]['hr_area_name'] }}</h4>
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

        			<h4>Total Late Employee: <b>{{ $totalEmployees }}</b></h4>
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
			                    <th colspan="9">{{ $body }}</th>
			                    @endif
			                </tr>
			                @endif
			                <tr>
			                    <th>Sl</th>
			                    {{-- <th>Photo</th> --}}
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
			            @php $i = 0; $totalOt=0; @endphp
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
				            	{{-- <td><img src="{{ $employee->as_pic }}" class='small-image' onError='this.onerror=null;this.src="/assets/images/avatars/avatar2.png"' style="height: 40px; width: auto;"></td> --}}
				            	<td>{{ $employee->associate_id }}</td>
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
					            	{{-- <td><img src="{{ $employee->as_pic }}" class='small-image' onError='this.onerror=null;this.src="/assets/images/avatars/avatar2.png"' style="height: 40px; width: auto;"></td> --}}
					            	<td>{{ $employee->associate_id }}</td>
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
				            	<td colspan="11" class="text-center">No Data Found!</td>
				            </tr>
			            @endif
			            </tbody>
			            <tfoot>
			            	<tr>
			            		<td colspan="8"></td>
			            		<td><b>Total Employee</b></td>
			            		<td colspan="2"><b>{{ $i }}</b></td>
			            	</tr>
			            	<tr>
			            		<td colspan="8"></td>
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
						if($format == 'as_line_id'){
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
										if($format == 'as_line_id'){
											$body = $line[$group]['hr_line_name']??'N/A';
										}elseif($format == 'as_floor_id'){
											$body = $floor[$group]['hr_floor_name']??'N/A';
										}elseif($format == 'as_department_id'){
											$body = $department[$group]['hr_department_name']??'N/A';
										}elseif($format == 'as_designation_id'){
											$body = $designation[$group]['hr_designation_name']??'N/A';
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
											$body = '';
										}
									@endphp
									{{ $body }}
								</td>
								<td>
									{{ $employee->total }}
									@php $totalEmployee += $employee->total; @endphp
								</td>
							</tr>
							@endforeach
							@else
							<tr>
				            	<td colspan="3" class="text-center">No Data Found!</td>
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


<script type="text/javascript">
    var loaderModal = '<td class="text-center" colspan="6"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:50px;"></i></td>';
    $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
    $(".overlay-modal, .item_details_dialog").removeAttr("style");
    /*Set min height to 90px after  has been set*/
    detailsheight = $(".item_details_dialog").css("min-height", "115px");
    var months    = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
    $(document).on('click','.yearly-activity',function(){
    	$("#body_result_section").html(loaderModal);
        let id = $(this).data('id');
        let associateId = $(this).data('eaid');
        let name = $(this).data('ename');
        let designation = $(this).data('edesign');
        
        $("#eName").html(name);
        $("#eId").html(associateId);
        $("#eDesgination").html(designation);
        /*Show the dialog overlay-modal*/
        $(".overlay-modal-details").show();
        $(".inner_body").show();
        // ajax call
        $.ajax({
            url: '/hr/reports/employee-yearly-activity-report-modal',
            type: "GET",
            data: {
                as_id: associateId
            },
            success: function(response){
                if(response.type === 'success'){
                	setTimeout(function(){
                		$("#body_result_section").html(response.value);
                	}, 1000);
                }else{
                	console.log(response);
                }
            }
        });
        /*Animate Dialog*/
        $(".show_item_details_modal").css("width", "225").animate({
          "opacity" : 1,
          height : detailsheight,
          width : "50%"
        }, 600, function() {
          /*When animation is done show inside content*/
          $(".fade-box").show();
        });
        // 
        
    });
    $("#confirm-disbursed").click(function() {
        let associate_id = $("#modal-associateId").val();
        let month = $("#modal-month").val();
        let year = $("#modal-year").val();
        let select_id = $("#modal-id").val();
        //alert(id);
        var _token = $('input[name="_token"]').val();

        $.ajax({
            url: '/hr/reports/employee-salary-disbursed',
            type: "post",
            data: { _token : _token,
                as_id: associate_id,
                year: year,
                month: month
            },
            success: function(response){
                console.log(response);
                if(response.status === 'success'){
                    $("#"+select_id).html( $.trim(response.value) ).effect('highlight',{},2500);
                }

            }
        });
    });

    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });
</script>