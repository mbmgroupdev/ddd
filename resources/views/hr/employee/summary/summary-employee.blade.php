<table class="table table-bordered " style="width: 800px;margin:0 auto;text-align: center;">
	<tr>
		<th style="text-align: left;" rowspan="2">Department</th>
		<th rowspan="2">CEIL</th>
		<th colspan="4">MBM</th>
		<th rowspan="2">AQL</th>
		<th rowspan="2">CEW</th>
	</tr>
	<tr>
		<th>MBM</th>
		<th>MFW</th>
		<th>MBM-2</th>
		<th>Total</th>
	</tr>
	@foreach($data as $key => $d)
		<tr >
			<td style="text-align: left;">
				{{$dept[$key]['hr_department_name']}}
			</td>
			<td>
				@isset($d[2])
					{{$d[2]->total}}
				@endisset
			</td>
			<td>
				@isset($d[1])
					{{$d[1]->total}}
				@endisset
			</td>
			<td>
				@isset($d[4])
					{{$d[4]->total}}
				@endisset
			</td>
			<td>
				@isset($d[5])
					{{$d[5]->total}}
				@endisset
			</td>
			<td></td>
			<td>
				@isset($d[3])
					{{$d[3]->total}}
				@endisset
			</td>
			<td>
				@isset($d[8])
					{{$d[8]->total}}
				@endisset
			</td>
		</tr>
	@endforeach
</table>