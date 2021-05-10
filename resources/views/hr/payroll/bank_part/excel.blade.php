<div class="panel">
	<div class="panel-body">

		<div id="report_section" class="report_section">
			<div class="top_summery_section">
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Salary Report - {{ $input['pay_status']}} </h2>
		            <h4  style="text-align: center;">Month : {{ date('M Y', strtotime($input['month'])) }} </h4>
		            
		        </div>
		        
			</div>

			<div class="content_list_section">
					
				<table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
					<thead>
		                <tr>
		                    <th>Sl</th>
		                    <th>Oracle ID</th>
		                    <th>Associate ID</th>
		                    <th>Name</th>
		                    <th>Pay Amount</th>
		                    <th>TDS</th>
		                    <th class="capitalize">{{ $input['pay_status']}} Account No.</th>
		                    <th>Unit</th>
		                    <th>Location</th>
		                </tr>
		            </thead>
		            <tbody>
		            @php $i = 0; $salarySum=0; $tds = 0; @endphp
		            @if(count($getEmployee) > 0)
			            @foreach($getEmployee as $employee)
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	<td>{{ $employee->as_oracle_code }}</td>
				            	<td>{{ $employee->associate_id }}</td>
				            	<td> <b>{{ $employee->as_name }}</b> </td>
				            	<td style="text-align:right;">{{ $employee->bank_payable }}</td>
					            <td style="text-align:right;">{{ $employee->tds }}</td>
				            	<td>
				            		{{ $employee->bank_no }}
				            		@php
				            			$salarySum = $salarySum + $employee->bank_payable;
				            			$tds = $tds + $employee->tds;
				            		@endphp
				            	</td>
				            	<td>{{ $unit[$employee->unit_id]['hr_unit_short_name']??'' }}</td>
				            	<td>{{ $location[$employee->location_id]['hr_location_short_name']??'' }}</td>
			            	</tr>
			            @endforeach
			            	<tr>
			            		<td colspan="4" class="text-right" style="text-align:right;"><b>Total Amount</b></td>
		            		
			            		<td style="text-align:right;"><b>{{ ($salarySum) }}</b></td>
			            		<td style="text-align:right;"><b>{{ ($tds) }}</b></td>
			            		<td colspan="3"> &nbsp;</td>
			            	</tr>
		            @else
			            <tr>
			            	<td colspan="9" class="text-center">No Employee Found!</td>
			            </tr>
		            @endif
		            </tbody>

		            
				</table>
				
			</div>
		</div>
	</div>
</div>
