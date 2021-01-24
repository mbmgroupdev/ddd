<div class="panel">
	<div class="panel-body">
		<div class="inner_body_content">
           <div class="body_top_section">
           		<h2 class="text-center modal-h3">Employee Yearly Activity Report - {{ $year }}</h2>
           		<h3 class="text-center modal-h3"><strong>Name </strong>: {{ $employee->as_name }}</b></h3>
           		<h3 class="text-center modal-h3"><strong>ID </strong>: {{ $employee->associate_id }}</h3>
           		<h3 class="text-center modal-h3"><strong>Designation </strong>: {{ $employee->designation['hr_designation_name'] }}</h3>
           </div>
           <div class="body_content_section">
           	<div class="body_section" id="">
           		<table class="table table-bordered table-hover table-head">
           			<thead>
           				<tr>
           					<th>Month</th>
           					<th>Present</th>
                    <th>Absent</th>
           					<th>Late</th>
           					<th>Leave</th>
           					<th>Holiday</th>
           				</tr>
           			</thead>
           			<tbody>
           				@if(count($getData) > 0)
						@foreach($getData as $data)
						<tr>
							<td>{{ date("F", mktime(0, 0, 0, $data->month, 1)) }}</td>
							<td>{{ $data->present }}</td>
              <td>{{ $data->absent }}</td>
							<td>{{ $data->late_count }}</td>
							<td>{{ $data->leave }}</td>
							<td>{{ $data->holiday }}</td>
						</tr>
						@endforeach
           				@else
						<tr>
							<td colspan="5" class="text-center"> No Data Found! </td>
						</tr>
           				@endif
           			</tbody>
           		</table>
           		
           	</div>
           </div>
        </div>
	</div>
</div>