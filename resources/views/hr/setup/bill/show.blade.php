<div class="panel">
	<div class="panel-body">
		
		<div class="content_list_section"  id="report_section">
			
			<div class="page-header report_section">
				
				<h3 style="font-weight: bold; text-align: center;"> {{ $unit[$bill->unit_id]['hr_unit_name']??'' }} </h3> 
				<h4 style=" text-align: center;">{{ $bill->bill_type['name']??'' }}  Bill - {{ $bill->amount }} Tk.</h4>
				<h4 style=" text-align: center;">
					@if($bill->pay_type == 1)
	                    All Present
	                @elseif($bill->pay_type == 2)
	                    Working Hour
                    @elseif($bill->pay_type == 3)
                        OT Hour
                    @elseif($bill->pay_type == 4)
                        Out Punch
                    @endif
                    {{ ($bill->pay_type == 1)?'':'- '. $bill->duration }}
				</h4>
				
	            
	        </div>
			
			<div class="row">
				<div class="offset-sm-3 col-sm-6">
					<table class="table table-bordered table-hover table-head" border="1" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" cellpadding="5">
				
						<thead>
							<tr class="text-center">
								<th>Parameter</th>
								<th>Pay Type</th>
								<th>Duration</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody>
							@if(count($billGroup) > 0)
							@foreach($billGroup as $key => $special)

								<tr>
									<th colspan="4" class="capitalize">
										@php $typeEx = explode('_', $key); @endphp
										{{ $typeEx[1]??$typeEx[0] }}
									</th>
								</tr>
								@foreach($special as $value)
									<tr>
										<td>
											@if($value->adv_type != 'outtime')
												@php
													$para = $value->adv_type;
													$group = $value->parameter;
													if($para == 'as_department_id'){
														$body = $department[$group]['hr_department_name']??'';
													}elseif($para == 'as_location'){
														$body = $location[$group]['hr_location_name']??'';
													}elseif($para == 'as_designation_id'){
														$body = $designation[$group]['hr_designation_name']??'';
													}elseif($para == 'as_section_id'){
														$body = $section[$group]['hr_section_name']??'';
													}elseif($para == 'as_subsection_id'){
														$body = $subSection[$group]['hr_subsec_name']??'';
													}else{
														$body = '';
													}
												@endphp
												{{ $body }}
											@else
												{{ $value->parameter }}
											@endif
										</td>
										
										<td>
											@if($value->pay_type == 1)
		                                        All Present
		                                    @elseif($value->pay_type == 2)
		                                        Working Hour
		                                    @elseif($value->pay_type == 3)
		                                        OT Hour
		                                    @elseif($value->pay_type == 4)
		                                        Out Punch
		                                    @else
		                                    	-
		                                    @endif
										</td>
										<td>
											@if($value->adv_type == 'outtime')
												-
											@else
												{{ ($value->pay_type == 1)?'':$value->duration }}
											@endif
											
										</td>
										<td class="text-right">{{ $value->amount }}</td>
									</tr>
								@endforeach
							@endforeach
							@else
							<tr>
				            	<td colspan="8" class="text-center">No Data Found!</td>
				            </tr>
							@endif
						</tbody>
					</table>
				</div>
			</div>	
			
		</div>
	</div>
</div>
