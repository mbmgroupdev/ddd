@extends('merch.index')
@section('content')
@push('css')
    <style type="text/css">
		td input[type=text], input[type=number], .text-custom-style {
		    color: #000;
		    font-weight: bold;
		    text-align: center;
		    border: navajowhite;
		    cursor: default;
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
                <li>
                    Order Booking Confirm
                    <?php //dump($poBooking);?>
                </li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <!-- Display Erro/Success Message -->
            @include('inc/message')
            <div class="panel panel-info">
                <div class="panel-heading page-headline-bar"><h5> Order Booking Confirm Form</h5> </div>
                <div class="panel-body" style="padding-bottom: 20px; padding-top: 20px;">
		            <form class="form-horizontal" role="form" method="post" action="{{ url('merch/order_po_booking/confirm_store/'.$poBooking->id) }}">
		                {{ csrf_field() }}
		                <div class="row">
							<div class="col-sm-12 table-responsive">
								<table class="table detailTable" width="50%" cellpadding="0" cellspacing="0" border="0">
									<tbody>
										<tr>
											<th width="20%">Buyer Name</th>
											<td>{{ isset($poBooking->buyer->b_name)?$poBooking->buyer->b_name:'' }}</td>
											<th class="text-center">Total Remaining Qty</th>
											<td class="totalRqty">0</td>
										</tr>
										<tr>
											<th>Supplier Name</th>
											<td>{{ isset($poBooking->getSupplierInfo->sup_name)?$poBooking->getSupplierInfo->sup_name:'' }}</td>
											<th class="text-center">Total Value</th>
											<td class="totalValueTd">0</td>
										</tr>
										<tr>
											<th>Order's</th>
											<td>{{ $poBooking->getOrderList($poBooking->id) }}</td>
											<th class="text-center">Reference No.</th>
											<td>{{ $poBooking->booking_ref_no }}</td>
										</tr>
										<tr>
											<th width="20%">Delivery Date</th>
											<td>{{ isset($poBooking->delivery_date)?$poBooking->delivery_date:'' }}</td>
											<th class="text-center"></th>
											<td></td>
										</tr>
									</tbody>
								</table>
		                    </div>
		                </div>


		                <div class="row">
		                    <!-- ORDERS -->
		                    <div class="col-sm-12 table-responsive">
		                        <table class="table table-bordered table-striped" id="boms_table">
		                            <thead>
		                                <tr>
											<th>Order</th>
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
											<th>Required Qty</th>
											<th>Remaining Qty</th>
											<th>Booking Qty</th>
											<th>Total Value</th>
										</tr>
		                            </thead>
		                            {{-- fabric category --}}
		                            <input type="hidden" name="mr_po_booking_id" value="{{$poBooking->id}}">
		                            <tbody id="supplier_order_item_list">
		                            	{!! $tableData !!}
										<tr>
											<td colspan="14" style="text-align: right; font-weight: bold;">Total Value</td>
											<td class="totalValueTd"  style="text-align: center; font-weight: bold;"></td>
										</tr>
		                            </tbody>
		                        </table>
		                    </div>
							<div class="col-sm-offset-3 col-sm-9" id="submit_btn">
								<br>
			                    <!-- SUBMIT -->
			                    {!! Custom::btn('Save',false) !!}
		                    </div>
		                </div>

		            </form>
        		</div>
        	</div>
        </div><!-- /.page-content -->
    </div>
</div>

@push('js')
	<script type="text/javascript">
		$(document).ready(function(){
			$('.boqtychange').tooltip({
		        placement: "left",
		        trigger: "focus"
		    });
			var rQty = 0;
			$(".req_qty").each(function() {
				rQty += parseFloat($(this).val());
			});
			$('.totalRqty').text(rQty.toFixed(6));
			$('.qty_rm_class').each(function(i,v){
                var qtyValue = $(this).val();
                if(qtyValue == 0) {
                    var classN = $(this).data('rm');
                    // $('.'+classN).siblings('span').remove();
                    // $('.'+classN).siblings('hr').remove();
                    // $('.'+classN).remove();
                }
            });
		});
		$(document).on('keyup','.boqtychange',function(){
			var idA = $(this).attr('id').split('_');
			var value = $(this).val();
			var unitP = $(this).data('uprice');
			console.log(value,unitP);
			if(idA.length > 0){
				$('#tvalue_'+idA[1]).val(parseFloat(value)*parseFloat(unitP));
			}
			calculateTvalue();
		});

		function calculateTvalue(){
			var gTotalC = 0;
			// var decimalPl = countDecimals($(".tvalueC:first").val());
			$(".tvalueC").each(function() {
				// if(countDecimals($(this).val()) > decimalPl) {
				// 	decimalPl = countDecimals($(this).val());
				// }
				// console.log(parseFloat($(this).val()));
				gTotalC += parseFloat($(this).val());
				// console.log(gTotalC)
			});
			$('.totalValueTd').text(parseFloat(gTotalC).toFixed(6));
			// console.log(gTotalC);
		}
		// function countDecimals(value) { 
		//     if ((value % 1) != 0) 
		//         return value.toString().split(".")[1].length;  
		//     return 0;
		// };
		calculateTvalue();
	</script>
@endpush
@endsection
