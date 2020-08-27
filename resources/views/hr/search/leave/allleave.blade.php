<style type="text/css">
	.infobox-data-number{
	    display: block;
	    font-size: 22px;
	    margin: 2px 0 4px;
	    position: relative;
	    text-shadow: 1px 1px 0 rgba(0,0,0,.15);
	}
	h5 {
		font-weight: bold !important;
	}
	@media print {
        #printOutputSection {display: block;}
    }
</style>
<div class="panel panel-info col-sm-12 col-xs-12">

	<div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                 MBM Group 
            </li>
        </ul>
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv({{json_encode($leave_type_list_count)}},"{{$showTitle}}")'>Print</a>
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
	        
				<div class="col-sm-offset-3 col-xs-4 col-sm-3 pricing-box">
					<div class="widget-box widget-color-dark search_unit">
						<div class="widget-header">
							<h5 class="widget-title bigger lighter">Total Unit</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center">
								<span class="infobox-data-number">{{ count($unit_list) }}</span>
							</div>
						</div>
					</div>
				</div>

				

				<div class="col-xs-4 col-sm-3 pricing-box">
					<div class="widget-box widget-color-red">
						<div class="widget-header">
							<h5 class="widget-title bigger lighter">Total Leave</h5>
						</div>

						<div class="widget-body">
							<div class="widget-main center <?php echo $request['type']=='year'?'after-load':''?>">
								<span class="infobox-data-number" id="tLeave">
								@php
									$totalLeave = 0;
									foreach($leave_type_list_count as $type=>$leave_type){
										$totalLeave += $leave_type;
									}
									echo $totalLeave;
								@endphp
								</span>
							</div>
						</div>
					</div>
				</div>
			
			@if($request['type'] == 'month' || $request['type'] == 'date')
				@if(count($leave_type_list_count) > 0)
					<br>
					<p class="alert alert-info">Leave Type</p>
				@endif
				<div class="row">
				@php
					$lCount = 0;
				@endphp
				@foreach($leave_type_list_count as $type=>$leave_type)
				@php
					$lCount++;
				@endphp
					<div class="col-xs-4 col-sm-3 pricing-box search_emp" data-leavetype="{{ $type }}">
						<div class="widget-box widget-color-{{ $lCount % 2 == 0? 'red':'orange' }}">
							<div class="widget-header">
								<h5 class="widget-title bigger lighter">Total {{ $type }} Leave</h5>
							</div>

							<div class="widget-body">
								<div class="widget-main center">
									<span class="infobox-data-number" id="t{{ $type }}">{{ $leave_type }}</span>
								</div>
							</div>
						</div>
					</div>
				@endforeach
				</div>
			@endif
        </div>
		<div class="row">
			@if($request['type'] == 'year')
				@php
					$lCount = 0;
				@endphp
				@foreach($leave_type_list as $type=>$leave_type)
					@php
						$totalEachMonthLeaveCount = 0;
					@endphp
					@foreach($leave_type as $monthName=>$leave_type1)
					@php
						$lCount++;
					@endphp
						<div class="col-xs-4 col-sm-3 pricing-box" data-attstatus="{{ strtolower($monthName) }}">
							<div class="widget-box widget-color-{{ $lCount % 2 == 0? 'red':'orange' }}">
								<div class="widget-header">
									<h5 class="widget-title bigger lighter">Total Leave in <span style="font-size: 15px; color: #000; font-weight: bold">{{ $monthName }}</span> </h5>
								</div>

								<div class="widget-body">
									<div class="widget-main center">
										<span class="infobox-data-number" id="t{{ $monthName }}">
											@if(count($leave_type1)>0)
												@php
													$totalEachMonthLeaveCount1 = 0;
												@endphp
												@foreach($leave_type1 as $type1=>$leave_type2)
													@php
														$totalEachMonthLeaveCount += $leave_type2;
														$totalEachMonthLeaveCount1 += $leave_type2;
													@endphp
													<a href="#">
			                                            <div class="profile-info-row">
			                                                <div class="profile-info-name"> Total {{ $type1 }} </div>
			                                                <div class="profile-info-value">
			                                                    <span>{{ $leave_type2 }}</span>
			                                                </div>
			                                            </div>
			                                        </a>
												@endforeach
													<a href="#">
			                                            <div class="profile-info-row">
			                                                <div class="profile-info-name"> Total Leave </div>
			                                                <div class="profile-info-value">
			                                                    <span>{{ $totalEachMonthLeaveCount1 }}</span>
			                                                </div>
			                                            </div>
			                                        </a>
											@else
												<a href="#">
		                                            <div class="profile-info-row">
		                                                <div class="profile-info-name"> Total Leave </div>
		                                                <div class="profile-info-value">
		                                                    <span>0</span>
		                                                </div>
		                                            </div>
		                                        </a>
											@endif
										</span>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				@endforeach
				<input type="hidden" id="totalYearlyLeave" value="{{ $totalEachMonthLeaveCount }}">
			@endif
		</div>
    </div>
</div>
<div id="printOutputSection" style="display: none;"></div>
@php
    $rangeFrom = date('Y-m-d');
    $rangeTo = date('Y-m-d');
    if($request['type'] == 'date'){
        $rangeFrom = $request['date'];
    	$rangeTo = $request['date'];
    }
@endphp
<script src="http://ace.jeka.by/assets/js/bootbox.js"></script>
<script>
	$(document).ready(function(){ 
        $('.after-load span').html('<i class="ace-icon fa fa-spinner fa-spin orange bigger-30"></i>');
    });
    @if($request['type'] == 'year')
	    setTimeout(function() {
	    	$('#tLeave').html($('#totalYearlyLeave').val());
	    }, 2000);
    @endif
    function printDiv(result,pagetitle) {
		$.ajax({
			url: '{{url('hr/search/hr_leave_searchPrint')}}',
			type: 'get',
			data: {
				data: result,
				title: pagetitle
			},
			success: function(data) {
				$('#printOutputSection').html(data);
				var divToPrint=document.getElementById('printOutputSection');
				var newWin=window.open('','Print-Window');
				newWin.document.open();
				newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
				newWin.document.close();
				setTimeout(function(){newWin.close();},10);
			}
		});
	}
</script>