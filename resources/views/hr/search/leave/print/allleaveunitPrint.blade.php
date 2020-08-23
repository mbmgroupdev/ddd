<style>
	div#header_area {
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
		<h3 style="margin: 5px 0px;">Leave Report (Unit Wise)</h3>
		<h4 style="margin-top: 0px;">{{$title}}</h4>
	</div>
	<div id="body_area">
		<table>
			<thead>
				<th>Leave Name</th>
				<th>Leave Count</th>
			</thead>
			<tbody>
				@php
					$leave = 0;
				@endphp
				@foreach($data as $k=>$unit)
				<tr>
					<td colspan="2" style="font-weight: bold;">{{$unit['hr_unit_name']}}</td>
					<td></td>
				</tr>
					@php
	                    $totalLeaveCount = 0;
	                @endphp
	                @foreach($data1[$unit['hr_unit_id']] as $type=>$unit_leave)
	                @php
	                    $totalLeaveCount += count($unit_leave);
	                @endphp
						<tr>
							<td>{{$type}}</td>
							<td>{{ count($unit_leave) }}</td>
						</tr>
	                @endforeach
				@endforeach
			</tbody>
		</table>
	</div>
</div>