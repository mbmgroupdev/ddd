@php 
    $productType = collect(product_type_by_id())->pluck('prd_type_name', 'prd_type_id'); 
@endphp
<div class="modal fade" id="new_garments_type" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            {{ Form::open(["url"=>"", "class"=>"form-horizontal", 'id'=>'newGmTypeFrm']) }}
                <div class="modal-header bg-primary">
                    <h2 class="modal-title text-center" id="myModalLabel">Add New Garment</h2>
                </div>

                <div class="modal-body">
                    <div class="message"></div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="prd_type_id"> Product Type<span style="color: red">&#42;</span>  </label>
                        <div class="col-sm-7">
                            {{ Form::select('modal_prd_type_id', $productType, null, ['placeholder'=>'Select Product Type', 'id'=>'modal_prd_type_id', 'class'=> 'col-xs-12', 'data-validation'=>'required', 'data-validation-error-msg' => 'The Product Type field is required','disabled']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="gmt_name" > Garment Type<span style="color: red">&#42;</span> </label>
                        <div class="col-sm-7">
                            <input type="text" name="gmt_name" id="gmt_name" placeholder="Garment Type" class="col-xs-12" data-validation="required length custom" data-validation-length="1-50"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label no-padding-right" for="gmt_remarks"> Remarks</label>
                        <div class="col-sm-7">
                            <textarea multiple="multiple" name="gmt_remarks" id="gmt_remarks" class="form-control" placeholder="Remarks"  data-validation="length custom" data-validation-length="1-128" data-validation-optional="true"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer clearfix " >
                    <div class="col-md-8">
                        <button type="button btn-sm" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-info btn-sm garments_add" type="submit" id="garments_add" >DONE</button>
                    </div>
                </div>

            {{Form::close()}}

        </div>
    </div>
</div>