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
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">{{ date('M Y', strtotime($input['month'])) }} Salary Report </h2>
		            <h4 style="margin:4px 10px; font-weight: bold; text-align: center;">@if($input['report_format'] == 0) Details @else Summary @endif Report</h4>
		            <div class="row">
		            	<div class="col-5">
		            		<div class="row">
		                		<div class="col-2 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Format: </font></h4>
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
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Total Salary: </font></h4>
		                		</div>
		                		<div class="col-8 pl-0">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $totalSalary }}</h4>
		                		</div>
		                		
		                		<div class="col-4 pr-0">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Total Employee: </font></h4>
		                		</div>
		                		<div class="col-8 pl-0">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ count($getEmployee) }}</h4>
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
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $department[$input['department']]['hr_department_name'] }}</h4>
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
        			
        			<h2>{{ date('M Y', strtotime($input['month'])) }} Salary Summary Report </h2>
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

        			<h4>Total Employee: <b>{{ count($getEmployee) }}</b></h4>
        			<h4>Total Salary: <b>{{ $totalSalary }}</b></h4>
		            		
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
										$body = $line[$group]['hr_line_name'];
									}elseif($format == 'as_floor_id'){
										$head = 'Floor';
										$body = $floor[$group]['hr_floor_name'];
									}elseif($format == 'as_department_id'){
										$head = 'Department';
										$body = $department[$group]['hr_department_name'];
									}elseif($format == 'as_designation_id'){
										$head = 'Designation';
										$body = $designation[$group]['hr_designation_name'];
									}else{
										$head = '';
									}
								@endphp
			                	@if($head != '')
			                    <th colspan="2">{{ $head }}</th>
			                    <th colspan="8">{{ $body }}</th>
			                    @endif
			                </tr>
			                @endif
			                <tr>
			                    <th>Sl</th>
			                    <th>Photo</th>
			                    <th>Associate ID</th>
			                    <th>Name & Phone</th>
			                    <th>Designation</th>
			                    <th>Department</th>
			                    <th>Floor</th>
			                    <th>Line</th>
			                    <th>Total</th>
			                    <th>Action</th>
			                </tr>
			            </thead>
			            <tbody>
			            @php $i = 0; @endphp
			            @if(count($getEmployee) > 0)
			            @foreach($getEmployee as $employee)
			            	@php
			            		$designationName = $designation[$employee->as_designation_id]['hr_designation_name']??'';
			            	@endphp
			            	@if($head == '')
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	<td><img src="{{ $employee->as_pic }}" class='small-image' onError='this.onerror=null;this.src="{{ ($employee->as_gender == 'Female'?asset('assets/images/user/1.jpg'):asset('assets/images/user/09.jpg')) }}";' style="height: 40px; width: auto;"></td>
				            	<td>{{ $employee->associate_id }}</td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            	</td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
				            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            	<td>{{ $employee->total_payable }}</td>
				            	<td>
				            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Yearly Activity Report' ><i class="fa fa-eye"></i></button>
				            	</td>
			            	</tr>
			            	@else
			            	@if($group == $employee->$format)
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	<td><img src="{{ $employee->as_pic }}" class='small-image' onError='this.onerror=null;this.src="{{ ($employee->as_gender == 'Female'?asset('assets/images/user/1.jpg'):asset('assets/images/user/09.jpg')) }}";' style="height: 40px; width: auto;"></td>
				            	<td>{{ $employee->associate_id }}</td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            	</td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
				            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            	<td>{{ $employee->total_payable }}</td>
				            	<td>
				            		<button type="button" class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Yearly Activity Report' ><i class="fa fa-eye"></i></button>
				            	</td>
			            	</tr>
			            	@endif
			            	@endif
			            @endforeach
			            @else
				            <tr>
				            	<td colspan="10" class="text-center">No Data Found!</td>
				            </tr>
			            @endif
			            </tbody>
			            <tfoot>
			            	<tr>
			            		<td colspan="7"></td>
			            		<td><b>Total Employee</b></td>
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
											$body = $line[$group]['hr_line_name'];
										}elseif($format == 'as_floor_id'){
											$body = $floor[$group]['hr_floor_name'];
										}elseif($format == 'as_department_id'){
											$body = $department[$group]['hr_department_name'];
										}elseif($format == 'as_designation_id'){
											$body = $designation[$group]['hr_designation_name'];
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
		               	<div class="body_section" id="">
		               		<table class="table table-bordered">
		               			<thead>
		               				<tr>
		               					<th>Month</th>
		               					<th>Salary</th>
		               				</tr>
		               			</thead>
		               			<tbody id="body_result_section">
		               				<tr>
		               					<td colspan="5">
		               						<img src='{{ asset("assets/img/loader-box.gif")}}' class="center-loader">
		               					</td>
		               				</tr>
		               			</tbody>
		               		</table>
		               		
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
            url: '/hr/reports/employee-yearly-salary-modal',
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

    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal").fadeOut("slow", function() {
          /*Remove inline styles*/

          $(".overlay-modal, .item_details_dialog").removeAttr("style");
          $('body').css('overflow', 'unset');
        });
    });
</script>