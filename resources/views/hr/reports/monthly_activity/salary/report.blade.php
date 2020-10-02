<div class="panel">
	<div class="panel-body">
		<button class="btn btn-sm btn-primary hidden-print" onclick="printDiv('report_section')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
		<div id="report_section" class="report_section">
			<style type="text/css">
              .table{
                width: 100%;
              }
              a{text-decoration: none;}
              .table-bordered {
                  border-collapse: collapse;
              }
              .table-bordered th,
              .table-bordered td {
                border: 1px solid #777 !important;
                padding:5px;
              }
              .no-border td, .no-border th{
                border:0 !important;
                vertical-align: top;
              }
              .f-16 th, .f-16 td, .f-16 td b{
                font-size: 16px !important;
              }
                      </style>
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
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Salary @if($input['report_format'] == 0) Details @else Summary @endif Report </h2>
		            <table class="table no-border f-16" border="0">
		            	<tr>
		            		<td>
		            			Unit <b>: {{ $unit[$input['unit']]['hr_unit_name'] }}</b> <br>
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
		            			Month <b>: {{ date('M Y', strtotime($input['month'])) }} </b> <br>
		            			@if($input['otnonot'] != null)
		                			<b> OT </b> 
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
		                			Total Employee
		                			<b>: {{ $totalEmployees }}</b> <br>
		                			Total Salary
		                			<b>: {{ bn_money($totalSalary) }} (BDT)</b>
		                		
		                		
		                	</div>
		                		
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
        			
        			<h2>{{ date('M Y', strtotime($input['month'])) }} Salary Summary Report </h2>
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
        			<h4>Total Employee: <b>{{ $totalEmployees }}</b></h4>
        			<h4>Total Salary: <b>{{ bn_money($totalSalary) }}</b></h4>
		            		
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
			                    <th>Sl</th>
			                    <th>Photo</th>
			                    <th>Associate ID</th>
			                    <th>Name</th>
			                    <th>Designation</th>
			                    <th>Department</th>
			                    <th>Floor</th>
			                    <th>Line</th>
			                    <th>Present</th>
			                    <th>Absent</th>
			                    <th>OT Hour</th>
			                    <th>Total</th>
			                    <th>Action</th>
			                </tr>
			            </thead>
			            <tbody>
			            @php $i = 0; $month = $input['month']; @endphp
			            @if(count($getEmployee) > 0)
			            @foreach($getEmployee as $employee)
			            	@php
			            		$designationName = $employee->hr_designation_name??'';
			            		
		                        $otHour = numberToTimeClockFormat($employee->ot_hour);
			            	@endphp
			            	@if($head == '')
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	<td><img height="30" src="{{ emp_profile_picture($employee) }}" class='small-image min-img-file'></td>
				            	<td><a href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank">{{ $employee->associate_id }}</a></td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            	</td>
				            	<td>{{ $designationName }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
				            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            	<td>{{ $employee->present }}</td>
				            	<td>{{ $employee->absent }}</td>
				            	<td><b>{{ $otHour }}</b></td>
				            	<td>{{ bn_money($employee->total_payable) }}</td>
				            	<td>
				            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-yearmonth="{{ $input['month'] }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Yearly Activity Report' ><i class="fa fa-eye"></i></button>
				            	</td>
			            	</tr>
			            	@else
			            	@if($group == $employee->$format)
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	<td><img height="30"  src="{{ emp_profile_picture($employee) }}" class='small-image min-img-file'></td>
				            	<td><a href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank">{{ $employee->associate_id }}</a></td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            	</td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
				            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            	<td>{{ $employee->present }}</td>
				            	<td>{{ $employee->absent }}</td>
				            	<td><b>{{ $otHour }}</b></td>
				            	<td>{{ bn_money($employee->total_payable) }}</td>
				            	<td>
				            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-yearmonth="{{ $input['month'] }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Yearly Activity Report' ><i class="fa fa-eye"></i></button>
				            	</td>
			            	</tr>
			            	@endif
			            	@endif
			            @endforeach
			            @else
				            <tr>
				            	<td colspan="13" class="text-center">No Data Found!</td>
				            </tr>
			            @endif
			            </tbody>
			            <tfoot>
			            	<tr>
			            		<td colspan="11" class="text-right"><b>Total Employee</b></td>
			            		
			            		<td colspan="2"><b>{{ $i }}</b></td>
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
						}else{
							$head = '';
						}
					@endphp
					<table class="table table-bordered table-hover table-head">
						<thead>
							<tr>
								<th>Sl</th>
								<th> {{ $head }} Name</th>
								<th>No. Of Employee</th>
								<th>Salary</th>
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
								<td>
									{{ bn_money($employee->groupSalary) }} (BDT)
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
		<div class="item_details_section">
		    <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
		      <div class="item_details_dialog show_item_details_modal" style="min-height: 115px;">
		        <div class="fade-box-details fade-box">
		          <div class="inner_gray clearfix">
		            <div class="inner_gray_text text-center" id="heading">
		             <h5 class="no_margin text-white">Employee Yearly Activity Report - {{ date('Y')}}</h5>   
		            </div>
		            <div class="inner_gray_close_button">
		              <a class="cancel_details item_modal_close" role="button" rel='tooltip' data-tooltip-location='left' data-tooltip="Close Modal">Close</a>
		            </div>
		          </div>

		          <div class="inner_body" id="modal-details-content" style="display: none">
		            <div class="inner_body_content">
		               	<div class="body_top_section">
		               		<h3 class="text-center modal-h3"><strong>Name :</strong> <b id="eName"></b></h3>
		               		<h3 class="text-center modal-h3"><strong>Id :</strong> <b id="eId"></b></h3>
		               		<h3 class="text-center modal-h3"><strong>Designation :</strong> <b id="eDesgination"></b></h3>
		               	</div>
		               	<div class="body_content_section">
			               	<div class="body_section" id="employee-salary">
			               		
			               	</div>
		               	</div>
		            </div>
		            <div class="inner_buttons">
		              <a class="cancel_modal_button cancel_details" role="button"> Close </a>
		            </div>
		          </div>
		        </div>
		      </div>
		    </div>
		</div>
	</div>
</div>


<script type="text/javascript">
    var loaderModal = '<div class="panel"><div class="panel-body"><p style="text-align:center;margin:10px;"><i class="ace-icon fa fa-spinner fa-spin orange bigger-30" style="font-size:60px;"></i></p></div></div>';
    $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
    $(".overlay-modal, .item_details_dialog").removeAttr("style");
    /*Set min height to 90px after  has been set*/
    detailsheight = $(".item_details_dialog").css("min-height", "115px");
    var months    = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
    $(document).on('click','.yearly-activity',function(){
    	$("#employee-salary").html(loaderModal);
        let id = $(this).data('id');
        let associateId = $(this).data('eaid');
        let name = $(this).data('ename');
        let designation = $(this).data('edesign');
        let yearMonth = $(this).data('yearmonth');
        $("#eName").html(name);
        $("#eId").html(associateId);
        $("#eDesgination").html(designation);
        /*Show the dialog overlay-modal*/
        $(".overlay-modal-details").show();
        $(".inner_body").show();
        // ajax call
        $.ajax({
            url: '/hr/reports/employee-salary-modal',
            type: "GET",
            data: {
                as_id: associateId,
                year_month: yearMonth
            },
            success: function(response){
            	// console.log(response);
                if(response !== 'error'){
                	setTimeout(function(){
                		$("#employee-salary").html(response);
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
          width : "70%"
        }, 600, function() {
          /*When animation is done show inside content*/
          $(".fade-box").show();
        });
        // 
        
    });

    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });

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