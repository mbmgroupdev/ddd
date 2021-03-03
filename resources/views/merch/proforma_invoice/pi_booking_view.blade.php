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
           
        
                <!-- 1st ROW -->
                <div class="row">
                    <div class="col-sm-12">
                    	<div class="panel panel-info">
			                <div class="panel-heading page-headline-bar"><h5> Proforma Invoice</h5> </div>
                            <div class="panel-body">
                                <div class="col-sm-4">
                                    <div class="profile-user-info">
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> PI No </div>
                                            <div class="profile-info-value">
                                                <span> {{$piMaster->pi_no}} </span>
                                            </div>
                                        </div>
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> PI Qty </div>
                                            <div class="profile-info-value">
                                                <span> {{$piMaster->total_pi_qty}} </span>
                                            </div>
                                        </div>
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> Booking No : </div>
                                            <div class="profile-info-value">
                                                <span> {!!$poText!!}  </span>
                                            </div>
                                        </div>
                                    </div>  
                                </div>
                                <div class="col-sm-4">
                                    <div class="profile-user-info">
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> Unit</div>
                                            <div class="profile-info-value">
                                                <span> {{$unit}} </span>
                                            </div>
                                        </div>
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> Buyer </div>
                                            <div class="profile-info-value">
                                                <span> {{$buyer}} </span>
                                            </div>
                                        </div>
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> Supplier </div>
                                            <div class="profile-info-value">
                                                <span> {{$supplier}} </span>
                                            </div>
                                        </div>
                                    </div>  
                                </div>
                                <div class="col-sm-4">
                                    <div class="profile-user-info">
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> Category </div>
                                            <div class="profile-info-value">
                                                <span> {{$piMaster->pi_category}} </span>
                                            </div>
                                        </div>
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> Ship Mode</div>
                                            <div class="profile-info-value">
                                                <span> {{$piMaster->ship_mode}} </span>
                                            </div>
                                        </div>
                                        <div class="profile-info-row">
                                            <div class="profile-info-name"> Status</div>
                                            <div class="profile-info-value">
                                                <span> {{$piMaster->pi_status}} </span>
                                            </div>
                                        </div>
                                    </div>  
                                </div>
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
		                            <tfoot class="sum-tfoot" style="text-align: right;">
		                            	<tr class="tr_visible">
			                               <td colspan="12" style="text-align: right;">Total Fabric</td>
			                               <td id="fab_total_booking_qty">0</td>
			                               <td id="fab_total_pi_qty">0</td>
			                               <td id="fab_total_pi_value">0</td>
			                               <td></td>
			                            </tr>
		                            	<tr class="tr_visible" style="text-align: right;"> 
			                               <td colspan="12" style="text-align: right;">Total Sewing Accessories</td>
			                               <td id="sw_total_booking_qty">0</td>
			                               <td id="sw_total_pi_qty">0</td>
			                               <td id="sw_total_pi_value">0</td>
			                               <td></td>
			                            </tr>
		                            	<tr class="tr_visible" style="text-align: right;">
			                               <td colspan="12" style="text-align: right;">Total Finishing Accessories</td>
			                               <td id="fin_total_booking_qty">0</td>
			                               <td id="fin_total_pi_qty">0</td>
			                               <td id="fin_total_pi_value">0</td>
			                               <td></td>
			                            </tr>

			                            <tr class="tr_visible" style="text-align: right;">
			                               <td colspan="12" style="text-align: right;">Grand Total</td>
			                               <td id="grand_total_booking_qty">0</td>
			                               <td id="grand_total_pi_qty">0</td>
			                               <td id="grand_total_pi_value">0 </td>
			                               <td>
                                               <input type="hidden" id="total-pi-qty" name="total_pi_qty" value="0">
                                               <input type="hidden" id="total-pi-value" name="total-pi-value" value="0">
                                           </td>
			                            </tr>
		                            </tfoot>
		                        </table>
		                    </div>
		                </div>
		            </div>
		        </div>
        </div><!-- /.page-content -->
    </div>
</div>
@push('js')
<script type="text/javascript">
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
</script>
@endpush

@endsection
