@php
  $supplierList = supplier_by_id();
  $supplierList = collect($supplierList)->pluck('sup_name','sup_id');
  $itemCategory = item_category_by_id();
@endphp
<div class="row">
      <div class="offset-3 col-6">
		<form class="form-horizontal" id="itemForm" role="form" method="post" enctype="multipart/form-data">
		    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
		    <input type="hidden" name="mcat_name" id="mcat_id" value="{{ $input['item_category'] }}">
		  	<input type="hidden" id="item-index" value="{{ $input['index'] }}">
		  	<input type="hidden" id="click-type" value="{{ $input['type'] }}">
		    <div class="form-group has-required has-float-label select-search-group">
		      {{Form::select('supplier', $supplierList, $input['supplierid'], [ 'id' => 'supplier', 'placeholder' => 'Select Supplier', 'class' => 'form-control filter', 'required'])}}
		      <label for="supplier" > Supplier </label>
		      
		    </div>
		    <div class="form-group has-required has-float-label">
		        <input type="text" id="art_name" name="art_name" placeholder="Enter Article Name " class="form-control" autocomplete="off" required="" />
		        <label for="art_name" > Article Name </label>
		    </div>
		    <div class="form-group has-required has-float-label">
		        <input type="text" id="composition" name="composition" placeholder="Enter Composition Name " class="form-control" autocomplete="off" required>
		        <label for="composition" > Composition </label>
		    </div>
		    <div class="form-group has-required has-float-label">
		        <input type="text" id="art_construction" name="art_construction" placeholder="Enter Construction Name " class="form-control" autocomplete="off" required>
		        <label for="art_construction" > Construction </label>
		    </div>

		    
		    <div class="form-group">
		        <button class="btn btn-outline-success" type="button" id="itemBtn">
		            <i class="fa fa-save"></i> Save
		        </button>
		    </div>                                 
		</form>
	</div>
</div>