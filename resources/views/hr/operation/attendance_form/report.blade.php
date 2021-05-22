<div class="panel">
	<div class="panel-body pt-0">

		<div id="report_section" class="report_section">
			<style type="text/css" media="print">

				h4, h2, p{margin: 0;}
				.text-right{text-align:right;}
				.text-center{text-align:center;}
			</style>
			<style>
              .table{
                width: 100%;
              }
              a{text-decoration: none;}
              .table-bordered {
                  border-collapse: collapse;
              }
              .table-bordered th,
              .table-bordered td {
                border: 1px solid #777 !important;
                padding:5px;
              }
              .no-border td, .no-border th{
                border:0 !important;
                vertical-align: top;
              }
              .f-14 th, .f-14 td, .f-14 td b{
                font-size: 14px !important;
              }
              .table thead th {
			    vertical-align: inherit;
				}
				.content-result .panel .panel-body .loader-p{
					margin-top: 20% !important;
				}
				.modal-h3{
					line-height: 1;
				}
			</style>


<!--			<div class="top_summery_section">
				<div class="page-header">
		            <h4 style="margin:4px 10px; font-weight: bold; text-align: center;">Attendance Form</h4>
		        </div>
			</div>-->
            @foreach($results->chunk(14) as $resultData)
			<div class="content_list_section">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center" style="font-family: 'Courier New'">
                            <h3>ATTENDANCE SHEET FOR  {{ \Carbon\Carbon::parse($month_year)->format('M-Y')}}</h3>
                        </div>
                        <div class="table-responsive">
                        <table class="table" valign="top" style="border-collapse: collapse; border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;">
                            <thead>
                            <tr style="border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;">
                                <th style="border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;"></th>
                                <th style="font-size: 6pt; border: 1px solid black; font-family: Tahoma, sans-serif;" rowspan="2">Name of Employees <br> ID No & Designation <br> Date of Join</th>
                                @for($day = 1; $day <= $total_days_month; $day++)
                                    <td style="border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;">{{ date('D', strtotime($month_year.'-'.$day)) }}</td>
                                @endfor
                                <th style="border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;" colspan="2"></th>
                            </tr>
                            <tr valign="top"  style="padding: 0in; text-align: center">
                                <th valign="top"  style="font-size: 8pt; border: 1px solid black; font-family: Tahoma, sans-serif;">Srl</th>
                                @for($day = 1; $day <= $total_days_month; $day++)
                                    <th style="border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;">{{$day}}</th>
                                @endfor
                                <th style="font-size: 8pt; border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;">Ttl</th>
                                <th style="font-size: 8pt; border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;">Rmks</th>
                            </tr>
                            </thead>

                            <tbody class="text-center" >
                            <?php $i = 1; ?>
                            @foreach($resultData as $result)
                                <tr>
                                    <td style="border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;">{{$i}}</td>
                                    <td  style="border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;"> {{$result->as_name}}
                                        <br><span>ID:(<b>{{$result->associate_id}}</b>)</span>
                                        <br>DOJ: {{$result->as_doj}}
                                    </td>
                                    @for($day = 1; $day <= $total_days_month+2; $day++)
                                        <td style="border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;" ></td>
                                    @endfor
                                </tr>
<!--                               <tr >
                                    <td style="font-size: 10px; border: 1px solid black; font-family: Tahoma, sans-serif;"><span style="font-weight: bolder; font-size: 10px ">ID</span>({{$result->associate_id}})</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid black; font-family: Tahoma, sans-serif; font-size: 10pt;"><b>DOJ:</b>{{$result->as_doj}}</td>
                                </tr>-->
                                <?php $i++?>
                            @endforeach

                            </tbody>

                        </table>
                        </div>
                    </div>
                </div>
            </div>
                <br><br>@endforeach
		</div>
	</div>
</div>

{{--  --}}

<script type="text/javascript">

</script>
