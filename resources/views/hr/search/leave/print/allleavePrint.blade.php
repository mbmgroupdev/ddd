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
		<h3 style="margin: 5px 0px;">Leave Report</h3>
		<h4 style="margin-top: 0px;">{{$title}}</h4>
	</div>
	<div id="body_area">
		<table>
			<thead>
				<th>Name</th>
				<th>Leave Count</th>
			</thead>
			<tbody>
				@foreach($data as $k=>$single)
					<tr>
						<td>{{ucwords($k)}}</td>
						<td>{{$single}}</td>
					</tr>
				@endforeach
				<tr>
					<td style="font-weight: bold;">Total</td>
					<td>{{array_sum($data)}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>