@php
  $countryList = country_by_id();
  $countryList = collect($countryList)->pluck('cnt_name','cnt_id');
  $itemCategory = item_category_by_id();
@endphp

<form class="form-horizontal" id="itemForm" role="form" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input type="hidden" id="mcat_id" value="{{ $input['item_category'] }}">
    <input type="hidden" id="item-index" value="{{ $input['index'] }}">
    <input type="hidden" id="click-type" value="{{ $input['type'] }}">
    <div class="row">
        <input type="hidden" name="unit_id" value="">
        <div class="offset-3 col-6">
          <div class="row">
            <div class="col-sm-8">
              <div class="form-group has-float-label has-required">
                  <input type="text" class=" form-control" id="sup_name" name="sup_name" placeholder="Enter Supplier Name" value="" autocomplete="off" required />
                <label for="sup_name">Supplier Name</label>
              </div>
              <div class="form-group has-float-label has-required select-search-group">
                {{ Form::select('cnt_id', $countryList, null, ['placeholder'=>'Select Country Name','id'=>'country_id','class'=> 'form-control filter', 'required']) }}
                <label for="country_id">Country</label>
              </div>
              <div class="form-group has-float-label has-required">
                <textarea name="sup_address" id="sup_address" rows="1" class="form-control" placeholder="Supplier Address" required></textarea>
                <label for="sup_address">Supplier Address</label>
              </div>
              <div class="row">
                <div class="col-10 pr-0">
                  <div class="form-group has-required has-float-label">
                    <input type="text" id="contact0" name="scp_details[]" placeholder="Enter Contact Person (Name, Cell No, Email)" class="form-control" autocomplete="off" required />
                    <label for="contact0" > Contact Person </label>
                  </div>
                </div>
                <div class="col-2">
                  <button type="button" class="btn btn-sm btn-outline-success AddBtn_bu">+</button>
                </div>
              </div>
              <div id="addAddress"></div>
              <div class="form-group">
                <button class="btn btn-outline-success btn-md" type="button" id="itemBtn"><i class="fa fa-save"></i> Save</button>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group has-required ">
                <label>Supplier Type :</label>
                <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                  <input type="radio" id="Local" name="sup_type" class="custom-control-input bg-primary local" value="Local">
                  <label class="custom-control-label" for="Local"> Local </label>
                </div>
                <div class="custom-control custom-radio custom-radio-color-checked custom-control-inline">
                  <input type="radio" id="Foreign" name="sup_type" class="custom-control-input bg-primary foreign" value="Foreign">
                  <label class="custom-control-label" for="Foreign"> Foreign</label>
                </div>
              </div>
              <div class="form-group has-required">
                <label for="">Item Type :</label>
                @foreach($itemCategory as $key => $item)
                <div class="custom-control custom-checkbox custom-checkbox-color-check custom-control-inline">
                  <input type="checkbox" name="items[]" class="custom-control-input bg-primary" value="{{ $item->mcat_id }}" id="item-{{ $item->mcat_id }}" @if($input['item_category'] == $item->mcat_id) checked @endif>
                  <label class="custom-control-label" for="item-{{ $item->mcat_id }}"> {{ $item->mcat_name }}</label>
                </div>
                @endforeach
              </div>
              
            </div>

          </div>
        </div>
    </div>
</form> 