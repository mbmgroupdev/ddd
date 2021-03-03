@extends('merch.index')
@push('css')
	<style>
		.modal { overflow: auto !important; }
		.modal-header{
			background: #428BCA;
    		color: #fff;
		}
		td input[type=text], input[type=number], .custom-font-table select {min-width: 100px !important;height: auto !important;}
		.table-scroll {
			position:relative;
			max-width:100%;
			margin:auto;
			overflow:hidden;
			
		}
		.table-wrap {width:100%;overflow:auto;max-height: 550px; overflow: auto; overflow-y: auto; display: block;}
		table thead th { position: sticky; position: -webkit-sticky; top: 0; z-index: 999;}
		.table-scroll table {
			width:100%;
			margin:auto;
			/*border-collapse:separate;
			border-spacing:0;*/
		}
		.table-scroll th, .table-scroll td {
			padding:5px 10px;
			
			background:#fff;
			white-space:nowrap;
			vertical-align:top;
		}
		.table-scroll thead, .table-scroll tfoot {
			background:#f9f9f9;
		}

		#bomItemTable tbody tr td{
			box-sizing: border-box;
			-moz-box-sizing: border-box;
		}
		.tr-active td{vertical-align: middle !important;}
		.select{width: 100px !important;}
		#cnt_id{width: 312px!important;}
		@media only screen and (max-width: 550px) {
        
         .detailTable{display: block;overflow-x: auto;}
        
    	}

    	@media only screen and (max-width: 767px) {
        
        .modal-dialog{padding-top: 50px;}
        
	    }
	    @media only screen and (max-width: 480px) {
	        
	        .modal-dialog{padding-top: 90px;}
	        
	    }

	    @media print{

	    	

	    }
	    .tr-disabled{
	    	display: none;
	    }
	    .select2{
			width: 100px!important;
		}

		
	</style>
@endpush
@section('content')
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
					<i class="ace-icon fa fa-file-text home-icon"></i>
					<a href="#">Style BOM</a>
				</li>
				<li class="active">Style BOM Edit</li>
			</ul><!-- /.breadcrumb -->
		</div>
		@php
			// dump($bomCostingArray);
		@endphp

		<div class="page-content" style="padding-bottom: 0">
            <div class="row">
            	<div class="col-sm-12">
	            	<div class="page-content-section">
		            	<!-- Display Erro/Success Message -->
		            	@include('inc/message')

			            {{ Form::open(['url'=>('merch/style_bom/'.request()->segment(3).'/update'), 'class'=>'row']) }}
			            	<div class="panel panel-info" style="margin-bottom: 0">
			            		@if(!empty($bomCostingArray))
					            	@foreach($bomCostingArray as $k=>$bomCostingId)
										<input type="hidden" name="bom_costing_pre_id[]" value="{{ $bomCostingId }}">
					            	@endforeach
					        	@endif
				            	<div class="panel-heading"><h6>Style BOM

				            		<button type="button" rel='tooltip' data-tooltip-location='top' data-tooltip='Please Print &#10 after Update' type="button" class="btn btn-success btn-xx pull-right" onClick="printMe('image','printMe1','printMe2','style_no')" style="margin-left: 10px;">
									<i class="glyphicon  glyphicon-print"></i> Print
									</button>

				            		{!!$costingButton!!}
				            		&nbsp;

									<button rel='tooltip' data-tooltip-location='left' data-tooltip='Style BOM Add Item' type="button" class="btn btn-primary btn-xx pull-right" data-toggle="modal" data-target="#newBomModal" style="margin-right: 10px;">
									<i class="glyphicon  glyphicon-plus"></i>	Add Item
									</button>
								</h6>
								</div>

								<div class="panel-body" id="PrintArea">
									<div class="panel panel-warning">
										<div class="panel-body">
											<div class="col-sm-12">
												<div class="col-sm-10" id="printMe1">
													<table class="custom-font-table table detailTable" width="50%" cellpadding="0" cellspacing="0" border="0">
														<tr>
															<th>Production Type</th>
															<td>{{ (!empty($style->stl_type)?$style->stl_type:null) }}</td>
															<th>Style Reference 1</th>
															<td id="style_no">{{ (!empty($style->stl_no)?$style->stl_no:null) }}</td>
															<th>Operation</th>
															<td>{{ (!empty($operations->name)?$operations->name:null) }}</td>
														</tr>
														<tr>
															<th>Buyer</th>
															<td>{{ (!empty($style->b_name)?$style->b_name:null) }}</td>
															<th>SMV/PC</th>
															<td>{{ (!empty($style->stl_smv)?$style->stl_smv:null) }}</td>
															<th>Speacial Machine</th>
															<td>{{ (!empty($machines->name)?$machines->name:null) }}</td>
														</tr>
														<tr>
															<th>Style Reference 2</th>
															<td>{{ (!empty($style->stl_product_name)?$style->stl_product_name:null) }}</td>
															<th>Sample Type</th>
															<td>{{ (!empty($samples->name)?$samples->name:null) }}</td>
															<th>Remarks</th>
															<td>{{ (!empty($style->stl_description)?$style->stl_description:null) }}</td>
														</tr>
													</table>
												</div>
												<div class="col-sm-2 picDiv" id="image">
													<a href="{{ asset(!empty($style->stl_img_link)?$style->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" target="_blank">
														<img class="thumbnail" height="100px" src="{{ asset(!empty($style->stl_img_link)?$style->stl_img_link:'assets/images/avatars/profile-pic.jpg') }}" alt=""/>
													</a>
												</div>
											</div>
										</div>
									</div>

									<input type="hidden" id="mr_style_stl_id" name="mr_style_stl_id" value="{{ (!empty($style->stl_id)?$style->stl_id:null) }}">
									<div class="panel panel-success" style="margin-bottom: 0">
					                	<div class="panel-body">
					                		<div id="table-scroll" class="table-scroll">
						                    	<div class="table-wrap" id="printMe2">
						                    		<div class="msg-wraper"></div>
								                    <table id="bomItemTable" class="display stripe row-border order-column custom-font-table table table-bordered main-table" style="width:100%">
														<thead>
															<tr class="success">
																<th class="fixed-side">Main Category</th>
																<th class="fixed-side">Item</th>
																<th>Item Code</th>
																<th>Description</th>
																<th width="80">Color</th>
																<th>Size/Width</th>
																<th width="80">Supplier</th>
																<th width="80">Article</th>
																<th>Composition</th>
																<th>Construction</th>
																<th width="80">UoM</th>
																<th>Consumption</th>
																<th>Extra(%)</th>
																<th>Extra Qty</th>
																<th>Total</th>
															</tr>
								                        </thead>
								                        <tbody>
								                        	@foreach($modalCats as $cat)
																@foreach($items as $item)
																@if($cat->mcat_id == $item->mcat_id)
																@php
																    $disabledClass = 'tr-disabled';
																    if(isset($bomItemsData[$item->id])){
																        $disabledClass = 'tr-active';
																    }

																    if($item->mcat_id == 1){
																        $cl = 'fab-tr';
																    }else if($item->mcat_id == 2){
																        $cl = 'sew-tr';
																    }else if($item->mcat_id == 3){
																        $cl = 'fin-tr';
																    }
																    $boms = $bomItemsData[$item->id]??[];
																@endphp
																<tr id="item-{{$item->id}}" class="{{$disabledClass}} {{$cl}}">
																    <td>
																        <input  type="hidden" class="form-control input-sm"  data-validation="required" value="{{$cat->mcat_name}}" readonly/>
																        <input  type="hidden" name="mr_material_category_mcat_id[]" value="{{$item->mcat_id}}">
																        <input  type="hidden" name="id[]" value="{{$boms->id??0}}"> 
																        <span style="font-size: 9px;"> {{$cat->mcat_name}} </span>
																    </td>
																    <td>
																        <input  type="hidden" class="form-control input-sm"  data-validation="required" value="{{$item->item_name}}" readonly/>
																        <input  type="hidden" name="mr_cat_item_id[]" value="{{$item->id}}"> 
																        {{$item->item_name}}
																    </td>
																    <td>
																        <input  type="hidden" class="form-control input-sm"  data-validation="required" value="{{$item->item_code}}" readonly/>
																        {{$item->item_code}}
																    </td>
																    <td>
																        <input  type="text" name="item_description[]" class="form-control input-sm bg_field"  placeholder="Description" value="{{$boms->item_description??''}}"/>
																    </td>
																    <td>
																    	<div class='input-group'>
																    		<select name="clr_id[]" id="" class ="form-control input-sm no-select color select2" placeholder="Select">
																    			<option value=""> -Select - </option>
																    			@foreach($colors as $color)
																    			<option value="{{ $color->clr_id}}" @if(isset($boms->clr_id) && $boms->clr_id == $color->clr_id) selected @endif>{{ $color->clr_name}} - {{ $color->clr_code}}</option>
																    			@endforeach
																    		</select>
																    		
																    		<span class='input-group-btn'><button type='button' id='add_new_color_button'  data-toggle='modal'  data-target='.newColorModal' class='btn btn-xs btn-primary add_new_color_button'>+</button></span>
																    	</div>
																    </td>
																    <td>
																        <input  type="text" name="size[]" class="form-control input-sm"  placeholder="Size/Width" value="{{$boms->size??''}}"/>
																    </td>
																    <td>
																    	<div class='input-group'>
																	    	<select name="mr_supplier_sup_id[]" id=""class="form-control input-sm no-select supplier"placeholder="Select"data-validation="required">
																    			<option value=""> -Select - </option>
																    			@if(isset($getSupplier[$item->mcat_id]))
																	    			@foreach($getSupplier[$item->mcat_id] as $sup)
																	    			@if($sup->supplier != null)
																	    			<option value="{{ $sup->supplier['sup_id'] }}"@if(isset($boms->mr_supplier_sup_id) && $boms->mr_supplier_sup_id == $sup->supplier['sup_id']) selected @endif>{{ $sup->supplier['sup_name'] }}</option>
																	    			@endif
																	    			@endforeach
																	    		@endif
																    		</select>
																    		<span class='input-group-btn'><button type='button' id='add_new_supplier_button'  data-toggle='modal' data-id="" data-cat="{{ $item->mcat_id }}" data-target='.newSupplierModal' class='btn btn-xs btn-primary'>+</button></span>
															    		</div>
																    </td>
																    <td>
																    	<div class='input-group'>
																	    	<select name="mr_article_id[]" id="" class="form-control input-sm no-select bom_article">
																    			<option value=""> - Select - </option>
																    		@if(isset($boms->mr_supplier_sup_id) &&isset($getSupArticle[$boms->mr_supplier_sup_id]))
																    		@foreach($getSupArticle[$boms->mr_supplier_sup_id] as $article)
																    			<option value="{{ $article->id}}"@if(isset($boms->mr_article_id) && $boms->mr_article_id == $article->id) selected @endif>{{ $article->art_name}}</option>
																    		@endforeach
																    		@endif
																    		</select>
																    		<span class='input-group-btn'><button type='button'  data-toggle='modal' data-target='.newArticleModal' class='btn btn-xs btn-primary'>+</button></span>
															    		</div>
																    </td>
																    <td class="comp_name">
																    @isset($boms->id)
																        {{ $boms->comp_name === null ? "N/A" : $boms->comp_name }}
																    @endisset
																    </td>
																    <td class="construction_name">
																    @isset($boms->id)
																        {{ $boms->construction_name === null ? "N/A" : $boms->construction_name }}
																    @endisset
																    </td>
																    <td>
																    	<div class='input-group'>
																	    	<select name="uom[]" id=""placeholder="Select" data-validation="required">
																    			<option value=""> - Select - </option>
																    			@if(isset($getUomItem[$item->id]))
																	    			@foreach($getUomItem[$item->id] as $uomItem)
																	    			
																	    			<option value="{{ $uomItem->uom['measurement_name']}}"@if(isset($boms->uom) && $boms->uom == $uomItem->uom['measurement_name']) selected @endif>{{ $uomItem->uom['measurement_name']}}</option>
																	    			@endforeach
																	    		@else
																	    			@foreach($getUom as $uom)
																	    			
																	    			<option value="{{ $uom->measurement_name}}"@if(isset($boms->uom) && $boms->uom == $uom->measurement_name) selected @endif>{{ $uom->measurement_name}}</option>
																	    			@endforeach
																	    		@endif
																    		</select>
																    		
															    		</div>
																    </td>
																    <td>
																        <input  data-toggle="tooltip" title="{{$cat->mcat_name}} > {{$item->item_name}} " type="text" name="consumption[]" class="form-control input-sm calc consumption tooltipped" data-validation="required" placeholder="Select" value="{{$boms->consumption??0}}" onclick="this.select()" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"/ >
																    </td>
																    <td>
																        @php
																            if(isset($boms->extra_percent)){
																                $extra = $boms->extra_percent;
																            }else{
																                $extra = 5;
																            }
																            if(isset($boms->extra_percent)){
																	            $boms->extra_qty = number_format(($boms->consumption/100)*$boms->extra_percent,2);
	                    														$boms->total_value = number_format(($boms->consumption+$boms->extra_qty),2);
	                    													}
																        @endphp
																        <input  data-toggle="tooltip" title="{{$cat->mcat_name}} > {{$item->item_name}} " type="text" name="extra_percent[]" class="form-control input-sm calc extra tooltipped"  placeholder="Extra" data-validation="required"  value="{{$extra}}" onclick="this.select()" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"/>
																    </td>
																    <td>
																        <input  type="text" class="form-control input-sm qty"  placeholder="Extra Qty" data-validation="required" readonly value="{{$boms->extra_qty??0}}"/>
																    </td>
																    <td>
																        <input  type="text" class="form-control input-sm calc total"  placeholder="Total" data-validation="required" readonly value="{{$boms->total_value??0}}"/>
																    </td>
																</tr>
																	
																@endif
																@endforeach
															@endforeach
								                        </tbody>
															   
								                    </table>
								                </div>
								            </div>
						                    <br>
							                <div class="text-right">
								            	<div class="col-sm-12">
									                <button type="button" id="bom_form_save" class="btn btn-info btn-sm">Save</button>
									                <button type="submit" id="bom_form" class="btn btn-success btn-sm">Update</button> 
									            </div>
								            </div>
						                </div><!-- /.col -->
						                
						            </div>

						            
								</div>
							</div>
				            
				            
						{!! Form::close() !!}
		            </div>
		        </div>
		    </div>
            
            <!-- /.form -->
		</div><!-- /.page-content -->
	</div>
</div>


<!-- NEW BOM  -->
<div class="modal fade" id="newBomModal" tabindex="-1" role="dialog" aria-labelledby="newBomLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">BOM Item</h4>
      </div>
      <div class="modal-body" style="padding:0 15px">
      	{!! (!empty($modalItem)?$modalItem:null) !!}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="button" id="newBomModalDone" class="btn btn-primary btn-sm">Done</button>
      </div>
    </div>
  </div>
</div>

<!-- New Color Modal -->
<div class="modal fade newColorModal" id="newColorModal" tabindex="-1" role="dialog" aria-labelledby="newColorModal">
  <div class="modal-dialog modal-xs" role="document">
    <div class="modal-content">
    <form class="modal-content form-horizontal" id="colorForm" role="form" method="POST">
      <div class="modal-header">
        <h4 class="modal-title">Color</h4>
      </div>
      <div class="modal-body">
      	<!-- Color Entry Form -->
        {{ csrf_field() }} 
            <div class="form-group">
              <label class="col-sm-3 control-label no-padding-right" for="march_color" >Main Reference <span style="color: red">&#42;</span> </label>

                <div class="col-sm-9">
                   <input type="text" id="march_color" name="march_color" placeholder="Enter Text" class="col-xs-12" data-validation="required length custom" data-validation-length="1-50"/>
                </div>
          </div>

            <div class="form-group">
              <label class="col-sm-3 control-label no-padding-right" for="march_color_code" >Second Reference  </label>
                <div class="col-sm-9">
                  <input type="text" id="march_color_code" name="march_color_code" placeholder="Enter Code" class="col-xs-12" />
                </div>
          </div>
          <!---<div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="march_description" > Description <span style="color: red">&#42;</span> </label>
                <div class="col-sm-9">
                  <input type="text" id="march_description" name="march_description" placeholder="Enter Text" class="col-xs-12" data-validation="required length custom" data-validation-length="1-128" data-validation-regexp="^([,./;:-_()%$&a-z A-Z0-9]+)$"/>
                 
            </div>
          </div>---->   
        <div id="addmoreAttach"> 
          <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right" for="march_file" > Attach File</label>
                <div class="col-sm-9">                                       
                  <input type="file" name="march_file[]" class="form-control-file col-xs-6 imgInp" data-validation="mime size" data-validation-allowing="docx,doc,pdf,jpeg,png,jpg" data-validation-max-size="1M"                                    data-validation-error-msg-size="You can not upload images larger than 512kb" data-validation-error-msg-mime="You can only upload docx, doc, pdf, jpeg, jpg or png type file">
                   <div class="form-group col-xs-6 col-sm-6">
                    <!--<img class="colorimage" src="#" alt="Color image" name="colorimagefile[]" />-->
                         <button type="button" class="btn btn-sm btn-success btn-round AddBtn">+</button>
                         <button type="button" class="btn btn-sm btn-danger btn-round RemoveBtn" style="width: 33px;">-</button> 
                   </div> 
            </div>
          </div> 
        </div>                         
      </div>
      <div class="modal-footer">
		<button type="button" class="btn btn-default btn-sm btn-round color_cancel_button" data-dismiss="modal">Cancel</button>
		<button type="button" class="btn btn-primary btn-sm btn-round color_save" data-dismiss="modal">Submit</button>
  	  </div>
      </form>
    </div>
  </div>
</div>

<!-- New Color Modal -->


<!-- NEW Supplier  -->
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

<!-- new Select Item -->
<div class="modal fade" id="select_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <!-- <button type="button" class="btn btn-danger btn-xs pull-right" data-dismiss="modal">Close</button> -->
                <h2 class="modal-title text-center" id="myModalLabel"> Items</h2>
            </div>
            <form class="form-horizontal" role="form" method="post" action="#" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-body"  style="padding:0 15px">

                      {!! (!empty($itemList)?$itemList:null) !!}

                </div>
                <div class="modal-footer">
                    <div class="col-md-8" style="padding-top: 20px;">
                        <button type="button btn-sm" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-info btn-sm" type="button" id="modal_data" data-dismiss="modal">
                            <i class="ace-icon fa fa-check bigger-110" ></i> Done
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

 
<!-- NEW ARTICLE  -->
<div class="modal fade newArticleModal" tabindex="-1" role="dialog" aria-labelledby="newArticleLabel">
  	<div class="modal-dialog modal-xs" role="document">
    	{{ Form::open(["url"=>"", "id"=>"newArticle", "class"=>"modal-content form-horizontal"]) }}
      		<div class="modal-header">
        		<h4 class="modal-title text-center">Add Article</h4>
      		</div>
	    	<div class="modal-body">
		      	<div class="row">
			      	<div class="col-xs-12">
			      		<div class="message"></div>

			            <div class="form-group">
			                <label class="col-sm-3 control-label no-padding-right" for="supplier_name"> Supplier Name<span style="color: red">&#42;</span></label>
			                <div class="col-sm-6">
			                    <input name="supplier_id" type="hidden" id="supplier_id" placeholder="Supplier id"/>
			                    <input type="text" id="supplier_name" placeholder="Supplier Name" class="col-xs-12 form-control" data-validation="required" readonly/>
			                </div>
			            </div>

			            <div class="form-group">
			                <label class="col-sm-3 control-label no-padding-right" for="article_name"> Article Name<span style="color: red">&#42;</span> </label>
			                <div class="col-sm-6">
			                    <input name="article_name" type="text" id="article_name" placeholder="Article Name" class="col-xs-12 form-control" data-validation="required"/>
			                </div>
			            </div>

			            <div class="form-group">
			              <label class="col-sm-3 control-label no-padding-right" for="art_composition" >Composition </label>
			                <div class="col-sm-6">
			                  <input type="text" id="art_composition" name="art_composition" placeholder="Enter Composition" class="col-xs-12 form-control" />
			                </div>
			            </div>

			            <div class="form-group">
			              <label class="col-sm-3 control-label no-padding-right" for="art_construction" >Construction</label>
			              <div class="col-sm-6">
			                <input type="text" id="art_construction" name="art_construction" placeholder="Enter Construction" class="col-xs-12 form-control" />
			              </div>
			            </div>
			      	</div>	
		      	</div>	
	    	</div>  
      		<div class="modal-footer">
        		<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        		<button type="submit" class="btn btn-primary btn-sm">Submit</button>
      		</div>
    	{{ Form::close() }}
  	</div>
</div>
 
<!-- NEW COMPOSITION  -->
<div class="modal fade newCompositionModal" tabindex="-1" role="dialog" aria-labelledby="newCompositionLabel">
  <div class="modal-dialog" role="document">
    {{ Form::open(["url"=>"", "id"=>"newComposition", "class"=>"modal-content form-horizontal"]) }}
      <div class="modal-header">
        <h4 class="modal-title">Add Composition</h4>
      </div>
      <div class="modal-body row"> 
      		<div class="message"></div>
            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="supplier_name"> Supplier Name </label>
                <div class="col-sm-8">
                    <input name="supplier_id" type="hidden" id="supplier_id" placeholder="Supplier id"/>
                    <input type="text" id="supplier_name" placeholder="Supplier Name" class="col-xs-10 col-sm-5" data-validation="required" readonly/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right"> Composition Name </label>
                <div class="col-sm-8">
                    <input name="composition_name" type="text" id="composition_name" placeholder="Composition Name" class="col-xs-10 col-sm-5" data-validation="required"/>
                </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
      </div>
    {{ Form::close() }}
  </div>
</div>
 
<!-- NEW  CONSTRUCTION -->
<div class="modal fade newConstructionModal" tabindex="-1" role="dialog" aria-labelledby="newConstructionLabel">
  <div class="modal-dialog" role="document">
    {{ Form::open(["url"=>"", "id"=>"newConstruction", "class"=>"modal-content form-horizontal"]) }}
      <div class="modal-header">
        <h4 class="modal-title">Add Construction</h4>
      </div>
      <div class="modal-body row"> 
      		<div class="message"></div>
            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" for="supplier_name"> Supplier Name </label>
                <div class="col-sm-8">
                    <input name="supplier_id" type="hidden" id="supplier_id" placeholder="Supplier id"/>
                    <input type="text" id="supplier_name" placeholder="Supplier Name" class="col-xs-10 col-sm-5" data-validation="required" readonly/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label no-padding-right" construction> Construction Name </label>
                <div class="col-sm-8">
                    <input name="construction_name" type="text" id="construction_name" placeholder="Construction Name" class="col-xs-10 col-sm-5" data-validation="required"/>
                </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
      </div>
    {{ Form::close() }}
  </div>
</div>
@push('js')
<script type="text/javascript">

$(document).ready(function(){
	$('select').select2();
	$('.tr-disabled').find('*').attr('disabled', true);
	$('input').tooltip({
	    placement: "left",
	    trigger: "focus"
	});
	

	//Color Save Section--------
	var data = $("#addmoreAttach").html();
    $('body').on('click', '.AddBtn', function(){
        $("#addmoreAttach").append(data);
    });

    $('body').on('click', '.RemoveBtn', function(){
        $(this).parent().parent().parent().remove();
    });



    $('body').on('click', '.color_cancel_button', function(){
    	$('#colorForm').trigger("reset");
    });

    var color_checked_par="";
    $("body").on("click", ".add_new_color_button", function(e) {
    	color_checked_par = $(this).parent().parent().find('.color');
    });

    $('body').on('click', '.color_save', function(){
    	var formData = $('#colorForm').serialize();
    	// console.log(formData);
    	$.ajax({
			url     : "{{ url('merch/style_bom/newcolorstore') }}",
			data    : formData,
			type    : 'POST',
			success : function(res) {
				$('#colorForm').trigger("reset");
				// console.log(res);
				var append_color = "<option value=\""+res.color[0].clr_id+"\">"+res.color[0].clr_name+" - "+res.color[0].clr_code+"</option>"
				$(".color").append(append_color);
				
				//making the selection

				var color_dd = "<option value=\"\">Select</option>";
				for(var i = 0; i < res.colors.length; i++ ){
					color_dd +="<option value=\""+res.colors[i].clr_id+"\"";
					if(res.last_id == res.colors[i].clr_id){
						color_dd += "selected=\"selected\"";
					}
					color_dd += ">"+res.colors[i].clr_name+" - "+res.color[0].clr_code+"</option>";
				}
				color_checked_par.html(color_dd);
				color_checked_par="";

				var message = '<div class="alert alert-success alert-dismissible" role="alert">'+
				  '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
					'Color Saved Successfully.'+
				'</div>';
				$('.msg-wraper').append(message);

			},
			error : function(xhr) {
					// console.log(xhr);
					var message = '<div class="alert alert-danger alert-dismissible" role="alert">'+
					  '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
						'Something Went Wrong! Please Try Again'+
					'</div>';
					$('.msg-wraper').append(message);
			}
    	});
    });
    //Color save end---------

	//while add supplier category item will checked automatically- rkb
	$("body").on("click", "#add_new_supplier_button", function(e) {
		var cat_id = $(this).data('cat');
		var supId = $(this).data('id');
		$('#supllier-store').val(supId);
		$('#supForm')
			    .find("textarea,select")
			       .val('')
			       .end()
		       	.find("#sup_name").val('');

       	$("#cnt_id").select2("val", "");

       	$("input[name='item_id[]']").each(function() {
       		if($(this).val()==cat_id){
       			$(this).prop("checked", "checked");
       		}else{
       			$(this).prop("checked", "");
       		}
       	});

       	$("#cnt_id").select2({
              dropdownParent: $(".newSupplierModal")
      	});
	});		

	//suppliers save and apeednd into selected category automatically- rkb
	$("body").on("click", ".supplier_save", function(e) {	

		var formData = $('#supForm').serialize();
		$.ajax({
			url     : "{{ url('merch/style_bom/ajax_save_supplier') }}",
			data    : formData,
			type    : 'POST',
			success : function(res) {
				// if(data.length > 0){				
				var r = JSON.parse(res);
				
				var supplier_select = '<option value="'+r.id+'">'+r.sup_name+'</option>';
				$("input[name='item_id[]']:checked").each(function() {
				    if($(this).val()==1){
				    	$('.fab-sup').append(supplier_select);
				    	console.log(supplier_select);
				    }else if($(this).val()==2){
				    	$('.sew-sup').append(supplier_select);
				    }else if($(this).val()==3){
				    	$('.fin-sup').append(supplier_select);
				    }
				});
				var supid = $('#supllier-store').val()
			    $('#'+supid).val(r.id);
				var message = '<div class="alert alert-success alert-dismissible" role="alert">'+
				  '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
					'Supplier Saved Successfully.'+
				'</div>';
				$('.msg-wraper').append(message);

				$('.newSupplierModal').modal('hide');
				$("input[name='item_id[]']").prop("checked", "")
			// }
			},
			error   : function(xhr) {
				console.log(xhr)
				var message = '<div class="alert alert-danger alert-dismissible" role="alert">'+
				  '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
					'Something Went Wrong! Please Try Again'+
				'</div>';
				$('.wraper').append(message);
			}
		});
	});
	


	var modal = $("#newBomModal");
	$("body").on("click", "#newBomModalDone", function(e) {
		modal.modal('hide');
        $.each($('input[type=checkbox]'), function(){            
            var item = $(this).val();
            if($(this).is(':checked')){
            	$('#item-'+item).addClass('tr-active');
            	$('#item-'+item).removeClass('tr-disabled');
            	$('#item-'+item).find('*').attr('disabled', false);
            }else{
            	$('#item-'+item).addClass('tr-disabled');
            	$('#item-'+item).removeClass('tr-active');
            	$('#item-'+item).find('*').attr('disabled', true);
            }
        });
        
    });


	function arrDiff(arr1, arr2) {
	    var second = new Set(arr2);
	    return arr1.filter(x => !second.has(x));
	}

	/*
	* --------CALCULATE TOTAL---------
	*/
	$("body").on("keyup", ".calc", function(){
		var consumption = $(this).parent().parent().find(".consumption").val();
		var extra = $(this).parent().parent().find(".extra").val();
		var qty   = parseFloat(((parseFloat(consumption)/100)*parseFloat(extra))).toFixed(2);
		var total = (parseFloat(qty)+parseFloat(consumption)).toFixed(2);
		$(this).parent().parent().find(".qty").val(qty);
		$(this).parent().parent().find(".total").val(total);
	});




	/*
	* GET ARTICLE, COMPOSITION AND CONSTRUCTION  
	* -------------------------------------------
	*/  
	$("body").on("change", ".supplier", function() {
		var that = $(this);
		//----------added on 03-10-2019----------------
		that.parent().parent().next().next().text(''); 
		that.parent().parent().next().next().next().text('');
		//--------------- on 03-10-2019----------------

		// load article
		$.ajax({
			url: "{{ url('merch/style_bom/get_article_by_supplier') }}",
			data: {
				"supplier_id" : that.val(), 
				"name"        : "mr_article_id[]", 
				"selected"    : "",  
				"option"      : {
				"class"           : "form-control input-sm no-select bom_article",
					"placeholder"     : "Select" 
				} 
			},
			success: function(data) {  
				that.parent().parent().next().html(data);
				$('select').select2(); 
			},
			error: function(xhr) {
				console.log(xhr)
			}
		});
 
		
	});

	//Construction and consumption on change article
	$("body").on("change", ".bom_article", function() {
		var that= $(this);

		//----------added on 03-10-2019----------------
			if($(this).val()==''){
				that.parent().parent().next().text(''); 
				that.parent().parent().next().next().text('');
			}else{
		//----------------on 03-10-2019----------------

			$.ajax({
				url: "{{ url('merch/style_bom/get_composition_by_article') }}",
				data: {
					"article_id" : $(this).val()
				},
				success: function(data) {
					that.parent().parent().next().next().text(data.cons); 
					that.parent().parent().next().text(data.comp); 
				},
				error: function(xhr) {
					console.log(xhr)
				}
			});
		}  //else-end
	});


	/*
	* NEW ARTICLE 
	* -------------------------------------------
	*/ 
	$('.newArticleModal').on('show.bs.modal', function (e) {

		var modal = $(this);
		var button = $(e.relatedTarget);
		var supplier_id = button.parent().parent().parent().parent().find(".supplier option:selected").val();
		var supplier_name = button.parent().parent().parent().parent().find(".supplier option:selected").text();
		modal.find("#supplier_id").val(supplier_id);
		modal.find("#supplier_name").val(supplier_name);

		// new article
		$("#newArticle").on("submit", function(e) {
			e.preventDefault();
			var that = $(this);
			$.ajax({
				url: "{{ url('merch/style_bom/new_article') }}",
				dataType: "json",
				data: {
					"supplier_id"  : that.find("#supplier_id").val(),
					"article_name" : that.find("#article_name").val(),
					"art_composition" : that.find("#art_composition").val(),
					"art_construction" : that.find("#art_construction").val(),
					"name"         : "mr_article_id[]",
					"selected"     : "",
					"option"       : {
						"class" : "form-control input-sm no-select article bom_article",
						"placeholder"     : "Select"
					}
				},
				success: function(data) {
					if (data.status)
					{
						button.parent().parent().parent().next().html(data.comp);
						button.parent().parent().parent().next().next().html(data.cons);
						button.parent().parent().parent().html(data.result);
						$('select').select2();
						modal.find("#supplier_id").val("");
						modal.find("#supplier_name").val("");
						modal.find("#article_name").val("");
						modal.find(".message").html("");
						$('.newArticleModal').modal('hide');
						that.unbind('submit');
					}
					else
					{
						modal.find(".message").html("<div class='alert alert-danger'>"+data.message+"</div>");
					}

				},
				error: function(xhr) {
					console.log(xhr)
				}
			});
		});
	});


	/*
	* NEW COMPOSITION 
	* -------------------------------------------
	*/ 
	$('.newCompositionModal').on('show.bs.modal', function (e) {
		var modal = $(this);
		var button = $(e.relatedTarget);
		var supplier_id = button.parent().parent().parent().parent().find(".supplier option:selected").val();
		var supplier_name = button.parent().parent().parent().parent().find(".supplier option:selected").text();
		modal.find("#supplier_id").val(supplier_id);
		modal.find("#supplier_name").val(supplier_name); 

		// new article
		$("#newComposition").on("submit", function(e) {
			e.preventDefault();
			var that = $(this);
			$.ajax({
				url: "{{ url('merch/style_bom/new_composition') }}",
				dataType: "json",
				data: {
					"supplier_id"  : that.find("#supplier_id").val(), 
					"composition_name" : that.find("#composition_name").val(), 
					"name"         : "mr_composition_id[]", 
					"selected"     : "",  
					"option"       : {
						"class" : "form-control input-sm no-select article",
						"placeholder"     : "Select" 
					} 
				},
				success: function(data) {  
					if (data.status)
					{
						button.parent().parent().parent().html(data.result);
						modal.find("#supplier_id").val("");
						modal.find("#supplier_name").val(""); 
						modal.find("#composition_name").val(""); 
						modal.find(".message").html(""); 
						$('.newCompositionModal').modal('hide');
						that.unbind('submit');
					}
					else
					{
						modal.find(".message").html("<div class='alert alert-danger'>"+data.message+"</div>");
					}
				},
				error: function(xhr) {
					console.log(xhr)
				}
			}); 
		});  
	}); 


	/*
	* NEW CONSTRUCTION 
	* -------------------------------------------
	*/ 
	$('.newConstructionModal').on('show.bs.modal', function (e) {
		var modal = $(this);
		var button = $(e.relatedTarget);
		var supplier_id = button.parent().parent().parent().parent().find(".supplier option:selected").val();
		var supplier_name = button.parent().parent().parent().parent().find(".supplier option:selected").text();
		modal.find("#supplier_id").val(supplier_id);
		modal.find("#supplier_name").val(supplier_name); 

		// new article
		$("#newConstruction").on("submit", function(e) {
			e.preventDefault();
			var that = $(this);
			$.ajax({
				url: "{{ url('merch/style_bom/new_construction') }}",
				dataType: "json",
				data: {
					"supplier_id"  : that.find("#supplier_id").val(), 
					"construction_name" : that.find("#construction_name").val(), 
					"name"         : "mr_construction_id[]", 
					"selected"     : "",  
					"option"       : {
						"class" : "form-control input-sm no-select article",
						"placeholder"     : "Select" 
					} 
				},
				success: function(data) {  
					if (data.status)
					{
						button.parent().parent().parent().html(data.result);
						modal.find("#supplier_id").val("");
						modal.find("#supplier_name").val(""); 
						modal.find("#construction_name").val(""); 
						modal.find(".message").html(""); 
						$('.newConstructionModal').modal('hide');
						that.unbind('submit');
					}
					else
					{
						modal.find(".message").html("<div class='alert alert-danger'>"+data.message+"</div>");
					}
				},
				error: function(xhr) {
					console.log(xhr)
				}
			}); 
		});  
	}); 

	/*
	* COLOR LIST WITH BACKGROUND COLOR
	* -------------------------------------------
	*/   
	$("body").on("click", "select.color", function(){
		$("body select.color option").each(function(i, v) {
        	$(this).css('background-color', $(this).text());
		}); 
		$(this).css('background-color', $(this).find('option:selected').text());
	});


	//prevent null

	$('.consumption').on('keyup',function(){

          if($(this).val()<0||$(this).val()==''||$(this).val()==null){
				$(this).val(0);
	        } 

	});

	$('.extra').on('keyup',function(){
		// console.log($(this).val());
          if($(this).val()<0||$(this).val()==''||$(this).val()==null){
				$(this).val(0);
	        } 

	});
	/*
	* CONSUMPTION & EXTRA VALIDATION FOR 0/NULL
	* -------------------------------------------
	*/

	 $('body').on('click', '#bom_form', function(e) {    

	  // CONSUMPTION    
	 	var alert_check_consumption= false;
		$('.tr-active td .consumption').each(function(){
			var consumption = parseFloat($(this).val());
			 // console.log(consumption);
			if(consumption==0||consumption==null){
				alert_check_consumption= true;
				return;
	        } 
		});	 	
		if(alert_check_consumption == true){
		  	alert("Invalid consumption");                
          
		}

	  // EXTRA    
	 	var alert_check_extra= false;
		$('.tr-active td .extra').each(function(){
			var extra = parseFloat($(this).val());
		
			if(extra==0||extra==null){
				alert_check_extra= true;
				return;
	        } 
		});	 	
		if(alert_check_extra == true){
		  	alert("Invalid extra");                
          
		}	
       // Form submit prevent 
	   if(alert_check_extra == true|| alert_check_consumption == true){
		                  
          	e.preventDefault();
		}	

    });

	 

});
</script>


<!-- add/remove from modal -->

<script type="text/javascript">

    $(document).ready(function(){

        $('#dataTables').DataTable();
        var data = $('.AddBtn').parent().parent().parent().parent().html();
        $('body').on('click', '.AddBtn', function(){
            $('.addRemove').append(data);
        });

        $('body').on('click', '.RemoveBtn', function(){
            $(this).parent().parent().parent().remove();
        });


        // Modal Check Box
                $('#select_item').on('hidden.bs.modal', function (e) {
            var data= '';
						data += '<table class="table" style="margin-top: 30px;">';
						data += '<thead>';
						data += '<tr>';
						data += '<td colspan="3" class="text-center">Item Name</td>';
						data += '</tr>';
						data += '</thead>';
						data += '<tbody>';
            $('.checkbox-input').each(function(i, v){
                if ($(this).is(":checked"))
                {
                    var id= $(this).val();
                    var item_name= $(this).next().text();

							      data += '<tr>';
							      data += '<td style="border-bottom: 1px solid lightgray;" class="text-center" colspan="3"><strong>'+item_name+'</strong></td>';
							      data+= '<input type="hidden" name="item_id[]" value="'+id+'"><input type="hidden" name="items[]" id="items[]" placeholder="Food" value="'+item_name+'" class="col-xs-12" readonly/>';
							      data += '</tr>';

                    //var item_code= $(this).parent().next().text();
                }
            });
						data += '</tbody>';
						data += '</table>';
            // console.log(data);
            $("#Item_description").html(data);
        });

    });

    $(document).ready(function() {
    	$('input').tooltip({
		     placement: "left",
		     trigger: "focus"
		});
    });


function printMe(image, elem_1, elem_2, style_no){   
	$('button').attr('hidden', 'hidden');
	$('select').attr('hidden', 'hidden');

	var style_sheet = '' +
        '<style type="text/css">' +
        '@page {'+
		    'size: A4 landscape;'+
		'}'+
		'input, select {'+
			'border: none;'+
			'font-size: 10px;'+
			'width: 80px;'+
		'}'+
        'img{ '+
			'width: 120px;'+
			'height: 160px;'+
		'}'+
		'table{ '+
			'width: 100%;'+
			'border-collapse: collapse;'+
			'font-size: 10px;'+
			// 'display: block'+
		'}'+
        'table th {' +
        'border:1px solid #808080;' +
        'padding:0.5em;' +
        'padding-top: 15px;' +
        'padding-bottom: 15px;' +
        'margin:0px;'+
        '}' +
        'table td {' +
        'border:1px solid #808080;' +
        'padding:0.5em;' +
        'padding-top: 15px;' +
        'padding-bottom: 15px;' +
        'margin:0px;'+
        '}' +
        'h2 {' +
        	'text-align:center; page-break-before: always; border: 1px solid lightgrey; border-radius:8px; padding:0.2em; margin-bottom: 30px;'
        '}' +
        '</style>';

    var mywindow = window.open('', 'PRINT', 'height=900,width=1200');
    mywindow.document.write('<html><head><title>' + 'Style BOM'  + '</title>');
    mywindow.document.write('</head><body>');


   	mywindow.document.write('<h1 style=" margin-top:30px; border: 1px solid green; border-radius:8px; padding:0.4em;">&nbsp Style BOM <small style="color: red;">('+document.getElementById(style_no).innerHTML+')</small> </h1><br>');
    
    mywindow.document.write('<div style="width: 100%;"><div style="text-align:center;">');
    mywindow.document.write(document.getElementById(image).innerHTML+' </div></div>');
   	
   	mywindow.document.write('<h3 style="margin-top: 100px;">Basic Information </h3><div style="width:70%">');
    mywindow.document.write(document.getElementById(elem_1).innerHTML+'</div>');

	mywindow.document.write('<h2>BOM Information <small style="color: red;">( Style: '+document.getElementById(style_no).innerHTML+')</small> </h2>');
    mywindow.document.write(document.getElementById(elem_2).innerHTML);

    mywindow.document.write('</body>'+style_sheet+'</html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    $('button').removeAttr('hidden');
    $('select').removeAttr('hidden');
    mywindow.close();


    // var myWindow=window.open('','','width=800,height=800');
    // myWindow.document.write(document.getElementById(divName).innerHTML);
    // myWindow.document.close();
    // myWindow.focus();
    // myWindow.print();
    // myWindow.close();
}

 //    function printMe(divName)
	// {   
 //    var myWindow=window.open('','','width=800,height=800');
 //    myWindow.document.write(document.getElementById(divName).innerHTML);
 //    myWindow.document.close();
 //    myWindow.focus();
 //    myWindow.print();
 //    myWindow.close();
	// }




    //added ---------------------on 03-10-2019----------------------------------------------------------------------

          $('.newArticleModal').on('hidden.bs.modal', function (e) {
			  $(this)
			    .find("input,textarea,select")
			       .val('')
			       .end()
		     	.find("input[type=checkbox]")
		       		.prop("checked", "")
		       		.end();
		       	$('.message').html('');
			});
          //------------------------------------on 03-10-2019------------------------------------------------------

        $('select').select2();

</script>


<script type="text/javascript">
$(document).ready(function(){
	$('body').on('click', '#bom_form_save', function(e) {    

		  var checkLength = $("#bomItemTable tbody tr").length;

		  if(checkLength === 0){
		  	alert("Please select item");
		  	e.preventDefault();
		  	return false;
		  }

		  else{

		  	var mr_material_category_mcat_id = [];
		  	$(".tr-active td input[name='mr_material_category_mcat_id[]']").each(function( i,v ) {
              mr_material_category_mcat_id.push($(this).val());
              
             });

		  	var mr_cat_item_id = [];
		  	$(".tr-active td input[name='mr_cat_item_id[]']").each(function( i,v ) {
              mr_cat_item_id.push($(this).val());
              
             });

		  	var item_description = [];
		  	$(".tr-active td input[name='item_description[]']").each(function( i,v ) {
              item_description.push($(this).val());
              
             });
		  	var clr_id = [];
		  	$(".tr-active td select[name='clr_id[]']").each(function( i,v ) {
              clr_id.push($(this).val());
              
             });

		  	var size = [];
		  	$(".tr-active td input[name='size[]']").each(function( i,v ) {
              size.push($(this).val());
              
             });
		  	var mr_supplier_sup_id = [];
		  	$(".tr-active td select[name='mr_supplier_sup_id[]']").each(function( i,v ) {
              mr_supplier_sup_id.push($(this).val());
              
             });

		  	var mr_article_id = [];
		  	$(".tr-active td select[name='mr_article_id[]']").each(function( i,v ) {
              mr_article_id.push($(this).val());
              
             });
		  	var uom = [];
		  	$(".tr-active td select[name='uom[]']").each(function( i,v ) {
              uom.push($(this).val());
              
             });
		  	var consumption = [];
		  	$(".tr-active td input[name='consumption[]']").each(function( i,v ) {
              consumption.push($(this).val());
              
             });
		  	var extra_percent = [];
		  	$(".tr-active td input[name='extra_percent[]']").each(function( i,v ) {
              extra_percent.push($(this).val());
              
             });
		  	var bom_costing_pre_id = [];
		  	$("input[name='bom_costing_pre_id[]']").each(function( i,v ) {
              bom_costing_pre_id.push($(this).val());
              
             });
		  	var id = [];
		  	$(".tr-active td input[name='id[]']").each(function( i,v ) {
              id.push($(this).val());
              
             });

		  	var mr_style_stl_id = $('#mr_style_stl_id').val();
		  
		  	// console.log(mr_material_category_mcat_id);

		  	$.ajax({
		  		type:'POST',
				url: "{{ url('merch/style_bom/ajax_update_bom_info') }}",
				dataType: "json",
				data: {
					_token: "{{ csrf_token() }}",
					bom_costing_pre_id : bom_costing_pre_id,
					mr_style_stl_id : mr_style_stl_id,
					id : id,
					mr_material_category_mcat_id : mr_material_category_mcat_id,
					mr_cat_item_id : mr_cat_item_id,
					item_description : item_description,
					clr_id : clr_id,
					size : size,
					mr_supplier_sup_id : mr_supplier_sup_id,
					mr_article_id : mr_article_id,
					uom : uom,
					consumption : consumption,
					extra_percent : extra_percent,
					style_bom_id : $("input[name='style_bom_id']").val()

				},
				success: function(data) {
					console.log(data);
					if(data=='true'){
					// document.location="merch/style_bom/"+mr_style_stl_id+"/edit";
					window.history.pushState('','',"{{url('merch/style_bom')}}/"+mr_style_stl_id+"/edit");
					window.location.reload();
					}
					else{
						alert('Save Unsuccessful');
					}
				},
				error: function(xhr) {
					console.log(xhr)
				}
			});
		  }
	});
});
</script>


@endpush
@endsection
