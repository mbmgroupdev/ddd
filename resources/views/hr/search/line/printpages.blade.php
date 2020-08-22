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
		<h3 style="margin-top: 5px; margin-bottom: 5px;">Line Change Report @if($type!='Total') ({{$type}} Wise) @endif</h3>
		@if(isset($info['unit']))
		<h3 style="margin: 5px 0px;">Unit: {{$info['unit']}}</h3>
		@endif
		@if(isset($info['floor']))
		<h3 style="margin: 5px 0px;"> Floor: {{$info['floor']}}  
			@if(isset($info['line']))| Line: {{$info['line']}} @endif
		</h3>
		@endif
		<h4 style="margin-top: 0px;">{{$title}}</h4>
	</div>
	<div id="body_area">
	@if($type == 'Total')
	 @php //dd($data); @endphp
		<h3 style="margin: 5px 0px;">Total Unit: {{$data['unit']}}</h3>
		<h3 style="margin: 5px 0px;">Changed Line: {{$data['line']}}</h3>
		<h3 style="margin: 5px 0px;">Changed Employee: {{$data['emp']}}</h3>
		<h3 style="margin: 5px 0px;">Total Changes: {{$data['line_change']}}</h3>
	@else
		
		<table>
			<thead>
				<th>{{$type}} Name</th>
				<th>Employee</th>
				<th>Line Change</th>
			</thead>
			<tbody>
				@foreach($data as $k=>$page)
				<tr>
				@php
				  if($type == 'Unit'){
				  	$name = 'hr_unit_name';
				  }
				  if($type == 'Floor'){
				  	$name = 'hr_floor_name';
				  }
				  if($type == 'Line'){
				  	$name = 'hr_line_name';
				  }
				@endphp
					<td style="color:#000;">{{$page[$name]}}</td>
					<td>{{$page['emp']}}</td>
					<td>{{$page['line_change']}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	@endif
	</div>
</div>