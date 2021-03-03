<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,maximum-scale=1.0">
    <title>Order Costing Print</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<style type="text/css" media="print">
	 	@media print {
	 		@page {size: landscape}
	 		::-webkit-scrollbar {
			    display: none;
			}
		    body {
		        margin: 0;
		        padding: 0;
		        line-height: 1.4em;
		        word-spacing: 1px;
		        letter-spacing: 0.2px;
		        font: 13px Arial, Helvetica,"Lucida Grande", serif;
		        color: #000;
		        zoom:65%;
		    }

		    #print-btn #update-btn #nav-left #nav-bar, #selectUnitContainer, .navbar, .sidebar-nav {
		        display: none;
		    }

		    #print-btn, #update-btn, #units {
		        display: none;
		    }

		    #nav-left {
		        display: none;
		    }

		    #report-container {
		        visibility: visible;
		    }

		    .well .span12{
		        width: 100%;
		        visibility: visible;
		    }

		    .navbar {
		        display: none;
		    }

		    .sidebar-nav {
		        display: none;
		    }
		    .input-sm {
			    border: none;
			    background: none;
			    width: 55px;
			    padding-left: 0px;
			}
		}
	</style>
  </head>
  <body>
    <div class="main-content">
		<div class="main-content-inner">

			<div class="page-content  table-responsive">
	            <div class="panel panel-success">
					<div class="panel-body" id="PrintArea">
						<div class="panel panel-warning">
							<div class="panel-body">
								<div class="row">
									<h2 class="text-center" style="padding: 10px 0px; border: 2px solid lightgrey; width: 30%; margin: 0 auto;">Order Costing</h2>
									<br>
									<div class="col-sm-10">
										<table class="table" width="50%" cellpadding="0" cellspacing="0" border="0">
											<tr>
												<th>Order No</th>
												<td>{{ (!empty($order->order_code)?$order->order_code:null) }}</td>
												<th>Unit</th>
												<td>{{ (!empty($order->hr_unit_name)?$order->hr_unit_name:null) }}</td>
												<th>Buyer</th>
												<td>{{ (!empty($order->b_name)?$order->b_name:null) }}</td>
											</tr>
											<tr>
												<!-- <th>Brand</th>
												<td>{{ (!empty($order->br_name)?$order->br_name:null) }}</td> -->
												<th>Order Quantity</th>
												<td>{{ (!empty($order->order_qty)?$order->order_qty:null) }}</td>
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
	                    <table class="table table-bordered" style="font-size: 10px;">
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
			        </div>
			    </div>
			</div><!-- /.page-content -->
		</div>
	</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script>
    	function set_percentage(changeName, totalAmount, finalPlace) {
			var val1 	= $('input[name='+totalAmount+']').val();
			var val2 	= $('input[name='+changeName+']').val();
			var val3 	= (val2*100)/val1;
			$('input[name='+finalPlace+']').val(val3.toFixed(6));
		}

		$(window).on("load", function(){
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

    	window.onload = function () {
		    window.print();
		}
    </script>
  </body>
</html>
