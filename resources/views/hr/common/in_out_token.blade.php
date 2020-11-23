
<div style="padding: 20px;">
	<button class="btn btn-primary hidden-print" onclick="printDiv('token_body')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print Report"><i class="las la-print"></i> </button>
	<div id="token_body">
		<style type="text/css" media="all">
			#result-section .iq-card-header{
				display: none !important;
			}
			.table-d{
				width: 100%;
				border-collapse: collapse;
			}
			.center{
				text-align: center;
			}
			.table-d td{
				padding: 5px;
			}
			.page-break{page-break-after: always;}
			#single-employee-search{
				display: none !important;
			}
		</style>

		@php $k = 0; @endphp
		@foreach($attData as $key => $att)
			@php $k++;  @endphp
			<table class="table-d" border="1">
				<tr>
					<td colspan="4" class="center">
						<h3>{{$unit[$att->as_unit_id]['hr_unit_name_bn']}}</h3>
						<strong>ইন আউট পাঞ্চ মিসিং কারেকশন</strong>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="center">শনাক্ত করন</td>
					<td colspan="2" class="center">সংশোধনকৃত তারিখ ও সময়</td>
				</tr>
				<tr>
					<td>নামঃ</td>
					<td>{{$att->as_name}}</td>
					<td>তারিখঃ</td>
					<td>{{$request['date']}}</td>
				</tr>
				<tr>
					<td>আইডিঃ</td>
					<td><strong style="font-size: 15px;">{{$att->associate_id}}</strong> ({{$att->as_oracle_code}})</td>
					<td rowspan="2">ইনটাইম</td>
					<td rowspan="2">
						@if(isset($att->in_time))
							@if($att->remarks != 'DSI') {{$att->in_time}} @endif
						@endif
					</td>
				</tr>
				<tr>
					<td>পদবীঃ</td>
					<td>{{$designation[$att->as_designation_id]['hr_designation_name_bn']}}</td>
				</tr>
				<tr>
					<td>সেকশন</td>
					<td>{{$section[$att->as_section_id]['hr_section_name_bn']}}</td>
					<td rowspan="2">আউটটাইম</td>
					<td rowspan="2">
						@if(isset($att->out_time))
							 {{$att->out_time}} 
						@endif</td>
				</tr>
				<tr>
					<td>বিভাগ</td>
					<td>{{$department[$att->as_department_id]['hr_department_name_bn']}}</td>
				</tr>
				<tr>
					<td colspan="4">কারন,<br><br><br> </td>
					
				</tr>
				<tr class="center">
					<td><br><br>------------<br> আবেদনকারীর স্বাক্ষর</td>
					<td><br><br>------------<br> সেকশন প্রধান এর স্বাক্ষর</td>
					<td><br><br>------------<br> ফ্লোর প্রতিনিধির স্বাক্ষর</td>
					<td><br><br>------------<br> অনুমোদনকারীর স্বাক্ষর</td>
				</tr>
			</table>
			<br><br><br>
			@if($k == 2)
			@php $k = 0;  @endphp
			<div class="page-break"></div>
			@endif
		@endforeach
		@foreach($absData as $key => $att)
			@php $k++;  @endphp
			<table class="table-d" border="1">
				<tr>
					<td colspan="4" class="center">
						<h3>{{$unit[$att->as_unit_id]['hr_unit_name_bn']}}</h3>
						<strong>ইন আউট পাঞ্চ মিসিং কারেকশন</strong>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="center">শনাক্ত করন</td>
					<td colspan="2" class="center">সংশোধনকৃত তারিখ ও সময়</td>
				</tr>
				<tr>
					<td>নামঃ</td>
					<td>{{$att->as_name}}</td>
					<td>তারিখঃ</td>
					<td>{{$request['date']}}</td>
				</tr>
				<tr>
					<td>আইডিঃ</td>
					<td><strong style="font-size: 15px;">{{$att->associate_id}}</strong> ({{$att->as_oracle_code}})</td>
					<td rowspan="2">ইনটাইম</td>
					<td rowspan="2">
						@if(isset($att->in_time))
							@if($att->remarks != 'DSI') {{$att->in_time}} @endif
						@endif
					</td>
				</tr>
				<tr>
					<td>পদবীঃ</td>
					<td>{{$designation[$att->as_designation_id]['hr_designation_name_bn']}}</td>
				</tr>
				<tr>
					<td>সেকশন</td>
					<td>{{$section[$att->as_section_id]['hr_section_name_bn']}}</td>
					<td rowspan="2">আউটটাইম</td>
					<td rowspan="2">
						@if(isset($att->out_time))
							 {{$att->out_time}} 
						@endif</td>
				</tr>
				<tr>
					<td>বিভাগ</td>
					<td>{{$department[$att->as_department_id]['hr_department_name_bn']}}</td>
				</tr>
				<tr>
					<td colspan="4" >কারন, <br><br><br></td>
					
				</tr>
				<tr class="center">
					<td><br><br>------------<br> আবেদনকারীর স্বাক্ষর</td>
					<td><br><br>------------<br> সেকশন প্রধান এর স্বাক্ষর</td>
					<td><br><br>------------<br> ফ্লোর প্রতিনিধির স্বাক্ষর</td>
					<td><br><br>------------<br> অনুমোদনকারীর স্বাক্ষর</td>
				</tr>
			</table>
			<br><br><br>
			@if($k == 2)
			@php $k = 0;  @endphp
			<div class="page-break"></div>
			@endif
		@endforeach
	</div>
</div>