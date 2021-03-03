@extends('merch.index')
@section('content')
@push('css')
<style type="text/css">
td input[type=text], input[type=number], .text-custom-style {
    color: #000;
    font-weight: bold;
    text-align: center;
        cursor: default;
}
select.input-sm {
    border-radius: 0;
    font-weight: 400;
    margin:0 !important;
    height: 25px!important;
}
input.ship-date {
    border-radius: 0;
    font-weight: 400 !important;
    margin:0 !important;
    height: 25px!important;
    border: 1px solid #d1d1d1 !important;
}
.ship-date{width: 120px!important;}
input[type=number]{border:1px solid #d1d1d1;}
.sum-tfoot{
	text-align: center;
}
.sum-tfoot td{
	font-size: 12px!important; 
	font-weight: bold;
}
</style>
@endpush

<div class="main-content">
    <div class="main-content-inner">
        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Proforma Invoice </a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <!-- Display Erro/Success Message -->
            @include('inc/message')
           
            <form class="form-horizontal" role="form" method="post" action="{{ url('merch/proforma_invoice/update') }}">
                {{ csrf_field() }}
                <input type="hidden" name="pi_id" value={{$pi_id}}>
                <!-- 1st ROW -->
                <div class="row">
                    <div class="col-sm-4">
                    	<div class="panel panel-info">
			                <div class="panel-heading page-headline-bar"><h5> Proforma Invoice</h5> </div>
			                <div class="panel-body" style="padding-bottom: 20px; padding-top: 20px;">
			                	<div class="form-group">
	                                <label class="col-sm-4 control-label no-padding-right"> Unit </label>
	                                <div class="col-sm-8">
	                                    {{ Form::select("unit_id", $unitList, $piMaster->unit_id, ['class'=>'col-xs-12 form-control disabled-input', 'id'=>'unit_id', 'placeholder'=>'Select Unit','disabled' => 'disabled']) }}
	                                </div>
	                            </div>
	                            
	                            <div class="form-group">
	                                <label class="col-sm-4 control-label no-padding-right"> Buyer </label>
	                                <div class="col-sm-8">
	                                    {{ Form::select("mr_buyer_b_id", $buyerList, $piMaster->mr_buyer_b_id, ['class'=>'col-xs-12 form-control disabled-input', 'id'=>'buyer_id', 'placeholder'=>'Select Buyer','disabled' => 'disabled']) }}
	                                </div>
	                            </div>

	                            <div class="form-group">
	                                <label class="col-sm-4 control-label no-padding-right"> Supplier </label>
	                                <div class="col-sm-8">
	                                    {{ Form::select("mr_supplier_sup_id", $supplierList, $piMaster->mr_supplier_sup_id, ['class'=>'col-xs-12 form-control disabled-input', 'id'=>'mr_supplier_sup_id', 'placeholder'=>'Select Supplier','disabled' => 'disabled']) }}
	                                </div>
	                            </div>
	                            
	                            <div class="form-group">
	                                <label class="col-sm-4 control-label no-padding-right" for="pi_no" > PI No<span style="color: red">&#42;</span> </label>
	                                <div class="col-sm-8">
	                                    <input type="text" id="pi_no" name="pi_no" placeholder="PI No" value="{{$piMaster->pi_no}}" class="form-control col-xs-12" data-validation="required length custom" data-validation-length="1-20" readonly="readonly" />
	                                </div>
	                            </div>
      



	                            <div class="form-group">
	                                <label class="col-sm-4 control-label no-padding-right" for="pi_date" > PI Date<span style="color: red">&#42;</span> </label>
	                                <div class="col-sm-8">
	                                    <input type="text" id="pi_date" name="pi_date" placeholder="YYYY-MM-DD" value="{{$piMaster->pi_date}}" class="col-xs-12 datepicker form-control" data-validation="required"/>
	                                </div>
	                            </div>

	                             

	                            <div class="form-group">
	                                <label class="col-sm-4 control-label no-padding-right" for="pi_category" > PI Category<span style="color: red">&#42;</span> </label>
	                                <div class="col-sm-8">
	                                    <select name="pi_category" class="form-control col-xs-12"  data-validation="required">
	                                        <option value="Foreign" @if($piMaster->pi_category=='Foreign') selected="selected"  @endif >Foreign</option>
	                                        <option 
	                                        value="Local" @if($piMaster->pi_category=='Local') selected="selected"  @endif>Local</option>
	                                    </select>
	                                </div>
	                            </div>

	                            <div class="form-group">
	                                <label class="col-sm-4 control-label no-padding-right" for="pi_last_date" > PI Last Date<span style="color: red">&#42;</span> </label>
	                                <div class="col-sm-8">
	                                    <input type="text" id="pi_last_date" name="pi_last_date" placeholder="YYYY-MM-DD" value="{{$piMaster->pi_last_date}}" class="col-xs-12 datepicker form-control" data-validation="required"/>
	                                </div>
	                            </div>

	                            <div class="form-group">
	                                <label class="col-sm-4 control-label no-padding-right" for="ship_mode" > Ship Mode <span style="color: red">&#42;</span> </label>
	                                <div class="col-sm-8">
	                                    <select name="ship_mode" class="form-control col-xs-12" data-validation="required">
	                                        <option value="Sea" @if($piMaster->ship_mode=='Sea') selected="selected"  @endif>Sea</option>
	                                        <option value="Air" @if($piMaster->ship_mode=='Air') selected="selected"  @endif>Air</option>
	                                        <option value="Road" @if($piMaster->ship_mode=='Road') selected="selected"  @endif>Road</option>
	                                    </select>
	                                </div>
	                            </div>

	                            <div class="form-group">
	                                <label class="col-sm-4 control-label no-padding-right" for="pi_status" > PI Status <span style="color: red">&#42;</span> </label>
	                                <div class="col-sm-8">
	                                    <select name="pi_status" class="form-control col-xs-12" data-validation="required">
	                                        <option value="Active" @if($piMaster->pi_status=='Active') selected="selected"  @endif>Active</option>
	                                        <option value="Inactive" @if($piMaster->pi_status=='Inactive') selected="selected"  @endif>Inactive</option>
	                                    </select>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
                    </div>

                    <div class="col-sm-8">
                    	<div class="panel panel-info">
			                <div class="panel-heading page-headline-bar"><h5> Booking List</h5> </div>
			                <div class="panel-body" style="padding-bottom: 20px; padding-top: 20px;">
	                            {{-- Orders --}}
	                            <table class="table table-bordered table-striped" id="">
	                                <thead>
	                                    <tr>
	                                        <th>Booking Reference</th>
	                                        <th>Supplier</th>
	                                        <th>Items</th>
	                                        <th>Total Booking Qty</th>
	                                        <th>Remaining Booking Qty</th>
	                                        <th>Delivery Date</th>
	                                        <th></th>
	                                    </tr>
	                                </thead>
	                                <tbody id="order_list2">{!! $bookingTable !!}</tbody>
	                            </table>
	                        </div>
	                    </div>
                    </div>
                </div>

                <div class="panel panel-info" id="aaa">
	                <div class="panel-heading page-headline-bar"><h5> BOM'S</h5> </div>
	                <div class="panel-body" style="padding-bottom: 20px; padding-top: 20px;">
		                <!-- 2nd ROW -->
		                <div class="row">
		                    <!-- ORDERS -->
		                    <div class="col-sm-12 table-responsive">
		                        <table class="table table-bordered table-striped" id="boms_table" >
		                        	<caption class="center">{!!$forMsg!!}</caption>
		                            <thead>
		                                <tr>
											<th>Booking Reference</th>
											<th>Main Category</th>
											<th>Item</th>
											<th>Article</th>
											<th>Supplier</th>
											<th>Consumption <br>
											& Extra (%)</th>
											<th>Uom</th>
											<th>Unit Price</th>
											<th>New Unit Price</th>
											<th>Color</th>
											<th>Size</th>
		                                    <th>Currency</th>
		                                    <th>Booking Qty</th>
		                                    <th>PI Qty</th>
		                                    <th>PI Value</th>
		                                    <th>Ship Date</th>
										</tr>
		                            </thead>
		                            {{-- fabric category --}}
		                            <tbody id="supplier_order_item_list">
		                            	{!!$itemTable!!}
		                            </tbody>
		                            <tfoot class="sum-tfoot" >
		                            	<tr class="tr_visible">
			                               <td colspan="12" style="text-align: right;">Total Fabric</td>
			                               <td id="fab_total_booking_qty">0</td>
			                               <td id="fab_total_pi_qty">0</td>
			                               <td id="fab_total_pi_value">0</td>
			                               <td></td>
			                            </tr>
		                            	<tr class="tr_visible" > 
			                               <td colspan="12" style="text-align: right;">Total Sewing Accessories</td>
			                               <td id="sw_total_booking_qty">0</td>
			                               <td id="sw_total_pi_qty">0</td>
			                               <td id="sw_total_pi_value">0</td>
			                               <td></td>
			                            </tr>
		                            	<tr class="tr_visible" >
			                               <td colspan="12" style="text-align: right;">Total Finishing Accessories</td>
			                               <td id="fin_total_booking_qty">0</td>
			                               <td id="fin_total_pi_qty">0</td>
			                               <td id="fin_total_pi_value">0</td>
			                               <td></td>
			                            </tr>

			                            <tr class="tr_visible" >
			                               <td colspan="12" style="text-align: right;">Grand Total</td>
			                               <td id="grand_total_booking_qty">0</td>
			                               <td id="grand_total_pi_qty">0</td>
			                               <td id="grand_total_pi_value">0 </td>
			                               <td>
				                               <input type="hidden" id="total-pi-qty" name="total_pi_qty" value="0">
				                               <input type="hidden" id="total-pi-value" name="total_pi_value" value="0">
			                               </td>
			                            </tr>
		                            </tfoot>
		                        </table>
		                    </div>
							<div class="col-sm-offset-3 col-sm-9" id="submit_btn" style="display: none;">
		                    <br>
			                    <!-- SUBMIT -->
			                    {!! Custom::btn('Update') !!}
		                    </div>
		                </div>
		            </div>
		        </div>
            </form>
        		
        </div><!-- /.page-content -->
    </div>
</div>

@push('js')
<script type="text/javascript">
var booking_list = @php echo json_encode($checkedBooking) @endphp;
if(booking_list.length > 0) {
    $('#submit_btn').show();
}
console.log(booking_list);

$(document).on('click','.supplier_order_checkbox',function() {
	var booking_id = $(this).data('bookid');
	var supplier_id = $(this).data('supid');
	var pi_id = $(this).data('pi');
	if($(this).is(":checked")) {
		booking_list.push(booking_id+'_'+supplier_id);
		if(booking_list.length == 1) {
			$('#submit_btn').show();
		}
		$.ajax({
			url: '<?= url('merch/proforma_invoice/getbookingitem'); ?>',
			type: 'get',
			data: {
				booking_id: booking_id,
				supplier_id: supplier_id,
				pi_id: pi_id
			},
			success: function(res){
				$('#supplier_order_item_list').hide().fadeIn('slow').append(res);
				$('.ship-date').datepicker({
					dateFormat: 'yy-mm-dd'
				});
				calculateSubTotal();
			},
			error: function() {
				console.log('error occured.');
			}
		});
	} else {
		$('.tr_booking_id_'+booking_id).remove();
		booking_list = jQuery.grep(booking_list, function(value) {
			  return value != booking_id+'_'+supplier_id;
			});
		calculateSubTotal();
		if(booking_list.length < 1) {
			$('#submit_btn').hide();
		}
	}

});
$( document ).ready(function() {
	calculateSubTotal();
});


function calculateSubTotal(){

    var fab_total_booking_qty= 0;
    var sw_total_booking_qty= 0;
    var fin_total_booking_qty= 0;

    var fab_pi_qty= 0;
    var sw_pi_qty= 0;
    var fin_pi_qty= 0;

    var fab_pi_value= 0;
    var sw_pi_value= 0;
    var fin_pi_value= 0;

    // grand total
    var grand_total_booking_qty = 0;
    var grand_total_pi_qty = 0;
    var grand_total_pi_value = 0;
    //fabric booking total quantity
    $(".fab_booking_qty").each(function(i, v) {
        fab_total_booking_qty = parseFloat(parseFloat(fab_total_booking_qty)+parseFloat($(this).val())).toFixed(6);
    });

    //sewing booking total quantity
    $(".sw_booking_qty").each(function(i, v) {
        sw_total_booking_qty = parseFloat(parseFloat(sw_total_booking_qty)+parseFloat($(this).val())).toFixed(6);
    });

    //finishing booking total quantity
    $(".fin_booking_qty").each(function(i, v) {
        fin_total_booking_qty = parseFloat(parseFloat(fin_total_booking_qty)+parseFloat($(this).val())).toFixed(6);
    });

    //fabric PI total quantity
    $(".fab_pi_qty").each(function(i, v) {
        fab_pi_qty = parseFloat(parseFloat(fab_pi_qty)+parseFloat($(this).val())).toFixed(6);
    });

    //sewing PI total quantity
    $(".sw_pi_qty").each(function(i, v) {
        sw_pi_qty = parseFloat(parseFloat(sw_pi_qty)+parseFloat($(this).val())).toFixed(6);
    });

    //finishing PI total quantity
    $(".fin_pi_qty").each(function(i, v) {
        fin_pi_qty = parseFloat(parseFloat(fin_pi_qty)+parseFloat($(this).val())).toFixed(6);
    });

    //fabric PI total value
    $(".fab_pi_value").each(function(i, v) {
        fab_pi_value = parseFloat(parseFloat(fab_pi_value)+parseFloat($(this).val())).toFixed(6);
    });

    //sewing PI total value
    $(".sw_pi_value").each(function(i, v) {
        sw_pi_value = parseFloat(parseFloat(sw_pi_value)+parseFloat($(this).val())).toFixed(6);
    });

    //finishing PI total value
    $(".fin_pi_value").each(function(i, v) {
        fin_pi_value = parseFloat(parseFloat(fin_pi_value)+parseFloat($(this).val())).toFixed(6);
    });

    //grand
    grand_total_booking_qty= parseFloat(parseFloat(fab_total_booking_qty)+parseFloat(sw_total_booking_qty)+parseFloat(fin_total_booking_qty)).toFixed(6);
    grand_total_pi_qty= parseFloat(parseFloat(fab_pi_qty)+parseFloat(sw_pi_qty)+parseFloat(fin_pi_qty)).toFixed(6);
    grand_total_pi_value= parseFloat(parseFloat(fab_pi_value)+parseFloat(sw_pi_value)+parseFloat(fin_pi_value)).toFixed(6);

    $("#grand_total_booking_qty").text(grand_total_booking_qty);
    $("#grand_total_pi_qty").text(grand_total_pi_qty);
    $("#grand_total_pi_value").text(grand_total_pi_value);

    //sub
    $("#fab_total_booking_qty").text(fab_total_booking_qty);
    $("#sw_total_booking_qty").text(sw_total_booking_qty);
    $("#fin_total_booking_qty").text(fin_total_booking_qty);

    $("#fab_total_pi_qty").text(fab_pi_qty);
    $("#sw_total_pi_qty").text(sw_pi_qty);
    $("#fin_total_pi_qty").text(fin_pi_qty);

    $("#fab_total_pi_value").text(fab_pi_value);
    $("#sw_total_pi_value").text(sw_pi_value);
    $("#fin_total_pi_value").text(fin_pi_value);

    $("#total-pi-qty").val(grand_total_pi_qty);
    $("#total-pi-value").val(grand_total_pi_value);
}

//for on key up  event
$("body").on("keyup", ".sw_pi_qty,.fin_pi_qty,.fab_pi_qty", function(){
    var x=parseFloat($(this).data('cost'));
    var y= parseFloat($(this).val());
    var z= parseFloat((parseFloat(x)*parseFloat(y)).toFixed(6));
    $(this).parent().next().children().val(z);
    calculateSubTotal();
});

$("body").on("keyup", ".new-price", function(){
	    var target = $(this).data('target');
	    var value = parseFloat($(this).val());
	    if(isNaN(value)){
	    	value = 0;
	    }
	    $('.'+target).each(function(i) {
	    	$(this).data('cost',value);
	    	var qty = parseFloat($(this).val());
	    	var cost = parseFloat($(this).data('cost'));
	    	var pivalue = parseFloat((cost*qty).toFixed(6));
	    	$(this).parent().next().children().val(pivalue);
		});
	    calculateSubTotal();
});

//for on mouse wheel event
/*$(".sw_pi_qty,.fin_pi_qty,.fab_pi_qty").on('mousewheel', function() {
	var x=parseFloat($(this).data('cost'));
    var y= parseFloat($(this).val());
    var z= parseFloat(parseFloat(x)*parseFloat(y));
    $(this).parent().next().children().val(z);
    calculateSubTotal();
});
*/


$('#booking_id').on('change', function() {
	if($(this).val()){
		var poBookingId = $(this).val();
		$.ajax({
			url: '<?= url('merch/order_po_booking/getPoOrderInfo'); ?>',
			type: 'get',
			data: {
				po_booking_id: poBookingId
			},
			success: function(res){
				console.log(res);
			},
			error: function() {
				console.log('error occured.');
			}
		});
	}
});
</script>
@endpush
@endsection
