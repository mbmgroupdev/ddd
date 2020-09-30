<button type="button" onclick="printMe('voucher_area')" class="btn btn-warning" title="Print">
    <i class="fa fa-print"></i> 
</button>
<div  id="voucher_area" style="font-size: 12px;width:800px;margin: auto;">
    <div class="tinyMceLetter" style="font-size: 12px;">
    	<h3 style="text-align: center">
    		<u>DEBIT VOUCHER</u>
    	</h3 >
    	<h2 style="text-align: center"><b>{{$employee->hr_unit_name}}</b></h2>
    	<p style="text-align: right">Date: {{$voucher->created_at->format('Y-m-d')}}</p>
    	<p>
    		<strong>Name: {{$employee->as_name}} , Associate ID #{{$employee->associate_id}} , Designation- {{$employee->hr_designation_name}} ,Salary-{{$employee->ben_current_salary}} /-Taka</strong>
    	</p>
    	<table border="0" style="width:100%;">
    		<tr style="border-top: 1px solid #d1d1d1;border-bottom: 1px solid #d1d1d1; font-weight: bold; ">
    			<td style="text-align: center;padding: 10px 0;">Descriptions</td>
    			<td style="text-align: right;padding: 10px 0;">Taka/ ps</td>
    		</tr>
    		<tr>
    			<td style="padding: 10px 0;">
    				{!!$voucher->description!!}
    			</td>
    			<td style="text-align: right;">
    				<strong>{{$voucher->amount}}</strong>
    			</td>
    		</tr>
    		<tr style="border-top: 1px solid #d1d1d1;border-bottom: 1px solid #d1d1d1; ">
    			<td style="text-transform: capitalize;">{{num_to_word($voucher->amount)}} Taka Only</td>
    			<td style="text-align: right;"><strong>{{$voucher->amount}}</strong></td>
    		</tr>
    		<tr>
    			<td colspan="2">Recieved payment in full</td>
    		</tr>
    	</table>
    	<br><br><br>
    	<table border="0" style="width:100%;">
    		<tr style="text-align: center;"">
    			<td>Prepared By</td>
    			<td>Accountant By</td>
    			<td>Received By</td>
    		</tr>
    	</table>
    </div>
</div>