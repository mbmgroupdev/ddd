@extends('merch.index')
@push('css')
<style>
	/*.has-validation-callback{overflow-x: hidden;}*/
	.text-right{display: inline;}
	th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        width: 100%;
        margin: 0 auto;
    }
    div.DTFC_LeftWrapper table.dataTable, div.DTFC_RightWrapper table.dataTable {
    margin-bottom: 0 !important;}
    .DTFC_LeftBodyLiner table{margin: 0 !important}
    #example_filter{display: none;}
    #example_info{display: none;}
    .DTFC_LeftBodyLiner{max-height: 282px !important;}
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.2.6/css/fixedColumns.dataTables.min.css">
@endpush
@section('content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-usd home-icon"></i>
					<a href="#">Order PO Costing</a>
				</li>
				<li class="active">Order PO Costing Edit</li>
			</ul><!-- /.breadcrumb -->
		</div>

		<div class="page-content  table-responsive">
            <!-- Display Erro/Success Message -->
            @include('inc/message')
            {{ Form::open(['url'=>('merch/orders/po_costing/store'), 'class'=>'row']) }}
            <input type="hidden" name="tmp_po_id" id="tmp_po_id" value="{{$po_id}}">

            <div class="panel panel-success">
            	<div class="panel-heading"><h6>Order PO Costing Edit

					<div class="text-right pull-right">
						<a href='{{ url("merch/orders/po_bom_list") }}' class="btn btn-xs btn-info btn-round" rel='tooltip' data-tooltip-location='left' data-tooltip="PO BOM List"><i class="glyphicon glyphicon-th-list"></i></a>

	                	{{-- <a href="{{ url('/merch/order_costing') }}" rel='tooltip' data-tooltip-location='left' data-tooltip='Order Costing list' type="button" class="btn btn-info btn-xx">
						<i class="glyphicon  glyphicon-list"></i>	Order Costing list
						</a> --}}
						{{-- <button type="button" rel='tooltip' data-tooltip-location='bottom' data-tooltip='Print' type="button" class="btn btn-success btn-xx" onClick="printMe('print_img_div','printMe1', 'printMe2','order_code')" style="margin-left: 10px;">
						<i class="glyphicon  glyphicon-print"></i> Print
						</button> --}}
		            </div>
					</h6>
				</div>

				<div class="panel-body" id="PrintArea">
					<div class="panel panel-warning">
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-10">
									<table class="table" width="50%" cellpadding="0" cellspacing="0" border="0">
										<tr>
											<th>Order No</th>
											<td>{{ (!empty($order->order_code)?$order->order_code:null) }}</td>
											<th>PO NO</th>
											<td>{{ (!empty($po_no)?$po_no:null) }}</td>
											<th>Color</th>
											<td>{{ (!empty($color_name)?$color_name:null) }}</td>
										</tr>
										<tr>
											<th>Unit</th>
											<td>{{ (!empty($order->hr_unit_name)?$order->hr_unit_name:null) }}</td>
											<th>Buyer</th>
											<td>{{ (!empty($order->b_name)?$order->b_name:null) }}</td>
											<!-- <th>Brand</th>
											<td>{{ (!empty($order->br_name)?$order->br_name:null) }}</td> -->
											<th>Order Quantity</th>
											<td>{{ (!empty($order->order_qty)?$order->order_qty:null) }}</td>
										</tr>
										<tr>
											<th>Season</th>
											<td>{{ (!empty($order->se_name)?$order->se_name:null) }}</td>
											<th>Style No</th>
											<td>{{ (!empty($order->stl_no)?$order->stl_no:null) }}</td>
											
										</tr>
										<tr>
											<th>Delivery Date</th>
											<td>{{ (!empty($order->order_delivery_date)?$order->order_delivery_date:null) }}</td>
											<th>Reference No</th>
											<td>{{ (!empty($order->order_ref_no)?$order->order_ref_no:null) }}</td>
										</tr>
									</table>
								</div>
								<div class="col-sm-2">
									<a href="{{ asset(!empty($order->stl_img_link)?$order->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" target="_blank">
										<img class="thumbnail" height="100px" src="{{ asset(!empty($order->stl_img_link)?$order->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" alt=""/>
									</a>
								</div>
							</div>
						</div>
					</div>

					<div class="panel panel-success" style="margin-bottom: 0">
					    <div class="panel-body">
		                    <table id="example" class="display stripe row-border order-column custom-font-table table table-bordered" style="width:100%">
								<thead>
									<tr class="info">
										<th>Main Category</th>
										<th>Item</th>
										<th>Item Code</th>
										<th>Description</th>
										<th>Color</th>
										<th>Size / Width</th>
										<th>Article</th>
										<th>Composition</th>
										<th>Construction</th>
										<th>Supplier</th>
										<th>Consumption</th>
										<th>Extra (%)</th>
										<th>Unit</th>
										<th>Terms</th>
										<th>FOB</th>
										<th>L/C</th>
										<th>Freight</th>
										<th>Unit Price</th>
										<th>Total Price</th>
										<th>Style Cost</th>
										<th>Req. Qty</th>
										<th>Total Value</th>
									</tr>
		                        </thead>
								<tbody>
									{!! (!empty($bomItemData)?$bomItemData:null) !!}
		                        </tbody>
		                    </table>
		                    <br>
		                    <div class="text-right" {{-- style="pointer-events: none;" --}}>
				            	<button type="submit" class="btn btn-info btn-sm btn-round pull-right">Save / Update</button>
				            </div>
		                </div><!-- /.col -->
			        </div>
		        </div>
		    </div>
			{!! Form::close() !!}
            <!-- /.form -->
		</div><!-- /.page-content -->
	</div>
</div>

@push('js')

<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var table = $('#example').DataTable( {
        scrollY:        "300px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        ordering:       false,
        fixedHeader   : true,
        fixedColumns:   {
            leftColumns: 2
        }
    } );
} );
$(document).ready(function(){
	function set_percentage(changeName, totalAmount, finalPlace) {
		var val1 	= $('input[name='+totalAmount+']').val();
		var val2 	= $('input[name='+changeName+']').val();
		var val3 	= (val2*100)/val1;
		$('input[name='+finalPlace+']').val(val3.toFixed(6));
	}

	// set commertial commision percentage
	$('input[name=commercial_commision]').on('change', function() {
		set_percentage('commercial_commision','net_fob','comercial_comision_percent');

	});

	// set buyer commission percentage
	$('input[name=buyer_commision]').on('change', function() {
		set_percentage('buyer_commision','final_fob','buyer_comission_percent');
	});
	/*
	* BOM TERM
	* -----------------------------------------------------
	*/
	$("body").on("click", ".bom_term", function(){
		var term = $(this).attr("value");
		if (term=="FOB")
		{
			$(this).parent().parent().parent().parent().find("input").not(".total_price").not(".required_qty").not(".total_val").prop("readonly", false);
		}
		else
		{
			$(this).parent().parent().parent().parent().find(".fob").prop("readonly", true).val(0);
			$(this).parent().parent().parent().parent().find(".lc").prop("readonly", true).val(0);
			$(this).parent().parent().parent().parent().find(".freight").prop("readonly", true).val(0);
		}
	});

	/*
	* -----------------------------------------------------
	* CALCULATE
	* -----------------------------------------------------
	*/
	// INITIAL TOTAL
	$(window).on("load", function(){
		///$('.input-sm').change();

		$('input[type=text]').css('font-size', '11px');
		// calculate subtotal
		$('.subtotal').val(0); //reset subtotal
		var sew_fin = 0.0;
		$(".total_category_price").each(function(i, v) {
			var cat_id = $(this).data("cat-id");

			var total  = $(this).val();

			// calculate subtotal
			var subtotal = $(this).parent().parent().parent().find('input[data-subtotal="'+cat_id+'"]');

			if(cat_id==2 || cat_id==3){
				// console.log(cat_id, total);
				sew_fin += parseFloat(total);
			}

			if (subtotal.length > 0)
			{

				$(this).parent().parent().parent().find('[data-subtotal="'+cat_id+'"]').val((parseFloat(total)+parseFloat(subtotal.val())).toFixed(6));
			}
		});

		$('#total_sewing_n_finishing_price').val(sew_fin);

		// calculate total and net fob price
		calculateFOB();
		// onload set percentage
		set_percentage('commercial_commision','net_fob','comercial_comision_percent');
		set_percentage('buyer_commision','final_fob');
	});

	// CATEGORY PRICE CALCULATION
	$(document).on("keyup change blur", ".fob, .lc, .freight, .unit_price", function(){
		var fob = $(this).parent().parent().find(".fob").val();
		var lc = $(this).parent().parent().find(".lc").val();
		var freight = $(this).parent().parent().find(".freight").val();
		var consumption = $(this).parent().parent().find(".consumption").text();
		var extraCon = $(this).parent().parent().find(".extra").text();

		//Sewing Finishing Total Showing.......
		sew = parseFloat($('#total_sewing').val());
		fin = parseFloat($('#total_finishing').val());
		if(isNaN(sew)  || sew == ""){
			sew = 0.0;
		}
		if(isNaN(fin) || fin == ""){
			fin = 0.0;
		}
		var sew_fin_total = sew + fin;

		// console.log(sew, fin);
		$('#total_sewing_n_finishing_price').val(sew_fin_total);

		// calculate unit price
		// if enabled fob then fob, lc and freight values
		if ($(this).parent().parent().find(".fob").is("[readonly]"))
		{
			var sp_price = $(this).val();
	        var unit_price = sp_price;
			/*var sp_price = $(this).val();
			if($(this).val().indexOf('.')!=-1){
				this.value = parseFloat(this.value);
	            if($(this).val().split(".")[1].length > 4){
	                if( isNaN( parseFloat( this.value ) ) ) return;
	                this.value = parseFloat(this.value).toFixed(6);
	                sp_price = this.value;
	                $(this).val(sp_price);
	            }
	        } else {
	        	// if dot(.) not found
	    		// only 4 digit get
			    if(isNumber(sp_price) && sp_price.length>4) {
				   	sp_price = sp_price.substring(0,4);
				   	$(this).val(sp_price);
				} else {
			 		sp_price = this.value;
				}
	        }
	        var unit_price = sp_price;*/
			// var unit_price = parseFloat($(this).parent().parent().find(".unit_price").val()).toFixed(6);
		}
		else
		{
			var unit_price = parseFloat(parseFloat(fob)+parseFloat(lc)+parseFloat(freight)).toFixed(6);
			// set unit price
			$(this).parent().parent().find(".unit_price").val(unit_price);
		}

		unit_price = isNaN(unit_price) ? '0.00' : unit_price;

		var comsumptionPer = parseFloat((parseFloat(consumption) * parseFloat(extraCon)) / 100).toFixed(6);
		var comsumptionEx = parseFloat(consumption) + parseFloat(comsumptionPer);
		var total_category_price = parseFloat(parseFloat(unit_price)*parseFloat(comsumptionEx)).toFixed(6);
		//required qty and total value

		var required_qty= $(this).parent().parent().find(".required_qty").val();
		var total_val= parseFloat(parseFloat(unit_price)*parseFloat(required_qty)).toFixed(6);
		//set total value
		$(this).parent().parent().find(".total_val").val(total_val);


		// set total price
		$(this).parent().parent().find(".total_category_price").val(total_category_price);

		// calculate subtotal
		$(this).parent().parent().parent().find('.subtotal').val(0); //reset subtotal
		$(".total_category_price").each(function(i, v) {
			var cat_id = $(this).data("cat-id");
			var total  = $(this).val();
			// calculate subtotal
			var subtotal = $(this).parent().parent().parent().find('input[data-subtotal="'+cat_id+'"]');
			if (subtotal.length > 0)
			{
				$(this).parent().parent().parent().find('[data-subtotal="'+cat_id+'"]').val(parseFloat(total)+parseFloat(subtotal.val()));
			}
		});

		// calculate total and net fob price
		calculateFOB();
	});

	// TOTAL PRICE
	$(document).on("keyup change blur", ".total_price", function(){
		// calculate total and net fob price
		calculateFOB();
	});

	// SPECIAL PRICE
	$(document).on("keyup change blur", ".sp_price, .sp_total_price", function(){
		var sp_price = parseFloat($(this).val()).toFixed(6);
		// remove mutiple dot(.) and set 0 first
		/*if($(this).val().indexOf('.')!=-1){
			this.value = parseFloat(this.value);
            if($(this).val().split(".")[1].length > 4){
                if( isNaN( parseFloat( this.value ) ) ) return;
                this.value = parseFloat(this.value).toFixed(6);
                sp_price = this.value;
            }
        } else {
        	// if dot(.) not found
    		// only 4 digit get
		    if(isNumber(sp_price) && sp_price.length>4) {
			   	sp_price = sp_price.substring(0,4);
		    	$(this).val(sp_price);
			} else {
		 		sp_price = this.value;
			}
        }*/
		$(this).parent().parent().find(".sp_total_price").val(sp_price);
		// calculate total and net fob price
		calculateFOB();
	});

	function isNumber (o) {
	  return ! isNaN (o-0);
	}

	//Buyer commision
	$(document).on('change keyup blur','.buyer_comission_percent',function(){

		calculateFOB()
	});

	//Agent commision
	$(document).on('change keyup blur','.agent_comission_percent',function(){

		calculateFOB()
	});

	// calculate total and net fob price
	function calculateFOB()
	{
		var net_style_total = 0;
		var net_fob = 0;
		var total_fob = 0;
		var buyer_total = $(".buyer_total_price").val();
		var agent_total = $(".agent_total_price").val();
		$(".total_price").each(function(i, v) {
			net_fob = parseFloat(parseFloat(net_fob)+parseFloat($(this).val())).toFixed(6);
		});
		$(".net_fob").val(net_fob);


		// buyer_comission_percent
		var buyerComissionValue = $(".buyer_comission_percent").val();
		var net_fob = $(".net_fob").val();
		var buyerPercent = parseFloat((net_fob * buyerComissionValue)/100).toFixed(6);
		$(".buyer_price").val(buyerPercent);
		$(".buyer_total_price").val(buyerPercent);

		//buyer fob
		var buyerPercent = $(".buyer_total_price").val();
		var buyerFob = parseFloat(parseFloat(net_fob) + parseFloat(buyerPercent)).toFixed(6);
		$(".buyer_fob").val(buyerFob);

		// agent_comission_percent
		var agentComissionValue = $(".agent_comission_percent").val();
		var buyer_fob = $(".buyer_fob").val();
		var agentPercent = parseFloat((buyer_fob * agentComissionValue)/100).toFixed(6);
		$(".agent_price").val(agentPercent);
		$(".agent_total_price").val(agentPercent);

		//agent fob
		var agentPercent = $(".agent_total_price").val();
		var agentFob = parseFloat(parseFloat(buyerFob) + parseFloat(agentPercent)).toFixed(6);
		$(".agent_fob").val(agentFob);

		$(".total_fob").val(parseFloat(parseFloat(net_fob)+parseFloat(buyer_total)+parseFloat(agent_total)).toFixed(6));
	}

	// calculate total and net fob price
	// function calculateFOB()
	// {
	// 	var net_fob = 0;
	// 	var total_fob = 0;
	// 	var buyer_total = $(".buyer_total_price").val();
	// 	$(".total_price").each(function(i, v) {
	// 		net_fob = parseFloat(parseFloat(net_fob)+parseFloat($(this).val())).toFixed(6);
	// 	});
	// 	$(".net_fob").val(net_fob);

	// 	$(".total_fob").val(parseFloat(parseFloat(net_fob)+parseFloat(buyer_total)).toFixed(6));
	// }

	/*
	* -----------------------------------------------------
	* ENDS CALCULATE
	* -----------------------------------------------------
	*/

	/*
	* UNIT PRICE VALIDATION FOR 0/NULL
	* -------------------------------------------
	*/

	 $('body').on('click', '#form_submit, #request', function(e) {

	  // UNIT PRICE
	 	var alert_check_unitprice= false;
		$('.unit_price').each(function(){
			var unitPrice = parseFloat($(this).val());

			if(unitPrice == null || unitPrice == ""){
				$(this).val(0);
			}

			if(unitPrice < 0){
				alert_check_unitprice = true;
				return;
	        }
		});
		if(alert_check_unitprice == true){
		  	alert("Invalid Unit Price");
		  		e.preventDefault();

		}



    });

});


function printMe(print_image_div,elem_1, elem_2,order_code){
	var style_sheet = '' +
        '<style type="text/css">' +
        '@page {'+
		    'size: A4 landscape;'+
		'}'+
		'input, select {'+
			'border: none;'+
			'font-size: 11px;'+
			'width: 60px;'+
		'}'+
        'img{ '+
			'width: 240px;'+
			'height: 320px;'+
		'}'+
		'table{ '+
			'width: 100%;'+
			'border-collapse: collapse;'+
			'font-size: 11px;'+
			// 'display: block'+
		'}'+
        'table th {' +
        'border:1px solid #808080;' +
        'padding:0.5em;' +
        'padding-top: 15px;' +
        'padding-bottom: 15px;' +
        'margin:0px;'+
        '}' +
        'table td {' +
        'border:1px solid #808080;' +
        'padding:0.5em;' +
        'padding-top: 15px;' +
        'padding-bottom: 15px;' +
        'margin:0px;'+
        '}' +
        'h2 {' +
        	'text-align:center; page-break-before: always; border: 1px solid lightgrey; border-radius:8px; padding:0.2em; margin-bottom: 30px;'
        '}' +
        '</style>';

    var mywindow = window.open('', 'PRINT', 'height=900,width=1200');
    mywindow.document.write('<html><head><title>' + 'Order Costing'  + '</title>');
    mywindow.document.write('</head><body>');

   	mywindow.document.write('<h1 style=" margin-top:30px; border: 1px solid green; border-radius:8px; padding:0.4em;">&nbsp Order Costing <small style="color: red;">('+document.getElementById(order_code).innerHTML+')</small> </h1><br>');

    mywindow.document.write('<div style="width: 100%;"><div style="text-align:center;">');
    mywindow.document.write(document.getElementById(print_image_div).innerHTML+' </div></div>');
    mywindow.document.write('<h3 style="margin-top: 100px;">Basic Information </h3><div style="width:70%">');
    mywindow.document.write(document.getElementById(elem_1).innerHTML+'</div>');

	mywindow.document.write('<h2>Costing Information <small style="color: red;">( Order: '+document.getElementById(order_code).innerHTML+')</small> </h2>');
    mywindow.document.write(document.getElementById(elem_2).innerHTML);

    mywindow.document.write('</body>'+style_sheet+'</html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    mywindow.close();

    // location.reload();

	}
</script>
@endpush
@endsection
