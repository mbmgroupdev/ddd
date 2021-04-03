<!--New Size Group Modal-->
@php $brabd = collect(brand_by_id())->pluck('br_name', 'br_id'); @endphp
<div class="modal fade" id="new_size_group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h2 class="modal-title text-center" id="myModalLabel">Add New Size Group
                </h2>
            </div>
            <div class="modal-body">
                {{ Form::open(["url"=>"", "class"=>"form-horizontal", 'id'=>'newSizeFrm']) }}
                    {{ csrf_field() }}

                        <div class="form-group">
                          <label class="col-sm-3 control-label no-padding-right" for="product_size_group" >Brand<span style="color: red">&#42;</span> </label>
                          <div class="col-sm-9">
                              <div class="form-group col-xs-12 col-sm-10" >
                                 {{ Form::select('brand', $brand, null, ['placeholder'=>'Select Brand', 'id'=> 'brand','class'=> 'col-xs-12','data-validation' => 'required']) }}
                               </div>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 control-label no-padding-right" for="genre" >Genre <span style="color: red">&#42;</span> </label>
                          <div class="col-sm-7">
                            <select name="gender" id="genre" class="col-xs-12" data-validation = "required">
                              <option>Select Genre</option>
                               <option value="Mens">Men's</option>
                               <option value="Ladies">Ladies</option>
                               <option value="Boys/Girls">Boys/Girls</option>
                               <option value="Girls">Girls</option>
                               <option value="Womens">Women's</option>
                               <option value="Mens & Ladies">Men's & Ladies</option>
                               <option value="Baby Boys/Girls">Baby Boys/Girls</option>
                              </select>
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 control-label no-padding-right" for="sg_name" >Size Group Name <span style="color: red">&#42;</span> </label>
                          <div class="col-sm-7">
                              <input type="text" id="sg_name" name="sg_name" placeholder="Enter Size Group Name" class="col-xs-12" data-validation="required length custom" data-validation-length="1-45"/>
                          </div>
                        </div>
                        <div class="addRemove">
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="psize" >Size <span style="color: red">&#42;</span></label>
                                <div class="col-sm-9">
                                    <input type="text" id="psize" name="psize[]" placeholder="Size" class="col-xs-9 psize" data-validation="required length custom" data-validation-length="1-11"/>
                                <!--  <a href=""><h5>+ Add More</h5></a>-->
                                    <div class="form-group col-xs-3 col-sm-3">
                                         <button type="button" class="btn btn-sm btn-success AddBtn_size">+</button>
                                         <button type="button" class="btn btn-sm btn-danger RemoveBtn_size">-</button>
                                    </div>
                                 </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right" for="sino" >SI No <span style="color: red">&#42;</span></label>
                                <div class="col-sm-9">
                                    <input type="text" id="sino" name="sino[]" placeholder="Size No" class="col-xs-9 sino" data-validation="required length custom" data-validation-length="1-45"/>
                                </div>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                <div class="modal-footer" style="margin-top: 20px;">
                    <div class="col-md-8">
                        <button type="button btn-sm" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-info btn-sm size-add-modal" type="submit" id="size-add-modal" >
                         DONE
                       </button>
                    </div>
                   {{ Form::close() }}
                </div>
        </div>
    </div>
</div>