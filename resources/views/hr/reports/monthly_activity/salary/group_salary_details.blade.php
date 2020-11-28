<div class="panel">
	<div class="panel-body">
		
		@php
			$urldata = http_build_query($input) . "\n";
		@endphp
		<a href='{{ url("hr/reports/monthly-salary-excel?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 16px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>
		
		<div id="report_section" class="report_section">
			<style type="text/css" media="print">
				h4, h2, p{margin: 0;}
			</style>
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
              .f-14 th, .f-14 td, .f-14 td b{
                font-size: 14px !important;
              }
              .table thead th {
			    vertical-align: inherit;
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
				$location = location_by_id();
				$formatHead = explode('_',$format);
			@endphp
			
			<div class="top_summery_section">
				@if($input['report_format'] == 0 || ($input['report_format'] == 1 && $format != null))
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Salary @if($input['report_format'] == 0) Details @else Summary @endif Report </h2>
		            <h4  style="text-align: center;">Month : {{ date('M Y', strtotime($input['month'])) }} </h4>
		            <h4  style="text-align: center;">Total Employee : {{ $totalEmployees }} </h4>
		            @if($input['pay_status'] == 'all')
		            <h4  style="text-align: center;">Total Payable : {{ bn_money(round($totalSalary,2)) }} </h4>
		            @endif
		            <table class="table no-border f-14" border="0" style="width:100%;margin-bottom:0;font-size:14px;text-align:left"  cellpadding="5">
		            	<tr>
		            		<td width="32%">
		            			@if(isset($input['unit']) && $input['unit'] != null)
		            			Unit <b>: {{ $unit[$input['unit']]['hr_unit_name'] }}</b> <br>
		            			@endif
		            			@if(isset($input['location']) && $input['location'] != null)
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
		            			@if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
	                			Total Cash
	                			<b>: {{ bn_money(round($totalCashSalary,2)) }} </b><br>
	                			@endif	
	                			
	                			@if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
	                			Total Bank
	                			<b>: {{ bn_money(round($totalBankSalary,2)) }} </b><br>
	                			Tax Amount
	                			<b>: {{ bn_money(round($totalTax,2)) }} </b>
	                			@endif
	                			
		            		</td>
		            		
		            		<td>
	                			Total OT Hour
	                			<b>: {{ numberToTimeClockFormat(round($totalOtHour,2)) }} </b><br>
	                			Total OT Amount
	                			<b>: {{ bn_money(round($totalOTAmount,2)) }} </b>
		                		
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
		                		{{-- Format 
		                			<b class="capitalize">: {{ isset($formatHead[1])?$formatHead[1]:'N/A' }}</b> <br> --}}
		                		<headtag class="capitalize">{{ $formatHead[1]??'N/A' }}</headtag>
		                			<b class="capitalize">: {{ $input['body']??'N/A' }} </b> <br>
	                			@if($input['otnonot'] != null)
		                			<b> OT </b> 
		                			<b>: @if($input['otnonot'] == 0) No @else Yes @endif </b> <br>
		                		@endif
		                		@if($input['pay_status'] != null)
		                		Payment Type 
		                			<b class="capitalize">: {{ $input['pay_status'] }}</b> <br>
		                		@endif
		            		</td>
		            	</tr>
		            	
		            </table>
		            
		        </div>
		        
		        @endif
			</div>

			<div class="content_list_section">
				
				<table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
					<thead>
						
		                <tr>
		                    <th>Sl</th>
		                    <th>Associate ID</th>
		                    <th>Name</th>
		                    <th>Designation</th>
		                    <th>Department</th>
		                    <th>Present</th>
		                    <th>Absent</th>
		                    <th>OT Hour</th>
		                    <th>Payment Method</th>
		                    <th>Payable Salary</th>
		                    @if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
		                    <th>Bank Amount</th>
		                    <th>Tax Amount</th>
		                    @endif
		                    @if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
		                    <th>Cash Amount</th>
		                    @endif
		                    <th>Stamp Amount</th>
		                    <th>Net Pay</th>
		                    <th>&nbsp;</th>
		                </tr>
		            </thead>
		            <tbody>
		            @php $i = 0; $otHourSum=0; $salarySum=0; $month = $input['month']; @endphp
		            @if(count($getEmployee) > 0)
			            @foreach($getEmployee as $employee)
			            	@php
			            		$designationName = $employee->hr_designation_name??'';
		                        $otHour = numberToTimeClockFormat($employee->ot_hour);
			            	@endphp
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	
				            	<td><a href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank">{{ $employee->associate_id }}</a></td>
				            	<td>
				            		<b>{{ $employee->as_name }}</b>
				            	</td>
				            	<td>{{ $designationName }}</td>

				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>{{ $employee->present }}</td>
				            	<td>{{ $employee->absent }}</td>
				            	<td><b>{{ $otHour }}</b></td>
				            	<td>
				            		@if($employee->pay_status == 1)
				            			Cash
				            		@elseif($employee->pay_status == 2)
				            		<b>{{ $employee->bank_name }}</b>
				            		<b>{{ $employee->bank_no }}</b>
				            		@else
				            		Bank & Cash
				            		<b>{{ $employee->bank_no }}</b>
				            		@endif
				            	</td>
				            	<td>
				            		@php $totalPay = $employee->total_payable + $employee->stamp; @endphp
				            		{{ bn_money($totalPay) }}
				            	</td>	
				            	@if($input['pay_status'] == 'all' || ($input['pay_status'] != 'cash' && $input['pay_status'] != null))
				            	<td>{{ bn_money($employee->bank_payable) }}</td>
				            	<td>{{ bn_money($employee->tds) }}</td>
				            	@endif
				            	@if($input['pay_status'] == 'all' || $input['pay_status'] == 'cash')
				            	<td>{{ bn_money($employee->cash_payable + $employee->stamp) }}</td>
				            	@endif
				            	<td>{{ bn_money($employee->stamp) }}</td>
				            	
				            	<td>
				            		@php
				            			if($input['pay_status'] == 'cash'){
				            				$totalNet = $employee->cash_payable;
				            			}else{
				            				$totalNet = $employee->total_payable - $employee->tds;
				            			}
				            		@endphp
				            		{{ bn_money($totalNet) }}
				            	</td>
				            	<td>
				            		<button type="button" class="btn btn-primary btn-sm yearly-activity-single" data-ids="{{ $employee->as_id}}" data-eaids="{{ $employee->associate_id }}" data-enames="{{ $employee->as_name }}" data-edesigns="{{ $designationName }}" data-yearmonths="{{ $input['month'] }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Employee Salary Report' ><i class="fa fa-eye"></i></button>
				            	</td>
			            	</tr>
			            	
			            @endforeach
		            @else
			            
		            @endif
		            </tbody>
		            
				</table>
					
			</div>
		</div>

		{{-- modal employee salary --}}
		<div class="item_details_section">
		    <div class="overlay-modal overlay-modal-details" style="margin-left: 0px; display: none;">
		      <div class="item_details_dialog show_item_details_modal_group" style="min-height: 115px;">
		        <div class="fade-box-details fade-box">
		          <div class="inner_gray clearfix">
		            <div class="inner_gray_text text-center" id="heading">
		             <h5 class="no_margin text-white">{{ date('M Y', strtotime($input['month'])) }} Salary</h5>   
		            </div>
		            <div class="inner_gray_close_button">
		              <a class="cancel_details item_modal_close" role="button" rel='tooltip' data-tooltip-location='left' data-tooltip="Close Modal">Close</a>
		            </div>
		          </div>

		          <div class="inner_body" id="modal-details-content" style="display: none">
		            <div class="inner_body_content">
		               	<div class="body_top_section">
		               		<h3 class="text-center modal-h3"><strong>Name :</strong> <b id="eNamesingle"></b></h3>
		               		<h3 class="text-center modal-h3"><strong>Id :</strong> <b id="eIdsingle"></b></h3>
		               		<h3 class="text-center modal-h3"><strong>Designation :</strong> <b id="eDesginationsingle"></b></h3>
		               	</div>
		               	<div class="body_content_section">
			               	<div class="body_section" id="employee-salary-single">
			               		
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
		{{--  --}}
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
    $(document).on('click','.yearly-activity-single',function(){
    	console.log('e')
    	$("#employee-salary-single").html(loaderModal);
        let id = $(this).data('ids');
        let associateId = $(this).data('eaids');
        let name = $(this).data('enames');
        let designation = $(this).data('edesigns');
        let yearMonth = $(this).data('yearmonths');
        $("#eNamesingle").html(name);
        $("#eIdsingle").html(associateId);
        $("#eDesginationsingle").html(designation);
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
                		$("#employee-salary-single").html(response);
                	}, 1000);
                }else{
                	console.log(response);
                }
            }
        });
        /*Animate Dialog*/
        $(".show_item_details_modal_group").css("width", "225").animate({
          "opacity" : 1,
          height : detailsheight,
          width : "100%"
        }, 600, function() {
          /*When animation is done show inside content*/
          $(".fade-box").show();
        });
        // 
        
    });

    $(".cancel_details").click(function() {
        $(".overlay-modal-details, .show_item_details_modal_group").fadeOut("slow", function() {
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