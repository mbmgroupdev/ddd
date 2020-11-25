<style type="text/css">
	.row-sum{border-top:1px solid #f1f1f1!important; }
    .sum-row .profile-info-name,.sum-row  .profile-info-value,.row-sum .profile-info-name,.row-sum  .profile-info-value{color: #1a1a1a;}
    .profile-info-value {
	    min-width: 170px!important;
	}
	.profile-info-row{
		display: block;
	}
</style>
<div class="panel panel-info col-sm-12 col-xs-12">
   <br>
	<div class="breadcrumbs ace-save-state" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-angle-double-right"></i>
                 MBM Group 
            </li>
        </ul>
        <a href="#" id="printButton" class="btn btn-xs btn-info pull-right" onclick='printDiv("{{$showTitle}}")'>Print</a>
    </div>

    <hr>
    <p class="search-title">Search results of  {{ $showTitle }}</p>
    <div class="panel-body">
        
    	<div class="row choice_2_div" id="choice_2_div" name="choice_2_div">
            <div class="col-sm-3 col-xs-6">
                <div class="row">    
        			<div class="search_unit col-xs-12 col-sm-12 pricing-box pr-0 mb-2" >
        				<div class="widget-box widget-color-green2">
        					<div class="widget-header">
        						<h5 class="widget-title bigger lighter">Total Unit</h5>
        					</div>

        					<div class="widget-body" style="height:80px;">
        						<div class="widget-main center" style="margin: 10px 0;">
        							<span class="infobox-data-number">{{ count($unit_list) }}</span>
        						</div>
        					</div>
        				</div>
        			</div>
        			<div class="col-xs-12 col-sm-12 pricing-box pr-0">
        				<div class="widget-box widget-color-green2 search_emp">
        					<div class="widget-header">
        							<h5 class="widget-title bigger lighter">
        								Total Employee
        							</h5>
        					</div>

        					<div class="widget-body" style="height:80px;">
        						<div class="widget-main center" style="margin: 10px 0;">
        							<span class="infobox-data-number">{{ $salary->employee }}</span>
        						</div>
        					</div>
        				</div>
        			</div>
                </div>
                    
            </div>

			

			<div class="col-xs-6 col-sm-6 pricing-box">
				<div class="widget-box widget-color-green2" >
					<div class="widget-header search_emp">
							<h5 class="widget-title bigger lighter">Salary</h5>
					</div>

					<div class="widget-body">
                        <table class="table table-bordered ">
                            <tr>
                                <th>Status</th>
                                <th>Employee</th>
                                <th>Salary Amount</th>
                                <th>OT Amount</th>
                                <th>Total Payable</th>
                            </tr>
                            @php $salary_pay = 0; $ot_pay = 0; @endphp
                            @foreach($salaryInfo as $key => $sal)
                                <tr>
                                    @php 
                                        $salary_pay += $sal->total_payable - ceil($sal->ot_payable);
                                        $ot_pay += ceil($sal->ot_payable);
                                    @endphp
                                    <td class="text-bold text-capitalize">{{ emp_status_name($sal->emp_status) }}</td>
                                    <td style="text-align: center;">{{$sal->emp}}</td>
                                    <td class="text-right pr-2">{{ bn_money($sal->total_payable - ceil($sal->ot_payable))}}</td>
                                    <td class="text-right pr-2">{{ bn_money(ceil($sal->ot_payable))}}</td>
                                    <td class="text-right pr-2">{{ bn_money($sal->total_payable)}}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th colspan="2">Total</th>
                                <th class="text-right pr-2">{{ bn_money($salary_pay) }}</th>
                                <th class="text-right pr-2">{{ bn_money($ot_pay) }}</th>
                                <th class="text-right pr-2" style="font-size: 20px;">{{ bn_money($salary->total_payable) }}</th>
                            </tr>
                        </table>
						{{-- <div class="widget-main">
							<a href="#" class="search_emp" data-salstatus='salary'>
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> Salary Payable  </div>

                                    <div class="profile-info-value">
                                        <span>{{ bn_money($salary->salary_payable) }} </span>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="search_emp" data-salstatus='ot'>
                                <div class="profile-info-row">
                                    <div class="profile-info-name"> OT Payable </div>

                                    <div class="profile-info-value">
                                        <span>{{ bn_money($salary->ot_payable) }} </span>
                                    </div>
                                </div>
                            </a>
                            <a href="#" class="search_emp" >
                                <div class="profile-info-row row-sum">
                                    <div class="profile-info-name"> Total Payable</div>

                                    <div class="profile-info-value">
                                        <span class="infobox-data-number">{{ bn_money($salary->total_payable)}} </span>
                                    </div>
                                </div>
                            </a>


						</div> --}}
					</div>
				</div>
			</div>
			
        </div>
    </div>
</div>
<div id="printOutputSection" style="display: none;"></div>
<script type="text/javascript">
function printDiv(pagetitle) {
    $.ajax({
        url: '{{url('hr/search/hr_salary_search_print_page')}}',
        type: 'post',
        data: {
        	"_token": "{{ csrf_token() }}",
            type: 'Total',
            title: pagetitle,
            ot: '{{$salary->ot_payable}}',
            emp: '{{$salary->employee}}',
            salary: '{{$salary->salary_payable}}',
            total: '{{$salary->total_payable}}'
        },
        success: function(data) {
            $('#printOutputSection').html(data);
            var divToPrint=document.getElementById('printOutputSection');
            var newWin=window.open('','Print-Window');
            newWin.document.open();
            newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
            newWin.document.close();
            setTimeout(function(){newWin.close();},10);
        }
    });
}
</script>
