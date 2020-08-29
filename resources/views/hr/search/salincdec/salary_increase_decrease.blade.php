<div class="panel panel-info col-sm-12 col-xs-12">
	<div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                 MBM Group
            </li>
        </ul>
        {{-- <a href="#" id="printButton" class="btn btn-xs btn-info pull-right">Print</a> --}}
    </div>
    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
        
        	<div class="col-xs-7  col-sm-7 pricing-box">
				
				@if(!empty(Request::get('month')))
				<div class="row">
	                <div class="widget-container-col col-sm-12" id="widget-container-col-4">
	                    <div class="widget-box no-padding no-margin" id="widget-box-4">
	                        <div class="widget-header widget-header-small">
		                        <h5 class="widget-title">Employee Status Summary</h5>
		                    </div>

	                        <div class="widget-body">
		                        <table class="table table-bordered  table-hover">
									<thead>
										<tr>
											<th style="background-color: lightgrey;">Status</th>
											<?php  
	           	        						$monthNum  = (int)$salInfo_previous->previous_month;
												$dateObj   = DateTime::createFromFormat('!m', $monthNum);
												$monthName = $dateObj->format('F');
											?>
		           	        				<th style="text-align: left; background-color: lightgrey;">{{$monthName}}-{{$salInfo_previous->previous_Y}}</th>
											<th style="background-color: lightgrey;">{{ Request::get('month') }}</th>
										</tr>
									</thead>
									<tbody>
											{{-- prevoius_out_emp current_out_emp new_employee --}}
										<tr>
											<th style="color: darkgreen;">Active</th>
											<th style="color: darkgreen;">
												{{
													$prevoius_out_emp['active']
													+$prevoius_out_emp['maternity']
													+$new_employee['previous']
												}}
											</th>
											<th style="color: darkgreen;">
												{{
													$current_out_emp['active']
													+$current_out_emp['maternity']
													+$new_employee['current']
												}}
											</th>
										</tr>
										<tr>
											<th style="color: mediumblue;">Maternity</th>
											<th style="color: mediumblue;">{{$prevoius_out_emp['maternity'] }}</th>
											<th style="color: mediumblue;">{{$current_out_emp['maternity']}}</th>
										</tr>
										<tr>
											<th style="color: darkblue;">New Employee</th>
											<th style="color: darkblue;">{{$new_employee['previous'] }}</th>
											<th style="color: darkblue;">{{$new_employee['current']}}</th>
										</tr>
										<tr>
											<th style="color: maroon;">Resigned</th>
											<th style="color: maroon;">{{$prevoius_out_emp['resigned']}}</th>
											<th style="color: maroon;">{{$current_out_emp['resigned']}}</th>
										</tr>
										<tr>
											<th style="color: red;">Terminated</th>
											<th style="color: red;">{{$prevoius_out_emp['terminated']}}</th>
											<th style="color: red;">{{$current_out_emp['terminated']}}</th>
										</tr>
										<tr>
											<th style="color: red;">Suspended</th>
											<th style="color: red;">{{$prevoius_out_emp['suspand']}}</th>
											<th style="color: red;">{{$current_out_emp['suspand']}}</th>
										</tr>
										<tr>
											<th style="color: red;">Deleted</th>
											<th style="color: red;">{{$prevoius_out_emp['deleted']}}</th>
											<th style="color: red;">{{$current_out_emp['deleted']}}</th>
										</tr>

									</tbody>
								</table>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            @endif

	            <div class="row no-padding no-margin" style="margin-top: 20px !important;">
	                <div class="widget-container-col col-sm-12" id="widget-container-col-4">
	                    <div class="widget-box no-padding no-margin" id="widget-box-5">
	                        <div class="widget-header widget-header-small">
		                        <h5 class="widget-title" >Amounts</h5>
		                    </div>

	                        <div class="widget-body">
	                        	<table class="table table-bordered  table-hover">
								<thead>
									<tr>
										<th style="background-color: lightgrey;">{{empty(Request::get('month'))?'':'No. of Employee'}}</th>
										<th style="background-color: lightgrey;">Month/Year</th>
										<th style="background-color: lightgrey;">Gross Salary</th>
										<th style="background-color: lightgrey;">OT Payble</th>
										<th style="background-color: lightgrey;">Total Payble</th>
									</tr>
								</thead>
		           	        	<tbody>
		           	        	@if(!empty(Request::get('month')))
		           	        		@if($salInfo_previous->emp > 0)
		           	        			<tr style="background-color: sandybrown; ">
		           	        				<td style="text-align: left;">
		           	        					{{
													$prevoius_out_emp['active']
													+$prevoius_out_emp['maternity']
													+$new_employee['previous']
												}}
											</td>
		           	        					<?php  
		           	        						$monthNum  = (int)$salInfo_previous->previous_month;
													$dateObj   = DateTime::createFromFormat('!m', $monthNum);
													$monthName = $dateObj->format('F');
												?>
		           	        				<td style="text-align: left;">{{$monthName}}-{{$salInfo_previous->previous_Y}}</td>
		           	        				<td style="text-align: left;">{{$salInfo_previous->total_payable-$salInfo_previous->ot_payable}}</td>
		           	        				<td style="text-align: left;">{{$salInfo_previous->ot_payable}}</td>
		           	        				<td style="text-align: left;">{{$salInfo_previous->total_payable}}</td>
		           	        				<?php $up_val=$salInfo_previous->total_payable; ?>
		           	        			</tr>
		           	        		@else
		           	        			<tr>
		           	        				<td colspan="5" style="text-align: center; color: red;">No Data Found for {{$monthName}}-{{$salInfo_previous->previous_Y}}</td>
		           	        			</tr>
		           	        		@endif

		           	        		@if($salInfo_input_month->emp > 0)
		           	        			<tr style="background-color: tomato; ">
		           	        				<td style="text-align: left;">
		           	        					{{
													$current_out_emp['active']
													+$current_out_emp['maternity']
													+$new_employee['current']
												}}
		           	        				</td>
		           	        				<td style="text-align: left;">{{ Request::get('month') }}</td>
		           	        				<td style="text-align: left;">{{$salInfo_input_month->total_payable-$salInfo_input_month->ot_payable}}</td>
		           	        				<td style="text-align: left;">{{$salInfo_input_month->ot_payable}}</td>
		           	        				<td style="text-align: left;">{{$salInfo_input_month->total_payable}}</td>
		           	        				<?php $down_val = $salInfo_input_month->total_payable; ?>
		           	        			</tr>
		           	        		@else
		           	        			<tr>
		           	        				<td colspan="5" style="text-align: center; color: red;">No Data Found for {{Request::get('month')}}</td>
		           	        			</tr>
		           	        		@endif

		           	        	@else
		           	        		{{-- <tr>
		           	        			<th colspan="5" style="color: forestgreen;">Input Year</th>
		           	        		</tr> --}}
		           	        		<tr style="background-color: tomato; ">
		           	        				<td>Yearly Total</td>
		           	        				<td style="text-align: left;">{{ Request::get('year') }}</td>
		           	        				<td style="text-align: left;">{{$input_year_salary->total_payable-$input_year_salary->ot_payable}}</td>
		           	        				<td style="text-align: left;">{{$input_year_salary->ot_payable}}</td>
		           	        				<td style="text-align: left;">{{$input_year_salary->total_payable }}</td>
		           	        				<?php $down_val = $input_year_salary->total_payable; ?>
		           	        			
		           	        		</tr>
		           	        		{{-- <tr>
		           	        			<th colspan="5" style="color: forestgreen;">Previous Year</th>
		           	        		</tr> --}}
		           	        		<tr style="background-color: sandybrown; ">
		           	        				<td>Yearly Total</td>
		           	        				<td style="text-align: left;">{{ (string)((int) Request::get('year')-1) }}</td>
		           	        				<td style="text-align: left;">{{$pre_year_salary->total_payable-$pre_year_salary->ot_payable}}</td>
		           	        				<td style="text-align: left;">{{$pre_year_salary->ot_payable}}</td>
		           	        				<td style="text-align: left;">{{$pre_year_salary->total_payable}}</td>
		           	        				<?php 
		           	        					$up_val = $pre_year_salary->total_payable; 
		           	        				?>
		           	        			
		           	        		</tr>	
		           	        	@endif
		           	        	
								</tbody>
							</table>
	                        </div>
	                    </div>
	                </div>
	            </div>	
			</div>
			<div class="col-xs-5  col-sm-5 pricing-box">
				<div class="widget-box widget-color-green2">
					<div class="widget-header">
						<h5 class="widget-title bigger">
							 Percentage
						</h5>
					</div>

					<div class="widget-body">
						<div class="widget-main center" style="min-height:220px;">
						@if(!empty(Request::get('month')))
							<?php  
	        						$monthNum  = (int)$salInfo_previous->previous_month;
								$dateObj   = DateTime::createFromFormat('!m', $monthNum);
								$monthName = $dateObj->format('F');
							?>
							@if($salInfo_input_month->emp > 0 && $salInfo_previous->emp > 0)
							<p style="color: black; font-size: 18px;"><span style="color: sandybrown; font-size: 18px;">{{$monthName}}-{{$salInfo_previous->previous_Y}}</span> : <span style="color: tomato; font-size: 18px;"> {{ Request::get('month') }}</span></p>
								{{-- <p style="color: forestgreen; font-size: 18px;"> = {{$up_val}} : {{$down_val}}</p> --}}
								@if($up_val-$down_val > 0)
								<p style="color: black; font-size: 18px;">Decreased Amount: {{round($up_val-$down_val,2)}} </p>
								<p style="color: forestgreen; font-size: 22px; font-weight: 900;">
									@if($up_val != 0)
									{{round( (($up_val-$down_val)/$up_val)*100 ,2)}} % Decrease
									@else
									% Can't be Calculated
									@endif
								</p>
								@else 
								<p style="color: black; font-size: 18px;">Incresed Amount: {{ round( $down_val-$up_val, 2) }} </p>
								<p style="color: forestgreen; font-size: 22px; font-weight: 900;">
									@if($up_val != 0)
									{{ round( (($down_val-$up_val)/$up_val)*100 , 2) }} % Increse
									@else
									% Can't be Calculated
									@endif
								</p>
								@endif
							@elseif($salInfo_input_month->emp == 0 && $salInfo_previous->emp > 0) 
								{{-- <p style="color: forestgreen; font-size: 18px;"> {{$up_val}} : 0.0 </p> --}}
								<p style="color: orange; font-size: 18px;">Insufficient Data to Compare.</p>
							@elseif($salInfo_input_month->emp > 0 && $salInfo_previous->emp == 0)
								{{-- <p style="color: forestgreen; font-size: 18px;"> 0.0 : {{$down_val}} </p> --}}
								<p style="color: orange; font-size: 18px;">Insufficient Data to Compare.</p>
							@else
								{{-- <p style="color: forestgreen; font-size: 18px;">0.0 : 0.0</p> --}}
								<p style="color: orange; font-size: 18px;">Insufficient Data to Compare.</p>
							@endif
						@else
							@if($yearly_compare == 'yes')
								<p style="color: black; font-size: 18px;"><span style="color: sandybrown; font-size: 18px;">{{ (string)((int) Request::get('year')-1) }}</span> : <span style="color: tomato; font-size: 18px;">{{ Request::get('year') }}</span></p>
								{{-- <p style="color: forestgreen; font-size: 18px;"> = {{$up_val}} : {{$down_val}}</p> --}}
								@if($up_val-$down_val > 0)
								<p style="color: black; font-size: 18px;">Decreased Amount: {{round($up_val-$down_val,2)}} </p>
								<p style="color: forestgreen; font-size: 22px; font-weight: 900;">{{round( (($up_val-$down_val)/$up_val)*100 ,2)}} % Decrease</p>
								@else 
								<p style="color: black; font-size: 18px;">Incresed Amount: {{ round( $down_val-$up_val, 2) }} </p>
								<p style="color: forestgreen; font-size: 22px; font-weight: 900;">{{ round( (($down_val-$up_val)/$up_val)*100 , 2) }} % Increse</p>
								@endif
							@else
								<p style="color: orange; font-size: 18px;">Insufficient Data to Compare. </p>
							@endif
						@endif

						</div>
					</div>
				</div>
			</div>
				
		</div>
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#widget-box-4').addClass('widget-box no-padding no-margin collapsed');
		// $('#widget-box-5').addClass('widget-box no-padding no-margin collapsed');
	});
</script>
