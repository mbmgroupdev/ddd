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
		<h3 style="margin: 5px 0px;">Attendance Report (Section Wise)</h3>
		<h3 style="margin: 5px 0px;">Unit: {{$unitName}}</h3>
		<h3 style="margin: 5px 0px;">Area: {{$areaName}} | Department: {{$departmentName}} | Floor: {{$floorName}} | Section: {{$sectionName}}</h3>
		<h4 style="margin-top: 0px;">{{$title}}</h4>
	</div>
	<div id="body_area">
		<table>
			<thead>
				<th>Name</th>
				<th>Present</th>
				<th>Absent</th>
				<th>Late</th>
				<th>Leave</th>
			</thead>
			<tbody>
				@php
					$present = 0;
					$absent = 0;
					$late = 0;
					$leave = 0;
				@endphp
				@foreach($data as $k=>$subsection)
				<tr>
					<td>{{$subsection['hr_subsec_name']}}</td>
					@php
						unset($response[$subsection['hr_subsec_id']]['attendance']);
					@endphp
					@foreach($response[$subsection['hr_subsec_id']] as $k=>$single)
						@php
							if($k=='present') {
								$present += $single;
							}
							if($k=='absent') {
								$absent += $single;
							}
							if($k=='late') {
								$late += $single;
							}
							if($k=='leave') {
								$leave += $single;
							}
						@endphp
						<td>{{$single}}</td>
					@endforeach
				</tr>
				@endforeach
				<tr>
					<td style="font-weight: bold;">Total</td>
					<td>{{$present}}</td>
					<td>{{$absent}}</td>
					<td>{{$late}}</td>
					<td>{{$leave}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>