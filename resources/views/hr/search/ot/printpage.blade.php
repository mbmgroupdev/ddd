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
		<h3 style="margin-top: 5px; margin-bottom: 5px;">Over Time Report @if($type != 'Total') ({{$type}}  Wise) @endif</h3>
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

	@if($type == 'Day')
		<table>
			<tr>
				<th>Date</th>
				<th>Total OT</th>
			</tr>
			<tr>
				<td>{{$data['day']}}</td>
				<td>{{ Custom::numberToTime($data['dayot']) }}</td>
			</tr>
			<tr>
				<td>{{$data['day1']}}</td>
				<td>{{ Custom::numberToTime($data['dayot1']) }}</td>
			</tr>
			<tr>
				<td>{{$data['day1']}}</td>
				<td>{{ Custom::numberToTime($data['dayot2']) }}</td>
			</tr>

		</table>
	@elseif($type == 'Month')
		<h3 style="text-align:center;">Employee : {{$data['emp']}}</h3>	
		<h3 style="text-align:center;">Over Time : {{ Custom::numberToTime($data['ot']) }} Hour(s)</h3>
	@elseif($type == 'Shift')
		@php 
			$totalOt=0;
			$totalOt1=0;
			$totalOt2=0;
		@endphp
		<table>
				<thead>
				<tr>
					<th rowspan="2">Shift</th>
					<th rowspan="2">Shift Name</th>
					<th colspan="3"> OT Hour'(s)</th>
				<tr>
				<tr></tr>
				<tr>
					<th ></th>
					<th ></th>
					@php $count=0; @endphp
					@foreach($data as $k=> $page)
					@php $count++; @endphp
					@if($count==1)
					<th >{{$page['day']}}</th>
					<th >{{$page['day1']}}</th>
					<th >{{$page['day2']}}</th>
					@endif
					@endforeach
				<tr>
				</thead>
				<tbody>
					@foreach($data as $k=> $page)
					<tr>
						<td>{{$k}}</td>
						<td style="color:#000;">{{$page['name']}}</td>
						<td>{{ Custom::numberToTime($page['dayot']) }} </td>
						<td>{{ Custom::numberToTime($page['dayot1']) }} </td>
						<td>{{ Custom::numberToTime($page['dayot2']) }} </td>
					</tr>
					@php 
						$totalOt+=$page['dayot'];
						$totalOt1+=$page['dayot1'];
						$totalOt2+=$page['dayot2'];
					@endphp
					@endforeach
					<tr>
						<td colspan="2"  style="font-weight: bold;">Total</td>
						<td><b>{{ Custom::numberToTime($totalOt) }} </b></td>
						<td><b>{{ Custom::numberToTime($totalOt1) }} </b></td>
						<td><b>{{ Custom::numberToTime($totalOt2) }} </b></td>
					</tr>
				</tbody>
			</table>	

	@elseif($type == 'Hour')
		@php 
			$totalOt=0;
		@endphp
		<table>
			<thead>
				<tr>
					<th>Hour</th>
					<th>Employee</th>
				<tr>
			</thead>
			<tbody>
        		<tr>
        			<td>1 Hour</td>
        			<td>{{$data['1']}}</td>
        		</tr>
        		<tr>
        			<td>2 Hour</td>
        			<td>{{$data['2']}}</td>
        		</tr>
        		<tr>
        			<td>3 Hour</td>
        			<td>{{$data['3']}}</td>
        		</tr>	           	        		
        		<tr>
        			<td>4 Hour</td>
        			<td>{{$data['4']}}</td>
        		</tr>
        		<tr>
        			<td>5 Hour</td>
        			<td>{{$data['5']}}</td>
        		</tr>
        		<tr>
        			<td>6 Hour</td>
        			<td>{{$data['6']}}</td>
        		</tr>
        		<tr>
        			<td>7 Hour</td>
        			<td>{{$data['7']}}</td>
        		</tr>
        		<tr>
        			<td>8 Hour</td>
        			<td>{{$data['8']}}</td>
        		</tr>
        	</tbody>
		</table>

	@else
		@php 
			$totalOt=0;
			$totalEmp=0;
		@endphp
		
			<table>
				<thead>
					<th>{{$type}} Name</th>
					<th>Employee</th>
					<th>OT Hour</th>
				</thead>
				<tbody>
					@foreach($data as $k=>$page)
					<tr>
						<td style="color:#000;">{{$page['name']}}</td>
						<td>{{$page['employee']}}</td>
						<td> {{ Custom::numberToTime($page['ot_hour']) }} </td>
					</tr>
					@php 
						$totalOt+=$page['ot_hour'];
						$totalEmp+=$page['employee'];
					@endphp
					@endforeach
					<tr>
						<td style="font-weight: bold;">Total</td>
						<td><b>{{$totalEmp}}</b></td>
						<td><b>{{ Custom::numberToTime($totalOt) }}</b></td>
					</tr>
				</tbody>
			</table>
	@endif
	</div>
</div>