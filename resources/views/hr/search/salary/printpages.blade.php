<style>
	div#header_area, div#body_area h3 {
	    width: 100%;
	    text-align: center;
	}

	div#body_area {
	    width: 100%;
	}

	div#body_area > table {
	    width: 100%;
	}

	div#body_area > table, tr, td,th {
	    border: 1px solid;
	    border-collapse: collapse;
	    text-align: center;
	}
	div#full_body_area {
	    border: 1px solid lightgray;
	    padding: 5px;
	}
	td,th {
	    padding: 5px 0px;
	}
</style>
<div id="full_body_area">
	<div id="header_area">
		<h2 style="margin-bottom: 0px; ">MBM Garments Ltd.</h2>
		<h3 style="margin-top: 5px; margin-bottom: 5px;">Salary Report @if($type != 'Total') ({{$type}}  Wise) @endif</h3>
		@if(isset($info['unit']))
		<h3 style="margin: 5px 0px;">Unit: {{$info['unit']}}</h3>
		@endif
		@if(isset($info['area']))
		<h3 style="margin: 5px 0px;">Area: {{$info['area']}} 
			@if(isset($info['department']))| Department: {{$info['department']}} @endif
			@if(isset($info['floor']))| Floor: {{$info['floor']}}  @endif
			@if(isset($info['section']))| Section: {{$info['section']}} @endif
		</h3>
		@endif
		<h4 style="margin-top: 0px;">{{$title}}</h4>
	</div>
	<div id="body_area">
	@if($type == 'Total')
	 @php //dd($data); @endphp
		<h3 style="margin: 5px 0px;">Total Employee: {{$data->emp}}</h3>
		<h3 style="margin: 5px 0px;">OT Payable: {{$data->ot}}</h3>
		<h3 style="margin: 5px 0px;">Salary Payable: {{$data->salary}}</h3>
		<h3 style="margin: 5px 0px;">Total Payable: {{$data->total}}</h3>
	@else
		@php 
			$totalOt=0;
			$totalSal=0;
			$totalPay=0;
			$totalEmp=0;
		@endphp
		
			<table>
				<thead>
					<th>{{$type}} Name</th>
					<th>Employee</th>
					<th>OT Payable</th>
					<th>Salary Payable</th>
					<th>Total Payable</th>
				</thead>
				<tbody>
					@foreach($data as $k=>$page)
					<tr>
						<td style="color:#000;">{{$page['name']}}</td>
						<td>{{$page['emp']}}</td>
						<td>{{$page['ot']}}</td>
						<td>{{$page['salary']}}</td>
						<td>{{$page['total']}}</td>
					</tr>
					@php 
						$totalOt+=$page['ot'];
						$totalSal+=$page['salary'];
						$totalPay+=$page['total'];
						$totalEmp+=$page['emp'];
					@endphp
					@endforeach
					<tr>
						<td style="font-weight: bold;">Total</td>
						<td><b>{{$totalEmp}}</b></td>
						<td><b>{{$totalOt}}</b></td>
						<td><b>{{$totalSal}}</b></td>
						<td><b>{{$totalPay}}</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	@endif
</div>