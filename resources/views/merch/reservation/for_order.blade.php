@php
	$buyerList= buyer_by_id();
	$buyerList = collect($buyerList)->pluck('b_name','b_id');
    $prdtypList= product_type_by_id();
  	$prdtypList = collect($prdtypList)->pluck('prd_type_name','prd_type_id');
  	// $yearMonth = 
@endphp

<div class="row">
	<div class="col-sm-3">
		<div class="form-group has-required has-float-label">
	        <input type="month" class="form-control" id="month" name="res_year_month" placeholder=" Month-Year"required="required" value="{{ date('Y-m') }}"autocomplete="off" />
	        <label for="year-month" > Reservation Year-Month </label>
	    </div>
	</div>
	<div class="col-sm-3">
		<div class="form-group has-required has-float-label">
	        <input type="number" id="res-quantity" name="res_quantity" placeholder="Enter Quantity" class="form-control sah_cal" autocomplete="off" value="{{ $reservationQty }}" onClick="this.select()" required min="0" />
	        <label for="res-quantity" >Reservation Quantity </label>
	    </div>
	</div>
	<div class="col-sm-3">
		<div class="form-group has-required has-float-label">
	        <input type="number" id="res-smv" name="res_sewing_smv" placeholder="Enter Sewing SMV " class="form-control sah_cal" autocomplete="off" value="0" step="any" onClick="this.select()" required min="0" />
	        <label for="res-smv" > Sewing SMV </label>
	    </div>
	</div>
	<div class="col-sm-3">
		<div class="form-group has-required has-float-label">
	        <input type="number" id="sah" name="res_sah" placeholder="Enter SAH" class="form-control" autocomplete="off" step="any" required readonly value="0" min="0" />
	        <label for="sah" > SAH </label>
	    </div>
	</div>
	
</div>
<div class="row1">
	<div class="form-group">
        <button class="btn btn-outline-success pull-right" type="button" id="saveBtn">
            <i class="fa fa-save"></i> Save
        </button>
    </div>
</div>
