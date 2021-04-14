
<div class="row">
      <div class="offset-3 col-6">
		<form class="form-horizontal" id="itemForm" role="form" method="post" enctype="multipart/form-data">
		    <input type="hidden" name="_method" value="PUT">
    		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		    <input type="hidden" id="page-type" value="order-update" />
		    <input type="hidden" id="res-quantity" value="{{ $resQty }}" />
		    <input type="hidden" id="po-qty" value="{{ $poQty }}" />
		    <input type="hidden" name="order_id" id="order-id" value="{{ $order->order_id }}" />
		    <input type="hidden" name="res_id" id="res-id" value="{{ $order->res_id }}" />
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="text" class="form-control" id="unit-name" placeholder="Unit Name" value="{{ $unitList[$order->unit_id]['hr_unit_name']??'' }}" readonly />
				        <label for="unit-name" > Unit Name </label>
				    </div>
		    	</div>
		    	<div class="col-sm-6">
		    		
				    <div class="form-group has-required has-float-label">
				        <input type="text" class="form-control" id="buyer-name" placeholder="Buyer Name" value="{!! $buyerList[$order->mr_buyer_b_id]->b_name??'' !!}" readonly />
				        <label for="buyer-name" >Buyer Name </label>
				    </div>
		    	</div>
		    </div>
		    @php	
		    	$yearMonth = $order->order_year.'-'.$order->order_month;
		    @endphp
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="text" class="form-control" id="internal-order-no" placeholder="Internal Order No"required="required" value="{{ $order->order_code }}"autocomplete="off" readonly />
				        <label for="internal-order-no" > Internal Order No </label>
				    </div>
		    	</div>
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="text" class="form-control" id="style-no" placeholder="Style No"required="required" value="{!! $order->style->stl_no !!}"autocomplete="off" readonly />
				        <label for="style-no" > Style No </label>
				    </div>
		    	</div>
		    	
		    	
		    </div>
		    
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="month" class="form-control" id="month" name="ord_year_month" placeholder=" Month-Year"required="required" value="{{ date('Y-m', strtotime($yearMonth)) }}"autocomplete="off" />
				        <label for="month" > Year-Month </label>
				    </div>
		    	</div>
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
    					<input type="text" class="form-control" id="season" placeholder="Season Name"required="required" value="{!! $seasonList[$order->style->mr_season_se_id]->se_name??'' !!}-{{ $order->style->stl_year }}" autocomplete="off" readonly />
				        
				        <label for="season" > Season </label>
				    </div>
		    	</div>
		    	
		    </div>
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="text" class="form-control" id="reference-no" name="order_ref_no" placeholder="Style No"required="required" value="{!! $order->order_ref_no !!}"autocomplete="off" />
				        <label for="reference-no" > Reference No </label>
				    </div>
		    	</div>
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="number" id="order_qty" name="order_qty" placeholder="Enter Quantity" class="form-control sah_cal" autocomplete="off" value="{{ $order->order_qty }}" onClick="this.select()" required />
				        <label for="order_qty" > Quantity </label>
				    </div>
		    	</div>
		    </div>
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="date" class="form-control" id="delivery-date" name="order_delivery_date" placeholder="Delivery Dat"required="required" value="{{ $order->order_delivery_date }}"autocomplete="off" />
				        <label for="delivery-date" > Delivery Date </label>
				    </div>
		    	</div>
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="date" id="pcd" name="pcd" placeholder="Enter PCD" class="form-control" autocomplete="off" value="{{ $order->pcd }}" required />
				        <label for="pcd" > PCD </label>
				    </div>
		    	</div>
		    </div>
		    
		    <div class="form-group">
		        <button class="btn btn-outline-success pull-right" type="button" id="itemBtn">
		            <i class="fa fa-save"></i> Update
		        </button>
		    </div>                                 
		</form>
	</div>
</div>