@extends('hr.layout')
@section('title', 'Add Role')
@section('main-content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-home home-icon"></i>
					<a href="#"> Human Resource </a>
				</li> 
				<li>
					<a href="#"> Payroll </a>
				</li>
				<li class="active"> Arear Salary Disburse </li>
			</ul><!-- /.breadcrumb --> 
		</div>

		<div class="page-content"> 
            <div class="page-header">
				<h1>Payroll <small><i class="ace-icon fa fa-angle-double-right"></i> Arear Salary Disburse </small></h1>
            </div>
            <div class="row">
            	<!-- Display Erro/Success Message -->
		            	@include('inc/message')

            	<div class="col-sm-8 no-padding no-margin">
            		<div class="panel panel-info" style="height: 250px !important; " >
            			<div class="panel-heading"><h5>To be paid</h5></div>
            			<div class="panel-body" style="height: 200px !important; overflow-y: scroll;">
            				<input type="hidden" name="total_months" id="total_months" value="{{sizeof($arrear_data)}}">
            				<table id="dataTables2" class="table table-striped " style="width: 100% !important;">
		                        <tbody>
		                            @if(isset($arrear_data))
	                                    <tr>
	                                    	<th>Associate ID</th>
	                                    	@foreach($arrear_data as $arr)
                                    			<td>{{$arr->associate_id}}</td>
                                    			@break
                                    		@endforeach
	                                    </tr>

	                                    <tr>
	                                    	<th>Details</th>
	                                    	<td>
	                                    	@foreach($arrear_data as $arr)
	                                    		<span style="color: black; font-weight: bold;">Name:</span> &nbsp{{ $arr->as_name }}<br>
                                                <span style="color: black; font-weight: bold;">Unit:</span> &nbsp{{ $arr->hr_unit_name}}<br>
                                                <span style="color: black; font-weight: bold;">Dept:</span> &nbsp{{ $arr->hr_department_name}}<br>
                                                <span style="color: black; font-weight: bold;">Cell:</span> &nbsp{{ $arr->as_contact}}<br>
                                                @break
	                                    	@endforeach
                                            </td>
	                                    </tr>
	                                    <tr>
	                                    	<th>Amount</th>
	                                    	<td>
	                                    	@foreach($arrear_data as $arr)
	                                            {{ $arr->amount }}
		                                    	<?php 
	                                                $monthNum  = $arr->month;
	                                                $dateObj   = DateTime::createFromFormat('!m', $monthNum);
	                                                $monthName = $dateObj->format('F'); // March
	                                            ?>
	                                            ( {{ $monthName }}, {{ $arr->year }}- 
		                                            @if($arr->status == 0)
	                                                    <span style="color: red; font-weight: bold;">Not given</span>
	                                                @else
	                                                    <span style="color: green; font-weight: bold;">Given</span>
	                                                @endif
                                                )

	                                            <br>
	                                    	@endforeach
	                                    	</td>
	                                    </tr>
	                                    <tr hidden="hidden">
	                                    	<th>Amount</th>
	                                    	<td>
	                                    	<?php $total_amount = 0; $not_given_months=0;?>
	                                    	@foreach($arrear_data as $arr)
	                                                {{ $arr->amount }}
	                                            <?php $total_amount += $arr->amount; ?>
	                                            @if($arr->status == 0)
                                                    <?php $not_given_months++;?>
                                                    <span style="color: red; font-weight: bold;">Not given</span><br>
                                                @else
                                                    <span style="color: green; font-weight: bold;">Given</span><br>
                                                @endif
	                                    	@endforeach
	                                    	</td>
	                                    </tr>
	                                    <tr>
	                                    	<th>Total</th>
	                                    	<td>
	                                            {{$total_amount}}
	                                        </td>
	                                    </tr>

		                            @endif                            
		                        </tbody>
		                    </table>
            			</div>
            		</div>
            	</div>
            	<div class="col-sm-4 no-padding no-margin">
            		<div class="panel panel-success" style="height: 250px !important;">
            			<div class="panel-heading">
            				<h5>Disburse
	            				<div class="text-right pull-right">
	            					<a href="{{url('hr/payroll/increment')}}" class="btn btn-success btn-xx" rel='tooltip' data-tooltip-location='left' data-tooltip="Go to Increment Entry/List Page" style="border-radius: 2px;">
	            						<i class="fa fa-mail-reply"></i></a>
	            				</div>
	            			</h5>
            		</div>
            			<div class="panel-body">
            				{{Form::open(['url'=> 'hr/payroll/arear_salary_disburse/save', 'class'=>'form-horizontal'])}}
            					<div class="form-group" style="margin-top: 50px;">
            						<label class="col-sm-4">No. of Month/s</label>
            						<div class="col-sm-8">
            							<input class="col-xs-12" type="text" name="no_of_month" id="no_of_month" data-validation="required-number" placeholder="Enter Number" >
            							
            							<input type="hidden" name="not_given_months" id="not_given_months" value="{{$not_given_months}}">
            							<input type="hidden" name="total_month" id="total_month" value="{{sizeof($arrear_data)}}">
            							<input type="hidden" name="ass_id" id="ass_id" value="{{ isset($arrear_data[0]->associate_id)?$arrear_data[0]->associate_id:'' }}">
            						</div>
            					</div>
            					<div class="form-group">
            						<label class="col-sm-4"></label>
            						<div class="col-sm-8">
            							<button type="submit" id="save_button" class="btn btn-sm btn-success" style="border-radius: 2px;">Save</button>
            						</div>
            					</div>
            				{{Form::close()}}
            			</div>
            		</div>
            	</div>
            </div>
            
            <?php
				class BanglaConverter {
				    public static $bn = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
				    public static $en = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

				    public static $bn_al = array("এ", "বি", "সি", "ডি", "ই", "এফ", "জি", "এইচ", "আই", "জে","কে","এল",	"এম",	"এন",	"ও",	"পি",	"কিউ",	"আর",	"এস",	"টি",	"ইউ",	"ভি",	"ডব্লু",	"এক্স",	"ওয়াই",	"জেড",	
														);
				    public static $en_al_cap = array("A", "B", "C", "D", "E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
				    public static $en_al_sm = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");

				    public static $month_number = array("1","2","3","4","5","6","7","8","9","10","11","12");
				    public static $month_name_bn = array("জানুয়ারী","ফেব্রুয়ারী","মার্চ","এপ্রিল","মে","জুন","জুলাই","অগাস্ট","সেপ্টেম্বর","অক্টোবর","নভেম্বর","ডিসেম্বর");
				    
				    public static function bn2en($number) {
				        return str_replace(self::$bn, self::$en, $number);
				    }
				    
				    public static function en2bn($number) {
				        return str_replace(self::$en, self::$bn, $number);
				    }

				    public static function en2bn_alph($str) {
				        return str_replace(self::$en_al_cap, self::$bn_al, $str);
				    }

				    public static function en2bn_month($month_num) {
				        return str_replace(self::$month_number, self::$month_name_bn, $month_num);
				    }
				}
			?>

            <input type="hidden" name="voucher_ready" id="voucher_ready" value="">
            
            <div class="row" style="margin-top: 20px;" id="voucher_div">
            	<div class="panel panel-info">
            		<div class="panel-heading">
        				<h5>Voucher
            				<div class="text-right pull-right">
            					<button class="btn btn-primary btn-xx printVoucher" rel='tooltip' data-tooltip-location='top' data-tooltip="Print" style="border-radius: 2px;"><i class="glyphicon  glyphicon-print"></i> Print</button>
            				</div>
        				</h5>
            		</div>
            		<div class="panel-body">
            			<div class="col-sm-12 print_div" style="border:1px solid grey; " id="print_div" >
	            			<h1 style="text-align: center; color: forestgreen;">এমবিএম গার্মেন্টস লিমিটেড</h1>
	            			<h5 style="text-align: center; ">এম-১৯, এম-১৪, সেকশন-১৪, মিরপুর, ঢাকা</h5>
	            			<h5 class="pull-right" style="margin-left: 80%;">তারিখঃ<?php
	            				echo BanglaConverter::en2bn(date('d-m-Y'));

	            			?></h5>


	            			<div style="margin-left: 40px; margin-top: 46px;">
		            			<h5 style="margin-left: 10%;">বর্ধিত বেতন পাওনার হিসাব-</h5>

		            			<table style="border: none; margin-left: 10%; width: 70%; font-size: 11px;">
		            				<tbody>
			            				<tr>
			            					<th style="padding:2px; text-align: left; ">নামঃ</th>
			            					<th style="padding:2px; text-align: left;"> {{$arrear_data[0]->hr_bn_associate_name}} <br></th>
			            				</tr>
			            				<tr>
			            					<th style="padding:2px; text-align: left; ">পদবীঃ</th>
			            					<td style="padding:2px;"> {{$arrear_data[0]->hr_designation_name_bn}} <br></td>
			            				</tr>
			            				<tr>
			            					<th style="padding:2px; text-align: left; ">ডিপার্টমেন্টঃ</th>
			            					<td style="padding:2px;"> {{$arrear_data[0]->hr_department_name_bn}} <br></td>
			            				</tr>
			            				<tr>
			            					<th style="padding:2px; text-align: left; ">আইডি নংঃ</th>
			            					<td style="padding:2px;">{{$arrear_data[0]->associate_id}} ( <?php echo BanglaConverter::en2bn(BanglaConverter::en2bn_alph($arrear_data[0]->associate_id)) ?> )<br></td>
			            				</tr>
		            				</tbody>
		            			</table>
		            			<div style="margin:0px; padding:0px;" id="hide2">
			            			<h5 style="margin-top: 10px; margin-left: 10%; text-decoration: underline;">প্রদত্ত বেতন এর পরিমানঃ</h5>
			            			<table style="border: 1px solid darkgrey; margin-left: 10%; width: 70%; border-collapse: collapse; font-size: 11px; ">
			            				<thead>
			            					<tr style="border: 1px solid darkgrey; padding: 5px;">
			            						<th style="border: 1px solid darkgrey; padding: 5px;  text-align: left; width: 50%;">মাসের নাম</th>
			            						<th style="border: 1px solid darkgrey; padding: 5px;  text-align: left; width: 50%;">টাকার পরিমান (প্রতিমাসের বেতন)</th>
			            					</tr>
			            				</thead>
			            				<tbody>
											<?php $total_pay = 0;?>
			            					@foreach($arrear_data as $arr)
												@if($arr->status == 1)
					            					<tr style="border: 1px solid darkgrey; padding: 5px;">
					            						<td style="border: 1px solid darkgrey; padding: 5px;">
															<?php echo BanglaConverter::en2bn_month($arr->month) ?>, 
				                                            <?php echo BanglaConverter::en2bn($arr->year) ?> <br>
					            						</td>
					            						<td style="border: 1px solid darkgrey; padding: 5px;">
			                                                <?php echo BanglaConverter::en2bn($arr->amount) ?>
			                                                <?php $total_pay+=$arr->amount; ?>
					            						</td>
					            					</tr>
			            						@endif
			                                @endforeach

			                                <tr style="border: 1px solid darkgrey; padding: 5px;">
			                                	<th style="border: 1px solid darkgrey; padding: 5px; text-align: center; color: maroon;">মোট</th>
			                                	<th style="border: 1px solid darkgrey; padding: 5px; text-align: left; color: maroon;"><?php echo BanglaConverter::en2bn($total_pay) ?></th>
			                                </tr>
			            				</tbody>
			            			</table>
		            			</div>

		            			<div style="margin:0px; padding:0px;" id="hide">
			            			<h5 style="margin-top: 10px; margin-left: 10%;text-decoration: underline; ">বকেয়া প্রদত্ত বেতন এর পরিমানঃ</h5>
			            			<table style="border: 1px solid darkgrey; margin-left: 10%; width: 70%; border-collapse: collapse; font-size: 11px;">
			            				<thead>
			            					<tr style="border: 1px solid darkgrey; padding: 5px;">
			            						<th style="border: 1px solid darkgrey; padding: 5px;  text-align: left; width: 50%;">মাসের নাম</th>
			            						<th style="border: 1px solid darkgrey; padding: 5px;  text-align: left; width: 50%;">টাকার পরিমান</th>
			            					</tr>
			            				</thead>
			            				<tbody>
											<?php $total_pay = 0;?>
			            					@foreach($arrear_data as $arr)
												@if($arr->status == 0)
					            					<tr style="border: 1px solid darkgrey; padding: 5px;">
					            						<td style="border: 1px solid darkgrey; padding: 5px;">
															<?php echo BanglaConverter::en2bn_month($arr->month) ?>, 
				                                            <?php echo BanglaConverter::en2bn($arr->year) ?> <br>
					            						</td>
					            						<td style="border: 1px solid darkgrey; padding: 5px;">
			                                                <?php echo BanglaConverter::en2bn($arr->amount) ?>
			                                                <?php $total_pay+=$arr->amount; ?>
					            						</td>
					            					</tr>
			            						@endif
			                                @endforeach

			                                <tr style="border: 1px solid darkgrey; padding: 5px;">
			                                	<th style="border: 1px solid darkgrey; padding: 5px; text-align: center; color: maroon;">মোট</th>
			                                	<th style="border: 1px solid darkgrey; padding: 5px; text-align: left; color: maroon;"><?php echo BanglaConverter::en2bn($total_pay) ?></th>
			                                </tr>
			            				</tbody>
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

<script type="text/javascript">
	$(document).ready(function(){
		if($('#not_given_months').val() == 0){
			$('#save_button').attr('disabled', 'disabled');
			$('#hide').attr('hidden', 'hidden');
		}
		else{
			$('#hide').removeAttr('hidden');	
		}

		if($('#not_given_months').val() == $('#total_month').val()){
			$('#hide2').attr('hidden', 'hidden');
		}
		else{
			$('#hide2').removeAttr('hidden');	
		}

		$('#no_of_month').on('keyup', function(){
			if($(this).val()<0){
				$('#no_of_month').val(1);
			}

			if($('#not_given_months').val() < $(this).val()){
				$('#no_of_month').val($('#not_given_months').val());
			}
		});

		$('body').on('click', '#save_button',function(){

			var num_of_month = $('#no_of_month').val();
			var ass_id = $('#ass_id').val();

			$('#voucher_ready').val('1');
			
			console.log(num_of_month+"  "+ass_id);
		
			// $.ajax({
				
			// 	url: '{{ url("hr/payroll/arear_salary_disburse/save") }}',
			// 	type: 'get',
			// 	data: { num_of_month: num_of_month, ass_id: ass_id },
			// 	dataType: 'json',
			// 	success: function(data){
			// 		console.log(data);
			// 	},
			// 	error: function(data){
			// 		alert('Failed..');
			// 	},
			// });

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
@endsection