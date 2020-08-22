@extends('hr.layout')
@section('title', 'Maternity Payment')
@section('main-content')
@section('content')
<div class="main-content">
  <div class="main-content-inner">
	    <div class="breadcrumbs ace-save-state" id="breadcrumbs">
	      <ul class="breadcrumb">
	        <li>
	          <i class="ace-icon fa fa-home home-icon"></i>
	          <a href="#"> Human Resource </a>
	        </li>
	        <li>
	          <a href="#"> Operations </a>
	        </li>
	        <li class="active"> Maternity Payment</li>
	      </ul><!-- /.breadcrumb -->
	    </div>
	    <div class="page-content">
	      <div class="col-sm-12">
		      <div class="panel panel-success">
		      	<div class="panel-heading">
		      		<h5>Maternity Payment</h5>
		      	</div>
		      	<div class="panel-body">
		      		@include('inc/message')
			        {{Form::open(['url'=>'#', 'class'=>'form-horizontal'])}}
		      		<div class="col-sm-7">
			      			<div class="form-group">
			      				<label class="col-sm-4">Unit <span style="color: red;"> *</span></label>
			      				<div class="col-sm-8">
			      					<select class="col-xs-12" id="unit_id">
			      						<option value="">Select Unit</option>
			      						@foreach($units as $key => $val)
			      							<option value="{{$key}}">{{$val}}</option>
			      						@endforeach
			      					</select>
			      				</div>
			      			</div>

			      			<div class="form-group">
			      				<label class="col-sm-4">Approval Status <span style="color: red;"> *</span></label>
			      				<div class="col-sm-8">
			      					<select class="col-xs-12" id="approval_status">
			      						<option value="">Select Unit First</option>
			      					</select>
			      				</div>
			      			</div>

			      			<div class="form-group">
			      				<label class="col-sm-4">Employee <span style="color: red;"> *</span></label>
			      				<div class="col-sm-8">
			      					<select class="col-xs-12" id="emp_associate_id">
			      						<option value="">Select Unit and Approval Status First</option>
			      					</select>
			      				</div>
			      			</div>
			      	</div>
			      	<div class="col-sm-5">
			      		<div class="form-group">
			      			<label class="col-sm-4">Duration:</label>
			      			<div class="col-sm-8">
			      				<div class="col-sm-5 no-padding no-margin">
			      					<input class="col-xs-12" type="text"  readonly="readonly" name="" id="view_from" value="" placeholder="Y-m-d">
			      				</div>
			      				<div class="col-sm-2 no-padding no-margin">
			      					<label class="col-xs-12 no-padding-right">To</label>
			      				</div>
			      				<div class="col-sm-5 no-padding no-margin">
			      					<input class="col-xs-12" type="text"  readonly="readonly" name="" id="view_to" value="" placeholder="Y-m-d">
			      				</div>

			      				<input type="hidden" name="month_duration_hiden" id="month_duration_hiden" value="">
			      				<input type="hidden" name="current_sal_hidden" id="current_sal_hidden" value="">
			      			</div>
			      		</div>

			      		<div class="form-group">
			      			<label class="col-sm-4">Current Salary:</label>
			      			<div class="col-sm-8">
			      				<input class="col-xs-12" type="text"  readonly="readonly" name="" id="view_current_sal" value="" placeholder="Current Salary">
			      			</div>
			      		</div>

			      		<div class="form-group">
			      			<label class="col-sm-4">Total Payble:</label>
			      			<div class="col-sm-8">
			      				<input class="col-xs-12" type="text"  readonly="readonly" name="" id="view_total" value="" placeholder="Total Payble Amount">
			      			</div>
			      		</div>

			      		<div class="form-group">
			      			<label class="col-sm-4"></label>
			      			<div class="col-sm-8" hidden="hidden" id="pay_button_div">
			      				<div class="col-sm-6 no-padding no-margin">
			      					<button  type="button" class="btn btn-sm btn-success" id="pay_button" style="border-radius: 2px;" value="">Pay</button>
			      				</div>
			      				<div class="col-sm-6 no-padding no-margin" id="loader" style="display:none;">
	                                <center><i class="ace-icon fa fa-spinner fa-spin orange bigger-300"></i></center>
	                            </div>
			      			</div>
			      		</div>	
			      	</div>
			      	{{Form::close()}}
		      	</div>
		      </div>

		      

		      <div class="panel panel-info" id="voucher_div" style="display:none;">
		      	<div class="panel-heading">
		      		<h5>
		      			Voucher
		      			<button class="btn btn-primary btn-xx pull-right printVoucher" rel='tooltip' data-tooltip-location='top' data-tooltip="Print"  style="border-radius: 2px;"><i class="glyphicon  glyphicon-print"></i> Print</button>
		      		</h5>
		      	</div>
		      	<div class="panel-body">
		      		<div class="col-sm-12 print_div" style="border:1px solid grey; " id="print_div" >
            			<h1 style="text-align: center; color: forestgreen;" id="unit_print"></h1>
            			<h5 style="text-align: center; " id="unit_addr_print"></h5>
            			<h5 class="pull-right" style="margin-left: 80%;">তারিখঃ<?php
            				echo eng_to_bn(date('d-m-Y'));

            			?></h5>


            			<div style="margin-left: 40px; margin-top: 60px;">
        				<h3 style=" margin-left: 70%;
								    color: red;
								    border: 3px solid red;
								    border-radius: 5px;
								    max-height: 50px;
								    max-width: 73px;
								    font-size: 28px !important;
								    font-weight: 800 !important;
								    font-style: italic;" id="paid_mark">PAID</h3>
            				{{-- <img src="{{url('assets/images/logo/paid.png')}}" style="margin-left: 70%; display: none;" id="paid_mark" height="50px;" width="90px;"/> --}}

	            			<h5 style="margin-left: 10%;">মাতৃত্তকালীন বেতন পাওনার হিসাব-</h5>

	            			<table style="border: none; margin-left: 10%; width: 60%; font-size: 11px;">
	            				<tbody>
		            				<tr>
		            					<th style="padding:2px; text-align: left; width: 30%;">নামঃ</th>
		            					<th style="padding:2px; text-align: left; width: 60%;" id="emp_name_print">  <br></th>
		            				</tr>
		            				<tr>
		            					<th style="padding:2px; text-align: left; width: 30%;">পদবীঃ</th>
		            					<td style="padding:2px; width: 60%;" id="emp_deg_print">  <br></td>
		            				</tr>
		            				<tr>
		            					<th style="padding:2px; text-align: left; width: 30%;">ডিপার্টমেন্টঃ</th>
		            					<td style="padding:2px; width: 60%;" id="emp_dep_print">  <br></td>
		            				</tr>
		            				<tr>
		            					<th style="padding:2px; text-align: left; width: 30%;">সেকশনঃ</th>
		            					<td style="padding:2px; width: 60%;" id="emp_sec_print">  <br></td>
		            				</tr>
		            				<tr>
		            					<th style="padding:2px; text-align: left; width: 30%;">সাব-সেকশনঃ</th>
		            					<td style="padding:2px; width: 60%;" id="emp_sub_sec_print">  <br></td>
		            				</tr>
		            				<tr>
		            					<th style="padding:2px; text-align: left; width: 30%;">আইডি নংঃ</th>
		            					<td style="padding:2px; width: 60%;" id="emp_ass_id_print">  <br></td>
		            				</tr>
		            				<tr>
		            					<th style="padding:2px; text-align: left; width: 30%;">মূল বেতনঃ</th>
		            					<td style="padding:2px; width: 60%;" id="emp_basic_sal_print">  <br></td>
		            				</tr>
		            				<tr>
		            					<th style="padding:2px; text-align: left; width: 30%;">মঞ্জূরীত ছুটীর সময়কালঃ</th>
		            					<td style="padding:2px; width: 60%;" id="emp_mt_leave_dur_print">  <br></td>
		            				</tr>
	            				</tbody>
	            			</table>
		            		<div style="margin:0px; padding:0px;" id="hide2">
		            			<h5 style="margin-top: 10px; margin-left: 10%; text-decoration: underline;">প্রদেয় বেতন এর পরিমানঃ</h5>
		            			<table style="border: 1px solid darkgrey; margin-left: 10%; width: 60%; border-collapse: collapse; font-size: 11px; ">
		            				<thead>
		            					<tr style="border: 1px solid darkgrey; padding: 5px;">
		            						<th style="border: 1px solid darkgrey; padding: 5px;  text-align: left; width: 40%;  padding-left: 30px;">মাসের নাম</th>
		            						<th style="border: 1px solid darkgrey; padding: 5px;  text-align: left;  padding-left: 30px;">টাকার পরিমান (মাসিক বেতন)</th>
		            					</tr>
		            				</thead>
		            				<tbody id="the_payble_body_print"></tbody>
		            			</table>
	            			</div>

	            			<table style="  width: 100%; margin-top: 20%; margin-bottom: 40px; font-size: 10px;">
	            					<tr style=" padding: 5px;">
	            						<td style=" padding: 4px;">
	            							প্রস্তুতকারী
	            						</td>
	            						<td style=" padding: 4px;">
	            							হিসাব বিভাগ
	            						</td>
	            						<td style=" padding: 4px;">
	            							সহঃ ব্যবস্থাপক <br> প্রশাসন, মানবসম্পদ ও কমপ্লাইন্স
	            						</td>
	            						<td style=" padding: 4px;">
	            							সহঃ মহাব্যবস্থাপক <br> প্রশাসন, মানবসম্পদ ও কমপ্লাইন্স
	            						</td>
	            						<td style=" padding: 4px;">
	            							এভিপি <br> প্রশাসন, মানবসম্পদ ও কমপ্লাইন্স
	            						</td>
	            					</tr>
	            			</table>
	            		</div>
	            	</div>

		      	</div>
		      </div>
	      </div>
	    </div>
	</div>
</div>
@push('js')
<script type="text/javascript">
	var as_pic = '';
	var as_name = '';
	function getEmpAsPic(id) {
        $.ajax({
            url: '{{url('users_management/get_emp_as_pic')}}',
            type: 'get',
            data: { as_id: id },
            success: function(res){
            	as_pic = res.as_pic;
            	as_name = res.as_name;
            },
            error: function(err){
            	// return err;
            }
        });
    }

    function format(state) {
		// console.log(state);
		// var as_pic_s = '';
		var employee = getEmpAsPic(state.id);
		console.log(employee,as_pic);
		// as_pic_s = as_pic;
		// as_name_s = as_name;
        if (!state.id) return state.text; // optgroup
        return "<img src='" + as_pic + "' height='50px' width='auto'/> - " + state.text;
    }

	$(document).ready(function(){


	    // $("#emp_associate_id").select2({
		   //  allowClear: true,
		   //  templateResult: format,
		   //  templateSelection: format,
		   //  escapeMarkup: function (m) {
		   //  	return m;
		  	// }
	    // });

		$('#maternity_list_table').dataTable();

		$('#unit_id').on('change', function(){
			$('#view_from').val('');
			$('#view_to').val('');
			$('#view_total').val('');
			$('#view_current_sal').val('');

			$('#pay_button_div').attr('hidden', true);
			$('#voucher_div').hide();
			$('#paid_mark').hide();

			$('#emp_associate_id').html("<option value=\"\">Select Unit and Approval Status First</option>");
			var status = "<option value=\"\">Select Status</option>"+
						 "<option value=\"0\">Pending</option>"+
						 "<option value=\"1\">Approved</option>";
			$('#approval_status').html(status);
		});

		$('#approval_status').on('change', function(){
			$('#view_from').val('');
			$('#view_to').val('');
			$('#view_total').val('');
			$('#view_current_sal').val('');

			var approval_status = $(this).val();
			var unit_id = $('#unit_id').val();
			// console.log(unit_id);
			$('#pay_button_div').attr('hidden', true);
			$('#voucher_div').hide();
			$('#paid_mark').hide();

			$.ajax({
				url: '{{url('hr/operation/get_maternity_employees')}}',
				type: 'get',
				dataType: 'json',
				data: { unit_id: unit_id, approval_status: approval_status },
				success: function(data){
					// console.log(data);
					if(data.length == 0){
						var list = "<option value=\"\"><span style='color:red !important;'>No Data Found</span></option>";	
					}
					else{
						var list = "<option value=\"\">Select Employee</option>";
						for(var i=0; i<data.length; i++){
								list += "<option value=\""+data[i].as_id+"\">"+data[i].leave_ass_id+" - "+data[i].as_name+"</option>";
						}
					}
					$('#emp_associate_id').html(list);
				},
				error: function(data){
					console.log(data);
				},
			})
		});


		$('#emp_associate_id').on('change', function(){
			$('#view_from').val('');
			$('#view_to').val('');
			$('#view_total').val('');
			$('#view_current_sal').val('');

			$('#voucher_div').hide();
			$('#paid_mark').hide();

			var emp_id = $(this).val();

			$.ajax({
				url: '{{url('hr/operation/get_maternity_employee_details')}}',
				type: 'get',
				dataType: 'json',
				data: {emp_id: emp_id},
				success: function(data){
					// console.log(data);
					//pay button show hide
					if( $('#approval_status').val() == 1){
						$('#pay_button_div').attr('hidden', false);
					}
					else{
						$('#pay_button_div').attr('hidden', true);	
					}

					$('#view_from').val(data.leave_from);
					$('#view_to').val(data.leave_to);
					$('#view_total').val(data.total_payable);
					$('#view_current_sal').val(data.current_salary);

					//printing values assign..
					$('#unit_print').text(data.hr_unit_name_bn);
					$('#unit_addr_print').text(data.hr_unit_address_bn);
					$('#emp_name_print').text(data.hr_bn_associate_name);
					$('#emp_deg_print').text(data.hr_designation_name_bn);
					$('#emp_dep_print').text(data.hr_department_name_bn);
					$('#emp_ass_id_print').text(data.leave_ass_id);
					$('#emp_sec_print').text(data.hr_section_name_bn);
					$('#emp_sub_sec_print').text(data.hr_subsec_name_bn);
					$('#emp_basic_sal_print').text(banglaDigit(data.basic_sal)+ " ৳");
					$('#emp_mt_leave_dur_print').text(banglaDigit(data.month_duration)+ " মাস (" +banglaMonthOfDigit(data.from_month)+", "+banglaDigit(data.from_Y)+" হইতে "+banglaMonthOfDigit(data.to_month)+", "+banglaDigit(data.to_Y)+")");
					// $('#').text(data.);
					// var m = banglaDigit(data.from_month);
 				// 	console.log(m);

 					var rows = "";
 					var fm  = data.from_month;
 					var tm  = data.to_month;
 					var fY  = data.from_Y;
 					var tY  = data.to_Y;

 					for(var i=0; i<data.month_duration; i++){
 						rows += "<tr style=\"border: 1px solid darkgrey; padding: 5px;\">"+
            						"<td style=\"border: 1px solid darkgrey; padding: 5px; padding-left: 30px;\">"+
										banglaMonthOfDigit(fm)+","+ 
                                        banglaDigit(fY)+"<br>"+
            						"</td>"+
            						"<td style=\"border: 1px solid darkgrey; padding: 5px;  padding-left: 30px;\">"+
                                        banglaDigit(data.current_salary)+
            						" ৳</td>"+
            					"</tr>";
    					fm++;
    					if(fm > 12) {
    						fm = 1;
    						fY++;
    					}
 					}
					rows += "<tr style=\"border: 1px solid darkgrey; padding: 5px;\">"+
			                	"<th style=\"border: 1px solid darkgrey; padding: 5px; text-align: right; color: maroon;\">মোট</th>"+
			                	"<th style=\"border: 1px solid darkgrey; padding: 5px; text-align: left; color: maroon;  padding-left: 30px;\">"+
			                		banglaDigit(data.total_payable)+
			                	" ৳</th>"+
			                "</tr>";

			        $('#the_payble_body_print').html(rows);
			        $('#pay_button').val(data.leave_ass_id);
			        $('#month_duration_hiden').val(data.month_duration);
			        $('#current_sal_hidden').val(data.current_salary);


			        //If already given...
			        if(data.maternity_salary_given_status == 1){
						$('#pay_button_div').attr('hidden', true);
						$('#voucher_div').show();
						$('#paid_mark').show();
						// alert('Already Given');	
					}
				},
				error: function(data){

				},
			});
		});

		//function that will return a number in bengali
		function banglaDigit(digit){
			var bn_digit = "";
			str_digit = new String(digit);
			// console.log(str_digit.length);
			for(var i=0; i<str_digit.length; i++){
				if(str_digit[i] == "0"){bn_digit += "০";}
				else if(str_digit[i] == "1"){bn_digit += "১";}
				else if(str_digit[i] == "2"){bn_digit += "২";}
				else if(str_digit[i] == "3"){bn_digit += "৩";}
				else if(str_digit[i] == "4"){bn_digit += "৪";}
				else if(str_digit[i] == "5"){bn_digit += "৫";}
				else if(str_digit[i] == "6"){bn_digit += "৬";}
				else if(str_digit[i] == "7"){bn_digit += "৭";}
				else if(str_digit[i] == "8"){bn_digit += "৮";}
				else {bn_digit += "৯";}
			}
			return bn_digit;
		}

		//bangla monthname return using int value
		function banglaMonthOfDigit(num){
				if(num == 1){ return "জানুয়ারী";}
				else if(num == 2){ return "ফেব্রুয়ারী";}
				else if(num == 3){ return "মার্চ";}
				else if(num == 4){ return "এপ্রিল";}
				else if(num == 5){ return "মে";}
				else if(num == 6){ return "জুন";}
				else if(num == 7){ return "জুলাই";}
				else if(num == 8){ return "অগাস্ট";}
				else if(num == 9){ return "সেপ্টেম্বর";}
				else if(num == 10){ return "অক্টোবর";}
				else if(num == 11){ return "নভেম্বর";}
				else { return "ডিসেম্বর";}
		}


		$('#pay_button').on('click', function(){
			var emp_ass  = $(this).val();
			// console.log(emp_ass);
			var from 	 = $('#view_from').val();
			var to 		 = $('#view_to').val();
			var duration = $('#month_duration_hiden').val();
			var amount 	 = $('#current_sal_hidden').val();

			$('#loader').show();
			$.ajax({
				url: '{{url('hr/operation/save_maternity_salary_disburse')}}',
				type: 'get',
				dataType: 'json',
				data: { emp_ass: emp_ass, from: from, to: to, duration: duration, amount: amount  },
				success: function(data){
					console.log(data);
					if(data == 1){
						$('#loader').hide();
						$('#voucher_div').show();
						$('#pay_button_div').attr('hidden', true);	
					}
				},
				error: function(data){
					console.log(data);
				},
			});


		});

	});

	//For printing the voucher
	$(function(){
	    $('body').on('click', '.printVoucher', function(){
	        setTimeout(function(){
	            // $('#printDiv')
	            // var divToPrint = document.getElementById("print_div").innerHTML;
	            var divToPrint = $(".print_div")[0].innerHTML;
	            // console.log(divToPrint);
	            var newWin=window.open('','Print-Window');
	            newWin.document.open();
	            newWin.document.write('<html><body onload="window.print()">'+divToPrint+'</body></html>');
	            newWin.document.close();
	            setTimeout(function(){newWin.close();},10);
	        },500);
	    });
	});

</script>
@endpush
@endsection
