@foreach($data as $info)
	

          <table class="table table-bordered" style="border: 1px; border-color: lightgrey;">
          	<tr>
          		<td style="font-weight: bold;">Associate ID</td>
          		<td>{{ $info->associate_id}}</td>
          	</tr>
          	<tr>
          		<td style="font-weight: bold;">Associate Name</td>
          		<td>{{ $info->as_name}}</td>
          	</tr>
          	<tr>
          		<td style="font-weight: bold;">Floor</td>
          		<td>{{ $info->floor['hr_floor_name'] }}</td>
          	</tr>
          	<tr>
          		<td style="font-weight: bold;">Line</td>
          		<td>{{ $info->line['hr_line_name'] }}</td>
          	</tr>
          	<tr>
          		<td style="font-weight: bold;">Shift</td>
          		<td>{{ $info->shift['hr_shift_name'] }}</td>
          	</tr>
          </table>

    
@endforeach