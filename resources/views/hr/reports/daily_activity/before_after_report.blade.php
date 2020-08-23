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
				$area = area_by_id();
				$formatHead = explode('_',$format);
			@endphp
			
			<div class="top_summery_section">
				<div class="page-header" style="text-align:left;border-bottom:2px double #666">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Before Absent After Present </h2>
		            <h4 style="margin:4px 10px; font-weight: bold; text-align: center;">@if($input['report_type'] == 0) Details @else Summary @endif Report</h4>
		            <div class="row">
		            	<div class="col-sm-5">
		            		<div class="row">
		                		<div class="col-sm-2 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Format: </font></h4>
		                		</div>
		                		<div class="col-sm-10 no-padding-left">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0; text-transform: capitalize;">&nbsp;&nbsp;{{ isset($formatHead[1])?$formatHead[1].' Wise':'N/A' }}</h4>
		                		</div>
		                		
		                		<div class="col-sm-2 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Unit: </font></h4>
		                		</div>
		                		<div class="col-sm-10 no-padding-left">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $unit[$input['unit']]['hr_unit_name'] }}</h4>
		                		</div>
		                		
		                		<div class="col-sm-2 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Area: </font></h4>
		                		</div>
		                		<div class="col-sm-10 no-padding-left">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $area[$input['area']]['hr_area_name'] }}</h4>
		                		</div>
		                		@if($input['department'] != null)
		                		<div class="col-sm-2 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Department: </font></h4>
		                		</div>
		                		<div class="col-sm-10 no-padding-left">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $department[$input['department']]['hr_department_name'] }}</h4>
		                		</div>
		                		@endif
		            		</div>
		            	</div>
		            	<div class="col-sm-4 no-padding">
		            		<div class="row">
		                		<div class="col-sm-3 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Absent Date: </font></h4>
		                		</div>
		                		<div class="col-sm-9">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $input['absent_date'] }}</h4>
		                		</div>
		                		<div class="col-sm-3 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Present Date: </font></h4>
		                		</div>
		                		<div class="col-sm-9">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $input['present_date'] }}</h4>
		                		</div>
		                		@if($input['report_type'] == 0 || ($input['report_type'] == 1 && $format == null))
		                		<div class="col-sm-3 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Total Employee: </font></h4>
		                		</div>
		                		<div class="col-sm-9">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ count($getEmployee) }}</h4>
		                		</div>
		                		@endif
		                	</div>
		            	</div>
		            	<div class="col-sm-3 no-padding">
		            		<div class="row">
		                		@if($input['section'] != null)
		                		<div class="col-sm-3 no-padding-right">
		                			<h4 style="margin:4px 5px; text-align:right; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Section: </font></h4>
		                		</div>
		                		<div class="col-sm-9">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $section[$input['section']]['hr_section_name'] }}</h4>
		                		</div>
		                		@endif
		                		@if($input['subSection'] != null)
		                		<div class="col-sm-3 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Sub Section: </font></h4>
		                		</div>
		                		<div class="col-sm-9">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $department[$input['department']]['hr_department_name'] }}</h4>
		                		</div>
		                		@endif
		                		@if($input['floor_id'] != null)
		                		<div class="col-sm-3 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Floor: </font></h4>
		                		</div>
		                		<div class="col-sm-9">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $floor[$input['floor_id']]['hr_floor_name'] }}</h4>
		                		</div>
		                		@endif
		                		@if($input['line_id'] != null)
		                		<div class="col-sm-3 no-padding-right">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0"><font style="font-weight: bold; font-size: 12px;">Line: </font></h4>
		                		</div>
		                		<div class="col-sm-9">
		                			<h4 style="margin:4px 5px; margin: 0; padding: 0">&nbsp;&nbsp;{{ $line[$input['line_id']]['hr_line_name'] }}</h4>
		                		</div>
		                		@endif
		                	</div>
		            	</div>
		            </div>
		        </div>
			</div>
			<div class="content_list_section">
				@if($input['report_type'] == 0 || ($input['report_type'] == 1 && $format == null))
					@foreach($uniqueGroups as $group)
					
					<table class="table table-bordered table-hover">
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
			                    <th colspan="7">{{ $body }}</th>
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
				            	<td><img src="{{ $employee->as_pic }}" class='small-image' onError='this.onerror=null;this.src="/assets/images/avatars/avatar2.png"' style="height: 40px; width: auto;"></td>
				            	<td>{{ $employee->associate_id }}</td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            		<p>{{ $employee->as_contact }}</p>
				            	</td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
				            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            	<td>
				            		<a class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" rel='tooltip' data-tooltip-location='top' data-tooltip='Yearly Activity Report' ><i class="fa fa-eye"></i></a>
				            	</td>
			            	</tr>
			            	@else
			            	@if($group == $employee->$format)
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	<td><img src="{{ $employee->as_pic }}" class='small-image' onError='this.onerror=null;this.src="/assets/images/avatars/avatar2.png"' style="height: 40px; width: auto;"></td>
				            	<td>{{ $employee->associate_id }}</td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            		<p>{{ $employee->as_contact }}</p>
				            	</td>
				            	<td>{{ $designation[$employee->as_designation_id]['hr_designation_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $floor[$employee->as_floor_id]['hr_floor_name']??'' }}</td>
				            	<td>{{ $line[$employee->as_line_id]['hr_line_name']??'' }}</td>
				            	<td>
				            		<a class="btn btn-primary btn-sm yearly-activity" data-id="{{ $employee->as_id}}" data-eaid="{{ $employee->associate_id }}" data-ename="{{ $employee->as_name }}" data-edesign="{{ $designationName }}" rel='tooltip' data-tooltip-location='top' data-tooltip='Yearly Activity Report' ><i class="fa fa-eye"></i></a>
				            	</td>
			            	</tr>
			            	@endif
			            	@endif
			            @endforeach
			            @else
				            <tr>
				            	<td colspan="9" class="text-center">No Data Found!</td>
				            </tr>
			            @endif
			            </tbody>
			            <tfoot>
			            	<tr>
			            		<td colspan="7"></td>
			            		<td><b>Total Employee</b></td>
			            		<td><b>{{ $i }}</b></td>
			            	</tr>
			            </tfoot>
					</table>
					@endforeach
				@else
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
					<table class="table table-bordered table-hover">
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
						<tfoot>
							<tr>
								<td></td>
								<td style="text-align: right"><b>Total Employee</b></td>
								<td><b>{{ $totalEmployee }}</b></td>
							</tr>
						</tfoot>
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
		             <h3 class="no_margin">Employee Yearly Activity Report - {{ date('Y')}}</h3>   
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
		               					<th>Absent</th>
		               					<th>Late</th>
		               					<th>Leave</th>
		               					<th>Holiday</th>
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
    
    $(".overlay-modal, .item_details_dialog").css("opacity", 0);
    /*Remove inline styles*/
    $(".overlay-modal, .item_details_dialog").removeAttr("style");
    /*Set min height to 90px after  has been set*/
    let detailsheight = $(".item_details_dialog").css("min-height", "115px");
    var months    = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
    $(document).on('click','.yearly-activity',function(){
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