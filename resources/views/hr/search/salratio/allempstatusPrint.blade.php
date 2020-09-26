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
		<h3 style="margin: 5px 0px;">Salary Ratio Report</h3>
		<h4 style="margin-top: 0px;">{{$title}}</h4>
	</div>
	<div id="body_area" style="width:800px; display: block;height:600px;">
		<div style="width:60%;float:left;display:inline;">
			<table style="width:100%;border-collapse: collapse;">
				<thead>
					<th>Status Type</th>
					<th>Employee</th>
					<th>Salary</th>
				</thead>
				<tbody>
					@php $saved = 0; @endphp
					@if(!empty($data))
					@foreach($data as $status)
						<tr>
							<td>{{ucfirst(Custom::getEmpStatusName($status['as_status']))}}</td>
							<td>{{$status['employee']}}</td>
							<td>{{$status['total_payable']}}</td>
						</tr>
						@php $saved += $status['total_payable']; @endphp 
					@endforeach
					@endif
					@if(!empty($joined))
						<tr >
							<td>New Employee</td>
							<td>{{$joined['employee']}}</td>
							<td>{{$joined['total_payable']}}</td>
						</tr> 
						@php $joined = $joined['total_payable'];@endphp
				    @else
				    	@php $joined = 0 ;@endphp
					@endif

					@php 
	   	        		$rest = $saved-$joined;
	   	        		if($rest>=0){
	   	        			$graph_status = 1;
	   	        			$stat = "Saved Amount";
	   	        			$net = $joined;
	   	        		}else{
	   	        			$graph_status = 0;
	   	        			$stat = "Extra Amount";
	   	        			$net = $saved;
	   	        		}
	   	        		$ratio = abs($rest); 
	   	        		$total = $ratio+$net;
	   	        		if($total==0){$total=1;}
	   	        		$net_per = round(($net/$total)*100,2);
	   	        		$net_star = 100-$net_per;
	   	        	@endphp
				</tbody>
			</table>
		</div>
		<div style="width:35%;margin-left:10px;display:inline;float:right;">
		  	<h4> Salary Payable : {{$net}} ({{$net_per}}%) </h4>
		  	<h4> {{$stat}} : {{$ratio}} ({{$net_star}}%)</h4>
		</div>
	</div>
</div>