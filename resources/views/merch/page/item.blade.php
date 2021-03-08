@php
  $uom = uom_by_id();
  $uom = collect($uom)->pluck('measurement_name','id');
  $itemCategory = item_category_by_id();
@endphp

<form class="form-horizontal" id="itemForm" role="form" enctype="multipart/form-data">
  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
  <div class="row">
      <div class="offset-3 col-6">
          <div class="row">
              <div class="col-sm-6">
                  <div class="form-group has-float-label">
                      <input type="text" class="autocomplete_sub_cat form-control" id="mcat_name" data-type="mcat_name" placeholder="Enter Sub Category" value="{{ $itemCategory[$input['item_category']]->mcat_name??'' }}" autocomplete="off" readonly />
                    <label for="mcat_name">Main Category</label>
                  </div>
                  <input type="hidden" name="mcat_name" id="mcat_id" value="{{ $input['item_category'] }}">
                  <input type="hidden" id="item-index" value="{{ $input['index'] }}">
                  <input type="hidden" id="click-type" value="{{ $input['type'] }}">
                  <div class="form-group has-float-label">
                      <input type="text" class="autocomplete_sub_cat form-control" id="subcategory_name" name="subcategory_name" data-type="subcategory_name" placeholder="Enter Sub Category" value="" autocomplete="off" />
                    <label for="subcategory_name">Sub Category</label>
                  </div>
                  <div class="form-group has-float-label">
                      <input type="text" class=" form-control" id="item_code" name="item_code" placeholder="Enter Item Code" value="" autocomplete="off" />
                    <label for="item_code">Item Code</label>
                  </div>
                  <div class="form-group has-float-label has-required">
                      <input type="text" class=" form-control" id="item_name" name="item_name" placeholder="Enter Item Name" value="" autocomplete="off" required />
                    <label for="item_name">Item Name</label>
                  </div>
                  <div class="form-group has-float-label has-required select-search-group">
                    {{ Form::select('uom[]', $uom, null, ['id'=>'uom-item','class'=> 'form-control', 'required', 'multiple']) }}
                    <label for="uom">UOM</label>
                  </div>
              </div>
              <div class="col-sm-6">
                  
                  <div class="form-group has-float-label">
                      <input type="text" class=" form-control" id="description" name="description" placeholder="Enter Description" value="" autocomplete="off" />
                    <label for="description">Description</label>
                  </div>
                  
                  <div class="form-group has-required ">
                      <label>Depends On:</label>
                      <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                        <input type="radio" id="color" name="depends" class="custom-control-input bg-primary" value="1">
                        <label class="custom-control-label" for="color"> Color </label>
                      </div>
                      <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                        <input type="radio" id="size" name="depends" class="custom-control-input bg-primary" value="2">
                        <label class="custom-control-label" for="size"> Size</label>
                      </div>
                      <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                        <input type="radio" id="sizecolor" name="depends" class="custom-control-input bg-primary" value="3">
                        <label class="custom-control-label" for="sizecolor"> Size & Color </label>
                      </div>
                      <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                        <input type="radio" id="none" name="depends" class="custom-control-input bg-primary" value="0" checked>
                        <label class="custom-control-label" for="none"> None </label>
                      </div>
                      
                  </div>
              </div>
          </div>
          
          <div class="form-group">
            <button class="btn btn-outline-success btn-md" type="button" id="itemBtn"><i class="fa fa-save"></i> Save</button>
          </div>
      </div>
  </div>
  
</form>