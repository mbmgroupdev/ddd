@php
	$buyerList= buyer_by_id();
	$buyerList = collect($buyerList)->pluck('b_name','b_id');
    $prdtypList= product_type_by_id();
  	$prdtypList = collect($prdtypList)->pluck('prd_type_name','prd_type_id');
@endphp
<div class="row">
      <div class="offset-3 col-6">
		<form class="form-horizontal" id="itemForm" role="form" method="post" enctype="multipart/form-data">
		    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
		    
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label select-search-group">
				      {{Form::select('hr_unit_id', $unitList, null, [ 'id' => 'unit', 'placeholder' => 'Select Unit Name', 'class' => 'form-control filter unitChange', 'required'])}}
				      <label for="unit" > Unit Name </label>
				    </div>
		    	</div>
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label select-search-group">
				      {{Form::select('b_id', $buyerList, null, [ 'id' => 'buyer', 'placeholder' => 'Select Buyer Name', 'class' => 'form-control filter buyerChange', 'required'])}}
				      <label for="buyer" > Buyer Name </label>
				    </div>
		    	</div>
		    </div>
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="month" class="form-control" id="month" name="res_year_month" placeholder=" Month-Year"required="required" value="{{ date('Y-m') }}"autocomplete="off" />
				        <label for="year-month" > Year-Month </label>
				    </div>
		    	</div>
		    	
		    </div>
		    
		    
		    <div id="order-entry-section">
		    	<div class="row">
		    		<div class="col-sm-6">
			    		<div class="form-group has-required has-float-label select-search-group">
					      {{Form::select('mr_season_se_id', [], null, [ 'id' => 'season', 'placeholder' => 'Select Season Name', 'class' => 'form-control filter seasonChange', 'readonly'])}}
					      <label for="season" > Season Name </label>
					    </div>
			    	</div>
			    	<div class="col-sm-6">
			    		<div class="form-group has-required has-float-label">
					        <input type="text" id="reference-no" name="order_ref_no" placeholder="Enter Reference No" class="form-control" autocomplete="off" />
					        <label for="reference-no" > Reference No </label>
					    </div>
			    	</div>
		    	</div>
		    	<div class="row">
		    		<div class="col-sm-6">
			    		<div class="form-group has-required has-float-label select-search-group">
					      {{Form::select('mr_style_stl_id', [], null, [ 'id' => 'style-no', 'placeholder' => 'Select Style Number', 'class' => 'form-control filter', 'readonly'])}}
					      <label for="style-no" > Style Number </label>
					    </div>
			    	</div>
			    	<div class="col-sm-6">
			    		<div class="form-group has-required has-float-label">
					        <input type="number" id="order-quantity" name="order_qty" placeholder="Enter Order Quantity" class="form-control" autocomplete="off" value="0" required onClick="this.select()" min="0" />
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