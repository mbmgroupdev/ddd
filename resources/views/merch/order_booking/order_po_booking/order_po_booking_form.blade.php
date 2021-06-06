@extends('merch.layout')
@section('title', 'Order Booking')

@section('main-content')
@push('css')
    <style type="text/css">
		td input[type=text], input[type=number], .text-custom-style {
		    color: #000;
		    font-weight: bold;
		    text-align: center;
		    border: navajowhite;
		    cursor: default;
		}
		.addBackground::after{
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-color: #31708f;
            opacity: .4;
        }
        span.label {
        	width: 100%;
        	height: 100%;
        }
        span.select2 {
		    width: 100% !important;
		}
		.sticky-th{
			background: #dedede !important;
			padding: 5px !important;
		}
    </style>
@endpush

<div class="main-content">
    <div class="main-content-inner">

        <div class="breadcrumbs ace-save-state" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="#"> Order Booking </a>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        @include('inc/message')
        <form class="form-horizontal" role="form" id="orderPoBookingForm" method="post" action="{{ url('merch/order_po_booking/store') }}">
        	@csrf
        	<div class="row">
        		<div class="col-sm-3 pr-0">
        			<div class="panel" id="orderSection">
			        	<div class="panel-heading d-flex justify-content-between">
			        		<h6 >Filter</h6>
			        		<div style="position: relative;">
								<input id="editOrderModeBtn" type="checkbox" style="top: 4px;position: absolute;left: -15px;">
								<span class="lbl middle"></span>
		                        <label for="editOrderModeBtn"> Order Mode </label>
		                    </div>
			        	</div>
			            <div class="panel-body" style="height: 270px;">

		                	<div class="form-group has-float-label select-search-group has-required" id="bookingList" style="display: none;">
	                            {{ Form::select("booking_id", $poBookingList, null, ['class'=>'form-control', 'id'=>'booking_id', 'placeholder'=>'Select Booking']) }}
			                        <label for="booking_id"> Booking List </label>
		                    </div>
		                    <div class="disabled-input-section">
		                        <div class="form-group has-float-label has-required select-search-group">
		                            {{ Form::select("unit_id", $unitList, null, ['class'=>'form-control disabled-input', 'id'=>'unit_id', 'placeholder'=>'Select Unit']) }}
		                            <label for="unit_id"> Unit </label>
		                        </div>
		                        <div class="form-group has-float-label has-required select-search-group">

		                             {{ Form::select("mr_buyer_b_id", $buyerOrderList, null, ['class'=>'form-control disabled-input', 'id'=>'buyer_id', 'placeholder'=>'Select Buyer']) }}
		                            <label for="buyer_id"> Buyer </label>

		                        </div>
		                        <div class="form-group has-float-label has-required select-search-group">
		                            {{ Form::select("mr_supplier_sup_id", $supplierList, null, ['class'=>'form-control disabled-input', 'id'=>'mr_supplier_sup_id', 'placeholder'=>'Select Supplier']) }}
			                        <label for="mr_supplier_sup_id"> Supplier </label>
		                        </div>
		                        <div class="form-group has-float-label has-required">
	                                <input type="date" name="delivery_date" id="delivery_date" class="form-control datepicker disabled-input" placeholder="Shipment Date" data-validation='required'/>
		                            <label for="delivery_date"> Shipment Date </label>
		                        </div>
		                        <div class="form-group has-float-label has-required select-search-group" id="orderList" style="display: none;">
									{{ Form::select("order_id", $orderList, null, ['class'=>'form-control disabled-input', 'id'=>'order_id', 'placeholder'=>'Select Order']) }}
		                            <label for="order_id"> Order List </label>

		                        </div>
		                    </div>

		                </div>
		            </div>

        		</div>
        		<div class="col-sm-9">
        			<div class="panel" id="orderSection">
			        	<div class="panel-heading">
			        		<h6 >Supplier Wise Qty</h6>
			        	</div>
			            <div class="panel-body pt-0" style="height: 270px;overflow-y: scroll;">
	                        <table class="table table-bordered" id="">
	                            <thead>
	                                <tr>
	                                    <th class="sticky-th">Order No</th>
	                                    <th class="sticky-th">Supplier/Item</th>
	                                    <th class="sticky-th">Required Quantity</th>
	                                    <th class="sticky-th">Booking Quantity</th>
	                                    <th class="sticky-th">Percentage</th>
	                                    <th class="sticky-th">Delivery Date</th>
	                                    <th class="sticky-th"></th>
	                                </tr>
	                            </thead>
	                            <tbody id="order_list2"></tbody>
	                        </table>


			            </div>
			        </div>

        		</div>
        	</div>




	        <div class="panel panel-info" id="aaa">
	            <div class="panel-heading page-headline-bar"><h6> BOM</h6> </div>
	            <div class="panel-body p-4">
	                <!-- 2nd ROW -->
	                <div class="row">
	                    <!-- ORDERS -->
	                    <div class="col-sm-12 table-responsive">
	                        <table class="table table-bordered table-striped" id="boms_table">
	                            <thead>
	                                <tr>
										<th>Order No</th>
										<th>PO No</th>
										<th>Main Category</th>
										<th>Item</th>
										<th>Description</th>
										<th>Article</th>
										<th>Supplier</th>
										<th>Uom</th>
										<th>Unit Price</th>
										<th>Color</th>
										<th>Size</th>
										<th>Qty</th>
										<th>Req Qty</th>
										<th>Total Value</th>
										 <th>Booking Qty</th>
									</tr>
	                            </thead>
	                            {{-- fabric category --}}
	                            <tbody id="supplier_order_item_list"></tbody>
	                        </table>
	                    </div>
						<div class="col-sm-offset-3 col-sm-9" id="submit_btn" style="display: none;">
	                    <br>
		                    <!-- SUBMIT -->
		                    {!! Custom::btn('Save & Generate') !!}
	                    </div>
	                </div>
	            </div>
	        </div>

        </form>

    </div>
</div>

@push('js')
	<script type="text/javascript">

	$(document).ready(function(){
	  var order = "@isset($_GET['order']){{$_GET['order']}}@endisset";
	  var unit = "@isset($_GET['unit']){{$_GET['unit']}}@endisset";
	  var buyer = "@isset($_GET['buyer']){{$_GET['buyer']}}@endisset";
	  if(buyer != '' && unit != '' && order != ''){

	  	$('#unit_id').val(unit);
	  	$('#buyer_id').val(buyer);

	  	$('#editOrderModeBtn').prop( "checked", true );
	  	$('#orderList').hide().fadeIn('slow').show();
		$('#mr_supplier_sup_id').hide().fadeIn('slow').attr('disabled','disabled');
	    $('#submit_btn').hide();

	  	$.ajax({
			url: '<?= url('merch/order_po_booking/getSupOrderList'); ?>',
			type: 'get',
			data: {
				buyer_id: buyer,
				unit_id: unit,
				order_id: order
			},
			success: function(res){
				$('#order_id').html(res.orderList);
				$('#order_id').val(order);
				$('#order_list2').hide().fadeIn('slow').html(res.result);
				// $('#order_id').hide().fadeIn('slow').html(res.orderList);
				$('#orderSection').removeClass('addBackground');
			},
			error: function() {
				console.log('error occured.');
				$('#orderSection').removeClass('addBackground');
			}
		});


	  }
	});





		var order_list = [];
		$('#buyer_id, #unit_id, #mr_supplier_sup_id, #order_id').on('change', function() {
			var buyerId = $('#buyer_id').val();
			var unitId = $('#unit_id').val();
			var orderId = $('#order_id').val();
			var supId = $('#mr_supplier_sup_id').val();
			var currentAttrId = $(this).attr('id');
			if(currentAttrId == 'buyer_id') {
				orderId = null;
			}
			if(buyerId != '' && unitId != '') {
				// order div empty
			    $("#order_list2").html('');
			    $("#supplier_order_item_list").html('');
			    // button hide
			    $('#submit_btn').hide();
			    // empty orderList var
			    order_list = [];
			    var supplierSelect2 = $('#mr_supplier_sup_id');
				$.ajax({
					url: '<?= url('merch/order_po_booking/getSupOrderList'); ?>',
					type: 'get',
					data: {
						buyer_id: buyerId,
						unit_id: unitId,
						sup_id: supId,
						order_id: orderId
					},
					success: function(res){
						if(currentAttrId == 'buyer_id') {
							$('#order_id').html(res.orderList);
						}
						$('#order_list2').hide().fadeIn('slow').html(res.result);
						// $('#order_id').hide().fadeIn('slow').html(res.orderList);
						$('#orderSection').removeClass('addBackground');
					},
					error: function() {
						console.log('error occured.');
						$('#orderSection').removeClass('addBackground');
					}
				});
			}
		});

		$(document).on('click','.supplier_order_checkbox',function() {
			var order_id = $(this).data('oid');
			var supplier_id = $(this).data('supid');
			var data_order = $(this).data('order');
			if($(this).is(":checked")) {
				if(data_order) {
					supplier_id = null;
				}
				$('#orderSection').addClass('addBackground');
				order_list.push(order_id+'_'+supplier_id);
				if(order_list.length == 1) {
					$('#submit_btn').show();
				}
				$.ajax({
					url: '<?= url('merch/order_po_booking/getPoOrderItem'); ?>',
					type: 'get',
					data: {
						order_id: order_id,
						supplier_id: supplier_id
					},
					success: function(res){
						$('#supplier_order_item_list').hide().fadeIn('slow').append(res);
						$('.qty_rm_class').each(function(i,v){
							var qtyValue = $(this).val();
							if(qtyValue == 0) {
								var classN = $(this).data('rm');
								// $('.'+classN).siblings('span').remove();
								// $('.'+classN).siblings('hr').remove();
								// $('.'+classN).remove();
							}
						});
						$('#orderSection').removeClass('addBackground');
					},
					error: function() {
						console.log('error occured.');
						$('#orderSection').removeClass('addBackground');
					}
				});
			} else {
				$('.tr_order_id_'+order_id).remove();
				order_list = jQuery.grep(order_list, function(value) {
					  return value != order_id+'_'+supplier_id;
					});
				if(order_list.length < 1) {
					$('#submit_btn').hide();
				}
			}
		});

		$(document).on('click','#editOrderModeBtn',function() {

			if($(this).is(":checked")) {
				$('#orderList').hide().fadeIn('slow').show();
				$('#mr_supplier_sup_id').hide().fadeIn('slow').attr('disabled','disabled');
				// reset select/input value
			    $("#mr_supplier_sup_id").val($("#mr_supplier_sup_id option:first").val()).trigger('change');
			    // order div empty
			    $("#order_list2").html('');
			    $("#supplier_order_item_list").html('');
			    // submit btn hide
			    $('#submit_btn').hide();
			} else {
				$("#order_id").val($("#order_id option:first").val()).trigger('change');

				$('#mr_supplier_sup_id').removeAttr('disabled','disabled');
				// reset select/input value
			    $("#mr_supplier_sup_id").val($("#mr_supplier_sup_id option:first").val()).trigger('change');
			    $('#orderList').hide();
			}

		});

		$(document).on('click','#editModeBtn',function() {
			if($(this).is(":checked")) {
				$('#bookingList').hide().fadeIn('slow').show();
				$('.disabled-input').hide().fadeIn('slow').attr('disabled','disabled');
				// reset select/input value
				$("#booking_id").val($("#booking_id option:first").val()).trigger('change');
			    $("#mr_supplier_sup_id").val($("#mr_supplier_sup_id option:first").val()).trigger('change');
			    $("#unit_id").val($("#unit_id option:first").val()).trigger('change');
			    $("#delivery_date").val('');
			    // order div empty
			    $("#order_list2").html('');
			    $("#supplier_order_item_list").html('');
			    // submit btn hide
			    $('#submit_btn').hide();
			    // empty order array
			    order_list = [];
				console.log('edit mode');
			} else {

				$('.disabled-input').removeAttr('disabled','disabled');
				// reset select/input value
			    $("#booking_id").val($("#booking_id option:first").val()).trigger('change');
			    $("#mr_supplier_sup_id").val($("#mr_supplier_sup_id option:first").val()).trigger('change');
			    $("#unit_id").val($("#unit_id option:first").val()).trigger('change');
			    $("#delivery_date").val('');
				$('#bookingList').hide();
				console.log('insert mode');
			}
		});

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
