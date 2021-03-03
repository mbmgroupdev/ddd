<div class="panel-body pb-0">
   <form class="form-horizontal" method="post" action="{{ url('merch/setup/item_update') }}" id="itemFormEdit" role="form" enctype="multipart/form-data">
        {{ csrf_field() }} 
        <div class="row">
            <div class="offset-3 col-6">
              <input type="hidden" name="mcat_id" value="{{ $mitem->id }}">
                <div class="form-group has-float-label has-required select-search-group">
                  {{ Form::select('mcat_name', $cat_list, $mitem->mcat_id, ['placeholder'=>'Select Category Name','id'=>'mcat_id','class'=> 'form-control', 'required']) }}
                  <label for="mcat_id">Main Category</label>
                </div>

                <div class="form-group has-float-label">
                    <input type="text" class="autocomplete_sub_cat form-control" id="subcategory_name" name="subcategory_name" data-type="subcategory_name" placeholder="Enter Sub Category" value="{{ $mitem->msubcat_name }}" autocomplete="off" />
                    <input type="hidden" name="msubcat_id" id="msubcat_id" value="{{$mitem->msubcat_id}}">
                  <label for="subcategory_name">Sub Category</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="newItem">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group has-float-label">
                                <input type="text" class=" form-control" id="item_code-0" name="item_code" placeholder="Enter Item Code" value="{{$mitem->item_code}}" autocomplete="off" />
                              <label for="item_code-0">Item Code</label>
                            </div>
                            <div class="form-group has-float-label has-required">
                                <input type="text" class=" form-control" id="item_name-0" name="item_name" placeholder="Enter Item Name" value="{{$mitem->item_name}}" autocomplete="off" required />
                              <label for="item_name-0">Item Name</label>
                            </div>
                            <div class="form-group has-float-label has-required select-search-group">
                              {{ Form::select('uom[]',$uom, $uomThis, ['id'=>'uom-edit','class'=> 'form-control', 'required', 'multiple']) }}
                              <label for="uom-edit">UOM</label>
                            </div>

                        </div>
                        <div class="col-6">
                            <div class="form-group has-float-label select-search-group">
                              {{ Form::select('buyer', $buyerList, $mitem->buyer_id, ['placeholder'=>'Select Buyer','id'=>'buyer-0','class'=> 'form-control']) }}
                              <label for="buyer-0">Buyer</label>
                            </div>
                            <div class="form-group has-float-label">
                                <input type="text" class=" form-control" id="description-0" name="description" placeholder="Enter Description" value="{{$mitem->description}}" autocomplete="off" />
                              <label for="description-0">Description</label>
                            </div>
                            
                            <div class="">
                                <label>Depends On:</label>
                                <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                                  <input type="radio" id="color-0" name="depends" class="custom-control-input bg-primary" value="1" @if($mitem->dependent_on==1) checked @endif>
                                  <label class="custom-control-label" for="color-0"> Color </label>
                                </div>
                                <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                                  <input type="radio" id="size-0" name="depends" class="custom-control-input bg-primary" value="2" @if($mitem->dependent_on==2) checked @endif>
                                  <label class="custom-control-label" for="size-0"> Size</label>
                                </div>
                                <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                                  <input type="radio" id="sizecolor-0" name="depends" class="custom-control-input bg-primary" value="3" @if($mitem->dependent_on==3) checked @endif>
                                  <label class="custom-control-label" for="sizecolor-0"> Size & Color </label>
                                </div>
                                <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                                  <input type="radio" id="none-0" name="depends" class="custom-control-input bg-primary" value="0" @if($mitem->dependent_on==0) checked @endif>
                                  <label class="custom-control-label" for="none-0"> None </label>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                </div> 
                <div class="form-group">
                  <button class="btn btn-outline-success btn-md" type="submit" id="itemEditBtn"><i class="fa fa-save"></i> Save</button>
                </div>
            </div>
        </div> 

    </form> 
</div>

@push('js')
  <script>
    
  </script>
@endpush