<div class="modal fade newSupplierModal" tabindex="-1" role="dialog" aria-labelledby="newArticleLabel">
  	<div class="modal-dialog modal-xs" role="document">
    	<form class="modal-content form-horizontal" id="supForm" role="form" method="POST">
      		<div class="modal-header">
        		<h4 class="modal-title text-center">Add Supplier</h4>
      		</div>
	    		<div class="modal-body row">
			<div class="col-md-12">
			
			{{ csrf_field() }}
					<div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="sup_name" > Supplier Name<span style="color: red">&#42;</span> </label>
						<div class="col-sm-9">
								<input type="text" name="sup_name" id="sup_name" placeholder="Supplier Name"  class="col-xs-9" data-validation="required length custom" data-validation-length="1-50" required/>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="cnt_id" >Country<span style="color: red">&#42;</span> </label>
						<div class="col-sm-9">
							{{ Form::select('cnt_id', $countryList, '', ['placeholder'=>'Select Country', 'id'=>'cnt_id','class'=> 'col-xs-9 filter', 'style'=>'width: 312px!important;','data-validation' => 'required']) }}
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="sup_address"> Address <span style="color: red">&#42;</span> </label>
						<div class="col-sm-9">
								<textarea name="sup_address" id="sup_address" class="col-xs-9" placeholder="Address"  data-validation="required length" data-validation-length="0-128"></textarea>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="sup_type"> Supplier Type <span style="color: red">&#42;</span> </label>
						<div class="col-sm-9">
							<div class="radio">
								<label>
									<!-- edited on 03-10-2019-->
										<input type="radio" checked="checked" id="sup_type" name="sup_type"  class="ace" value="Local"  data-validation="required"/>
										<span class="lbl"> Local</span>
								</label>
								<label>
										<input type="radio" id="sup_type" name="sup_type" class="ace" value="Foreign"/>
										<span class="lbl">Foreign</span>
								</label>
							</div>
						</div>
					</div>

					<div class="addRemove">
						<div class="form-group">
							<label class="col-sm-3 control-label no-padding-right" for="scp_details"> Contact Person <span style="color: red">&#42;</span> (<span style="font-size: 9px">Name, Cell No, Email</span>) </label>
							<div class="col-sm-9">
								<textarea name="scp_details[]" id="scp_details" class="col-xs-9" placeholder="Contact Person"  data-validation="required length" data-validation-length="0-128"></textarea>
								<div class="form-group col-xs-3">
									<button type="button" class="btn btn-sm btn-success AddBtn">+</button>
									<button type="button" class="btn btn-sm btn-danger RemoveBtn">-</button>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group" > 
						<label class="col-sm-3 control-label no-padding-right" for="scp_details"> Item</label>
						<div class="col-sm-9">
							@foreach($modalCats as $cat)
							<div class="checkbox">
								<label>
									<input name="item_id[]" class="ace ace-checkbox-2" type="checkbox" value="{{$cat->mcat_id}}">
									<span class="lbl"> {{$cat->mcat_name}}</span>
								</label>
							</div>
							@endforeach
						</div>
					</div>
					<input type="hidden" id="supllier-store" value='' disabled="disabled">
			</div>
		</div>  
      		<div class="modal-footer">
        		<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        		<button type="button" class="btn btn-primary btn-sm supplier_save">Submit</button>
      		</div>
    	{{ Form::close() }}
  	</div>
</div>
