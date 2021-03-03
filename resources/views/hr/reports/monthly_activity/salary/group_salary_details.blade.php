{{-- 
	!-------------caution-----------
 --}}

<div class="panel">
	<div class="panel-body">
		
		@php
			$urldata = http_build_query($input) . "\n";
		@endphp
		@if(auth()->user()->hasRole('Buyer Mode'))
		<a href='{{ url("hrm/reports/monthly-salary-excel?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 16px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>
		@else
		<a href='{{ url("hr/reports/monthly-salary-excel?$urldata")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 16px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>
		@endif
		
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
			.associate-right{
				cursor: pointer;
				position: relative;
			}
			.associate-right a{
				cursor: pointer;
				color:#089bab;
			}

			/*right click*/
			  .context-menu ul{ 
			    z-index: 1000;
			    position: absolute;
			    overflow: hidden;
			    border: 1px solid #CCC;
			    white-space: nowrap;
			    font-family: sans-serif;
			    background: #FFF;
			    color: #333;
			    border-radius: 5px;
			    padding: 0;
			    box-shadow: 2px 4px 4px 0px;
			}
			.context-menu:before {
			  content: "";
			  position: absolute;
			  border-color: rgba(194, 225, 245, 0);
			  border: solid transparent;
			  border-bottom-color: white;
			  border-width: 11px;
			  margin-left: -10px;
			  top: -17px;
    		  right: -21px;
			  z-index: 1;
			} 
			.popover{
				position: absolute;
			    will-change: transform;
			    width: 250px;
			}
			.popover-left{
				transform: translate3d(80px, -20px, 0px);
			    top: 0px;
			    left: 0;
			}
			.popover-right{
				transform: translate3d(-260px, -20px, 0px);
			    top: 0px;
			    right: 0;
			}
			.context-menu:after {
			    content: "";
			    position: absolute;
			    right: -20px;
    			top: -17px;
			    width: 0;
			    height: 0;
			    border: solid transparent;
			    border-width: 10px;
			    border-bottom-color: #2B1A41;
			    z-index: 0;
			}
			/* Each of the items in the list */
			.context-menu ul li {
			    padding: 8px 12px;
			    cursor: pointer;
			    list-style-type: none;
			}
			.context-menu ul li:hover {
			    background-color: #DEF;
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
		                		@if(isset($input['audit']) && $input['audit'] == 'Audit')
		                		Audited 
		                			<b class="capitalize">: <count id="auditCount">{{count($auditedEmployee)}}</count> / {{ $totalEmployees }}</b> <br>
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
		                    @if(isset($input['audit']) && $input['audit'] == 'Audit')
		                    <th>&nbsp;</th>
		                    @endif
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
			            	<tr id="row-{{ $employee->as_id}}" class="@if(count($auditedEmployee) > 0 && isset($auditedEmployee[$employee->as_id])) {{ $auditedEmployee[$employee->as_id]['status'] == 1?'table-success':'table-danger'}}  @endif">
			            		<td>{{ ++$i }}</td>
				            	
				            	<td class="associate-right">
				            		<a class="" data-toggle="tooltip" data-placement="top" title="" data-original-title='Right Click Action'>{{ $employee->associate_id }}</a>
				            		@if(auth()->user()->hasRole('Buyer Mode'))
				            		@else
				            		<div class="context-menu" id="context-menu-file-{{$employee->associate_id}}" style="display:none;position:absolute;z-index:1;">
									    <ul>
									      <li>
									      	{{-- <a class="textblack" href='{{ url("hr/operation/job_card?associate=$employee->associate_id&month_year=$month") }}' target="_blank" ><i class="lar la-id-card"></i> Job Card</a> --}}
									      	<a class="textblack job_card" data-name="{{ $employee->as_name }}" data-associate="{{ $employee->associate_id }}" data-month-year="{{ $month }}"><i class="lar la-id-card"></i> Job Card</a>
									      	
									      </li>           
									      <li><a class="yearly-activity-single" data-ids="{{ $employee->as_id}}" data-eaids="{{ $employee->associate_id }}" data-enames="{{ $employee->as_name }}" data-edesigns="{{ $designationName }}" data-yearmonths="{{ $input['month'] }}" ><i class="las la-money-check-alt"></i> Salary Sheet</a></li>           
									      {{-- <li><a onclick="copyDocuments('{{$employee->associate_id}}')"><i class="las la-copy"></i> Copy</a></li>  --}}
									    </ul>
									</div>
									@endif

									@if(isset($input['audit']) && $input['audit'] == 'Audit')
									<div class="popover bs-popover-right auditpopover popover-left" role="tooltip" id="popover-{{$employee->as_id}}" x-placement="right" style="display:none;position:absolute;z-index:1;">
										<div class="arrow" style="top: 37px;"></div>
										@if(count($auditedEmployee) > 0 && isset($auditedEmployee[$employee->as_id]))
											<h3 class="popover-header">Audited By - {{ $auditedEmployee[$employee->as_id]['user']->name??'' }}</h3>
											
											<div class="popover-body">
												@if($auditedEmployee[$employee->as_id]['status'] == 2){{ $auditedEmployee[$employee->as_id]['comment']??'No Comment' }}
												@else
												Audited Pass
												@endif
											</div>
										@else
										<h3 class="popover-header"> &nbsp; </h3>
										<div class="popover-body">
											Not Audited!
										</div>
										@endif
									</div>
									@endif
				            	</td>
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
				            	@if(isset($input['audit']) && $input['audit'] == 'Audit')
				            	<td>
				            		<div class="action-section" style="position: relative">
				            			{{-- <button type="button" class="btn btn-primary btn-sm yearly-activity-single" data-ids="{{ $employee->as_id}}" data-eaids="{{ $employee->associate_id }}" data-enames="{{ $employee->as_name }}" data-edesigns="{{ $designationName }}" data-yearmonths="{{ $input['month'] }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='Employee Salary Report' ><i class="fa fa-eye"></i></button> --}}
					            		<button type="button" class="btn btn-primary btn-sm audit-pass" data-status="1" data-ids="{{ $employee->as_id}}" data-eaids="{{ $employee->associate_id }}" data-enames="{{ $employee->as_name }}" data-edesigns="{{ $designationName }}" data-yearmonths="{{ $input['month'] }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='' style="font-size: 5px;"><i class="las la-check"></i></button>
					            		<button type="button" class="btn btn-danger btn-sm audit-fail-btn" data-status="2" data-ids="{{ $employee->as_id}}" data-eaids="{{ $employee->associate_id }}" data-enames="{{ $employee->as_name }}" data-edesigns="{{ $designationName }}" data-yearmonths="{{ $input['month'] }}" data-toggle="tooltip" data-placement="top" title="" data-original-title='' style="font-size: 5px;"><i class="las la-times"></i></button>
					            		<div class="popover bs-popover-left cancleAuditPopover popover-right" role="tooltip" id="popover-{{$employee->as_id}}" x-placement="left" style="display:none;position:absolute;z-index:1;">
											<div class="arrow" style="top: 37px;"></div>
											<h3 class="popover-header"> {{ $employee->as_name }} </h3>
											<div class="popover-body">
												<textarea id="cancle-{{ $employee->as_id}}" class="form-control cancle-textarea" ></textarea>
											</div>
											<div class="popover-footer">
												<button type="button" class="btn btn-success btn-sm audit-fail" data-status="2" data-ids="{{ $employee->as_id}}" data-eaids="{{ $employee->associate_id }}" data-enames="{{ $employee->as_name }}" data-edesigns="{{ $designationName }}" data-yearmonths="{{ $input['month'] }}" style="margin-left: 20px; margin-bottom: 4px; font-size: 12px;">Save</button>
											</div>
										</div>
				            		</div>
				            	</td>
				            	@endif
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
    	// console.log('e')
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
    function copyDocuments(text) {
		var copyText = document.getElementById("emp-"+text);
	  	// copyText.select();
	  	document.execCommand("copy");
	  	alert("Copied the text: " + copyText.innerHTML);
    }
    $(function () {
	  $('[data-toggle="tooltip"]').tooltip()
	})
    
</script>