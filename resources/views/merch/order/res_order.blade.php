@php
	$unitList= unit_by_id();
	$buyerList= buyer_by_id();
	$resYearMonth = $reservation->res_year.'-'.$reservation->res_month;
	$resYearMonth = date('Y-m', strtotime($resYearMonth));
@endphp
<div class="row">
      <div class="offset-3 col-6">
		<form class="form-horizontal" id="itemForm" role="form" method="post" enctype="multipart/form-data">
		    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
		    <input type="hidden" id="page-type" value="order-store" />
		    <input type="hidden" name="res_id" value="{{ $reservation->id }}" />
		    <input type="hidden" name="b_id" id="buyer" value="{{ $reservation->b_id }}" />
		    <input type="hidden" name="unit_id" value="{{ $reservation->hr_unit_id }}" />
		    <input type="hidden" id="res-quantity" name="res_quantity" value="{{ $reservation->res_quantity }}" />
		    <input type="hidden" id="res-year-month" name="res_year_month" value="{{ $resYearMonth }}" />
		    
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
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label select-search-group">
				      {{ Form::select('season_n_year', $seasonList, null, [ 'id' => 'season', 'placeholder' => 'Select Season', 'class' => 'seasonChange form-control filter', 'readonly']) }}
				      <label for="season" > Season </label>
				    </div>
				    <div class="form-group has-float-label select-search-group">
				      {{Form::select('mr_style_stl_id', [], null, [ 'id' => 'style-no', 'placeholder' => 'Select Style Number', 'class' => 'style-no form-control filter', 'readonly'])}}
				      <label for="style-no"> Style Number </label>
				    </div>
				    <div class="form-group has-required has-float-label">
				        <input type="text" id="brand" placeholder="Brand Name" class="form-control" autocomplete="off" readonly />
				        <label for="brand" > Brand </label>
				    </div>
				    <div class="form-group has-float-label">
				        <input type="text" id="style-ref-2" placeholder="Style Reference 2" class="form-control" autocomplete="off" readonly />
				        <label for="style-ref-2" > Style Ref. 2 </label>
				    </div>
				    <div class="form-group has-required has-float-label">
				        <input type="month" class="form-control" id="month" name="order_year_month" placeholder=" Month-Year"required="required" value="{{ $resYearMonth }}"autocomplete="off" max="{{ $resYearMonth }}" />
				        <label for="month" >Order Year-Month </label>
				    </div>
		    	</div>
		    	<input type="hidden" name="mr_season_se_id" id="mr_season_se_id" value="">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="text" id="reference-no" name="order_ref_no" placeholder="Enter Reference No" class="form-control" autocomplete="off" />
				        <label for="reference-no" > Reference No </label>
				    </div>
				    <div class="form-group has-required has-float-label">
				        <input type="number" id="order-qty" name="order_qty" placeholder="Enter Order Quantity" class="form-control" autocomplete="off" value="{{ $reservation->res_quantity }}" required onClick="this.select()" min="0" max="{{ $reservation->res_quantity }}" />
				        <label for="order-qty"> Order Quantity </label>
				    </div>
				    <div class="form-group has-required has-float-label">
				        <input type="date" class="form-control" id="pcd-date" name="pcd" placeholder="Enter Planned Cut Date" required="required" value="{{ date('Y-m-d') }}"autocomplete="off" />
				        <label for="pcd-date" > PCD </label>
				    </div>
				    <div class="form-group has-required has-float-label">
				        <input type="date" class="form-control" id="delivery-date" name="order_delivery_date" placeholder="Enter Delivery Date"required="required" value="{{ date('Y-m-d') }}"autocomplete="off" />
				        <label for="delivery-date" > Delivery Date </label>
				    </div>
				    <div class="form-group">
				        <button class="btn btn-outline-success pull-right" type="button" id="itemBtn">
				            <i class="fa fa-save"></i> Save
				        </button>
				    </div>
		    	</div>
		    </div>
		                                     
		</form>
	</div>
</div>