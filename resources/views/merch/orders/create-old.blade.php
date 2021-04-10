@php
	$unitList= unit_by_id();
	$buyerList= buyer_by_id();
@endphp
<div class="row">
      <div class="offset-3 col-6">
		<form class="form-horizontal" id="itemForm" role="form" method="post" enctype="multipart/form-data">
		    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
		    <input type="hidden" id="page-type" value="order-store" />
		    <input type="hidden" name="res_id" value="{{ $reservation->id }}" />
		    <input type="hidden" name="b_id" value="{{ $reservation->b_id }}" />
		    <input type="hidden" name="unit_id" value="{{ $reservation->hr_unit_id }}" />
		    <input type="hidden" name="res_quantity" value="{{ $reservation->res_quantity }}" />
		    
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				      <input type="text" class="form-control" id="unit" placeholder="Unit Name" value="{{ $unitList[$reservation->hr_unit_id]['hr_unit_name']??'' }}" readonly autocomplete="off" />
				      <label for="unit" > Unit Name </label>
				    </div>
		    	</div>
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label select-search-group">
				      <input type="text" class="form-control" id="buyerName" placeholder="Buyer Name" value="{!! $buyerList[$reservation->b_id]->b_name??'' !!}" readonly autocomplete="off" />
				      <label for="buyerName" > Buyer Name </label>
				    </div>
		    	</div>
		    </div>
		    <input type="hidden" id="buyer" value="{{ $reservation->b_id }}">
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="month" class="form-control" id="month" name="order_year_month" placeholder=" Month-Year"required="required" value="{{ date('Y-m') }}"autocomplete="off" />
				        <label for="year-month" >Order Year-Month </label>
				    </div>
		    	</div>
		    	
		    </div>
		    
		    <div id="order-entry-section">
		    	<div class="row">
		    		<div class="col-sm-6">
			    		<div class="form-group has-required has-float-label select-search-group">
					      {{Form::select('mr_season_se_id', $seasonList, null, [ 'id' => 'season', 'placeholder' => 'Select Season Name', 'class' => 'form-control filter seasonChange'])}}
					      <label for="season" > Season Name </label>
					    </div>
			    	</div>
			    	<div class="col-sm-6">
			    		<div class="form-group has-required has-float-label">
					        <input type="text" id="reference-no" name="order_ref_no" placeholder="Enter Reference No" class="form-control" autocomplete="off" required />
					        <label for="reference-no" > Reference No </label>
					    </div>
			    	</div>
		    	</div>
		    	<div class="row">
		    		<div class="col-sm-6">
			    		<div class="form-group has-required has-float-label select-search-group">
					      {{Form::select('mr_style_stl_id', $styleList, null, [ 'id' => 'style-no', 'placeholder' => 'Select Style Number', 'class' => 'form-control filter'])}}
					      <label for="style-no" > Style Number </label>
					    </div>
			    	</div>
			    	<div class="col-sm-6">
			    		<div class="form-group has-required has-float-label">
					        <input type="number" id="order-quantity" name="order_qty" placeholder="Enter Order Quantity" class="form-control" autocomplete="off" value="{{ $reservation->res_quantity }}" required onClick="this.select()" min="0" max="{{ $reservation->res_quantity }}" />
					        <label for="order-quantity" > Order Quantity </label>
					    </div>
			    	</div>
		    	</div>
		    	<div class="row">
		    		<div class="col-sm-6">
			    		<div class="form-group has-required has-float-label">
					        <input type="date" class="form-control" id="pcd-date" name="pcd" placeholder="Enter Planned Cut Date" required="required" value="{{ date('Y-m-d') }}"autocomplete="off" />
					        <label for="pcd-date" > PCD </label>
					    </div>
			    	</div>
			    	<div class="col-sm-6">
			    		<div class="form-group has-required has-float-label">
					        <input type="date" class="form-control" id="delivery-date" name="order_delivery_date" placeholder="Enter Delivery Date"required="required" value="{{ date('Y-m-d') }}"autocomplete="off" />
					        <label for="delivery-date" > Delivery Date </label>
					    </div>
			    	</div>
		    	</div>
		    </div>
		    <div class="form-group">
		        <button class="btn btn-outline-success pull-right" type="button" id="itemBtn">
		            <i class="fa fa-save"></i> Save
		        </button>
		    </div>                                 
		</form>
	</div>
</div>