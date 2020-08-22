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
		<h3 style="margin: 5px 0px;">Leave Report (Section Wise)</h3>
		<h4 style="margin: 5px 0px;">Unit: {{ucwords($unitName)}} | Area: {{$areaName}} | Department: {{$departmentName}} | Floor: {{$floorName}} | Section: {{$sectionName}}</h4>
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
                    $totalLeaveCount = 0;
                @endphp
				@foreach($data as $k=>$subsec)
				<tr>
					<td colspan="2" style="font-weight: bold;">{{ucwords($subsec['hr_subsec_name'])}}</td>
					<td></td>
				</tr>
	                @foreach($data1[$subsec['hr_subsec_id']] as $type=>$subsec_leave)
	                @php
	                    $totalLeaveCount += count($subsec_leave);
	                @endphp
						<tr>
							<td>{{$type}}</td>
							<td>{{ count($subsec_leave) }}</td>
						</tr>
	                @endforeach
				@endforeach
				<tr>
					<td>Total</td>
					<td>{{$totalLeaveCount}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>