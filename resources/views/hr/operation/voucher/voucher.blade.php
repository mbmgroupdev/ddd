<button type="button" onclick="printMe('voucher_area')" class="btn btn-warning" title="Print">
    <i class="fa fa-print"></i> 
</button>
<div  id="voucher_area" style="font-size: 11px;width:800px;">
    <div class="tinyMceLetter" style="font-size: 11px;">
    	<h3 style="text-align: center">
    		<u>DEBIT VOUCHER</u>
    	</h3 style="text-align: center">
    	<h2><b>{{$employee->hr_unit_name}}</b></h2>
    	<p style="text-align: right">Date: {{$voucher->created_at->format('Y-m-d')}}</p>
    	<p>
    		<strong>Name: {{$employee->as_name}} , Associate ID #{{$employee->associate_id}} , Designation- {{$employee->hr_designation_name}} ,Salary-{{$employee->ben_current_salary}} /-Taka</strong>
    	</p>
    	<table border="0" style="width:100%;">
    		<tr>
    			<td style="text-align: center;">Descriptions</td>
    			<td style="text-align: right;">Taka/ ps</td>
    		</tr>
    		<tr>
    			<td>
    				{!!$voucher->descriptions!!}
    			</td>
    			<td>
    				<strong>{{$voucher->amount}}</strong>
    			</td>
    		</tr>
    		<tr>
    			<td>{{num_to_word($voucher->amount)}} Taka Only</td>
    			<td><strong>{{$voucher->amount}}</strong></td>
    		</tr>
    		<tr>
    			<td colspan="2">Recieved payment in full</td>
    		</tr>
    	</table>
    	<br><br><br>
    	<table border="0" style="width:100%;">
    		<tr>
    			<td>Prepared By</td>
    			<td>Accountant By</td>
    			<td>Received By</td>
    		</tr>
    	</table>
    </div>
</div>