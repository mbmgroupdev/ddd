
<div class="row">
      <div class="offset-3 col-6">
		<form class="form-horizontal" id="itemForm" role="form" method="post" enctype="multipart/form-data">
		    <input type="hidden" name="_method" value="PUT">
    		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		    <input type="hidden" id="page-type" value="reservation-update" />
		    <input type="hidden" id="order-qty" value="{{ $orderQty }}" />
		    <input type="hidden" name="id" id="res-id" value="{{ $reservation->id }}" />
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label select-search-group">
				      {{Form::select('hr_unit_id', $unitList, $reservation->hr_unit_id, [ 'id' => 'unit', 'placeholder' => 'Select Unit Name', 'class' => 'form-control filter unitChange', 'required'])}}
				      <label for="unit" > Unit Name </label>
				    </div>
		    	</div>
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label select-search-group">
				      {{Form::select('b_id', $buyerList, $reservation->b_id, [ 'id' => 'buyer', 'placeholder' => 'Select Buyer Name', 'class' => 'form-control filter buyerChange', 'required'])}}
				      <label for="buyer" > Buyer Name </label>
				    </div>
		    	</div>
		    </div>
		    @php	
		    	$yearMonth = $reservation->res_year.'-'.$reservation->res_month;
		    @endphp
		    <div class="row">
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label">
				        <input type="month" class="form-control" id="month" name="res_year_month" placeholder=" Month-Year"required="required" value="{{ date('Y-m', strtotime($yearMonth)) }}"autocomplete="off" />
				        <label for="year-month" > Year-Month </label>
				    </div>
		    	</div>
		    	<div class="col-sm-6">
		    		<div class="form-group has-required has-float-label select-search-group">
				      {{Form::select('prd_type_id', $prdtypList, $reservation->prd_type_id, [ 'id' => 'product-type', 'placeholder' => 'Select Product Type Name', 'class' => 'form-control filter', 'required'])}}
				      <label for="product-type" > Product Type Name </label>
				      
				    </div>
		    	</div>
		    </div>
		    
		    <div class="row">
		    	<div class="col-sm-4">
		    		<div class="form-group has-required has-float-label">
				        <input type="number" id="res-quantity" name="res_quantity" placeholder="Enter Quantity" class="form-control sah_cal" autocomplete="off" value="{{ $reservation->res_quantity }}" onClick="this.select()" required min="0" />
				        <label for="res-quantity" > Quantity </label>
				    </div>
		    	</div>
		    	<div class="col-sm-4">
		    		<div class="form-group has-required has-float-label">
				        <input type="number" id="res-smv" name="res_sewing_smv" placeholder="Enter Sewing SMV " class="form-control sah_cal" autocomplete="off" value="{{ $reservation->res_sewing_smv }}" step="any" onClick="this.select()" required min="0" />
				        <label for="res-smv" > Sewing SMV </label>
				    </div>
		    	</div>
		    	<div class="col-sm-4">
				    <div class="form-group has-required has-float-label">
				        <input type="number" id="sah" name="res_sah" placeholder="Enter SAH" class="form-control" autocomplete="off" step="any" required readonly value="{{ $reservation->res_sah }}" min="0" />
				        <label for="sah" > SAH </label>
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